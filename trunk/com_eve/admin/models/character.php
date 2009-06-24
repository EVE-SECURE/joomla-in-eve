<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Core
 * @copyright	Copyright (C) 2008 Pavol Kovalik. All rights reserved.
 * @license		GNU/GPL, see http://www.gnu.org/licenses/gpl.html
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

class EveModelCharacter extends EveModel {
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function _populateState()
	{
		$app		= &JFactory::getApplication('administrator');
		$params		= &JComponentHelper::getParams('com_eve');

		// Load the User state.
		if (JRequest::getWord('layout') === 'edit') {
			$characterID = (int) $app->getUserState('com_eve.edit.character.characterID');
			$this->setState('character.characterID', $characterID);
		} else {
			$characterID = (int) JRequest::getInt('characterID');
			$this->setState('character.characterID', $characterID);
		}

		// Load the parameters.
		$this->setState('params', $params);
	}



	/**
	 * Method to get a member item.
	 *
	 * @access	public
	 * @param	integer	The id of the member to get.
	 * @return	mixed	User data object on success, false on failure.
	 * @since	1.0
	 */
	public function &getItem($characterID = null)
	{
		// Initialize variables.
		$characterID	= (!empty($characterID)) ? $characterID : (int) $this->getState('character.characterID');
		$false		= false;

		// Attempt to load the row.
		$return = $this->getCharacter($characterID);
		
		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return $false;
		}

		// Convert the params field to an array.
		return $return;
	}


	public function save($data)
	{
		$characterID	= (int) $this->getState('character.characterID');
		$isNew		= true;

		// Get a character row instance.
		$table = &$this->getItem($characterID);
		
		// Bind the data
		if (!$table->bind($data)) {
			$this->setError(JText::sprintf('JTable_Error_Bind_failed', $table->getError()));
			return false;
		}
		
		// Prepare the row for saving
		$this->_prepareTable($table);

		// Check the data
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// Store the data
		if (!$table->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return $table->characterID;
	}

	protected function _prepareTable(&$character)
	{
		$app = JFactory::getApplication();
		if ($character->apiStatus == 'Unknown') {
			JPluginHelper::importPlugin('eveapi');
			$dispatcher =& JDispatcher::getInstance();
			$ale = $this->getAleEVEOnline();
			try {
				$ale->setCredentials($character->characterID, $character->apiKey);
				$xml = $ale->character->Characters();
				$dispatcher->trigger('characterCharacters', 
					array($xml, $ale->isFromCache(), array('characterID' => $character->characterID)));
				
				$charRow = reset($xml->result->characters->getIterator());
				if ($charRow !== false) {
					$ale->setCharacterID($charRow->characterID);
					$xml = $ale->char->CharacterBalance();
					$dispatcher->trigger('charCharacterBalance', 
						array($xml, $ale->isFromCache(), array('characterID' => $charRow->characterID)));
				}
				$character->apiStatus = 'Full';
				$app->enqueueMessage(JText::_('API key offers full access'));
			}
			catch (AleExceptionEVEAuthentication $e) {
				$this->updateApiStatus($character, $e->getCode());
				switch ($character->apiStatus) {
					case 'Limited':
						JError::raiseNotice(0, JText::_('API key offers limited access'));
						break;
					case 'Invalid':
						JError::raiseWarning(0, JText::_('API key is invalid'));
						break;
					case 'Inactive':
						JError::raiseWarning(0, JText::_('Character is inactive'));
						break;
				}
			}
			catch (RuntimeException $e) {
				JError::raiseWarning($e->getCode(), $e->getMessage());
			}
			catch (Exception $e) {
				JError::raiseError($e->getCode(), $e->getMessage());
			}
			$dispatcher->trigger('onRegisterCharacter', array($character->characterID, $character->apiStatus));
			
		}	
	}
	
	public function validate($data = null)
	{
		if (!is_numeric($data['characterID'])) {
			$this->setError(JText::_('Invalid characterID'));
			return false;
		}
		return $data;
	}
	
	/**
	 * Method to checkin a row.
	 *
	 * @param	integer	$id		The numeric id of a row
	 * @return	boolean	True on success/false on failure
	 * @since	1.6
	 */
	public function checkin($characterID = null)
	{
		// Initialize variables.
		$user		= &JFactory::getUser();
		$juserId	= (int) $user->get('id');
		$characterID	= (int) $characterID;

		if ($characterID === 0) {
			$characterID = $this->getState('character.characterID');
		}

		if (empty($characterID)) {
			return true;
		}

		// Get a charactersTablecharacter instance.
		$table = &$this->getCharacter();

		// Attempt to check-in the row.
		$return = $table->checkin($characterID);
		// Check for a database error.
		if ($return === false) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to check-out a character for editing.
	 *
	 * @param	int		$characterID	The numeric id of the character to check-out.
	 * @return	bool	False on failure or error, success otherwise.
	 * @since	1.6
	 */
	public function checkout($characterID)
	{
		// Initialize variables.
		$user		= &JFactory::getUser();
		$juserId	= (int) $user->get('id');
		$characterID	= (int) $characterID;

		// Check for a new character id.
		if ($characterID === -1) {
			return true;
		}

		$table = &$this->getCharacter();

		// Attempt to check-out the row.
		$return = $table->checkout($juserId, $characterID);

		// Check for a database error.
		if ($return === false) {
			$this->setError($table->getError());
			return false;
		}

		// Check if the row is checked-out by someone else.
		if ($return === null) {
			$this->setError(JText::_('JCommon_Item_is_checked_out'));
			return false;
		}

		return true;
	}
	
	/**
	 * Tests if character is checked out
	 *
	 * @access	public
	 * @param	int	A user id
	 * @return	boolean	True if checked out
	 * @since	1.5
	 */
	public function isCheckedOut($juserId = 0)
	{
		if ($juserId === 0) {
			$user		= &JFactory::getUser();
			$juserId	= (int) $user->get('id');
		}

		$characterID = (int) $this->getState('character.characterID');

		if (empty($characterID)) {
			return true;
		}

		$table = &$this->getCharacter();

		$return = $table->load($characterID);

		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return false;
		}

		return $table->isCheckedOut($juserId);
	}
	

	/**
	 * Method to delete characters from the database.
	 *
	 * @param	integer	$cid	An array of	numeric ids of the rows.
	 * @return	boolean	True on success/false on failure.
	 */
	public function delete($cid)
	{
		// Get a character row instance
		$table = $this->getCharacter();

		for ($i = 0, $c = count($cid); $i < $c; $i++) {
			// Load the row.
			$return = $table->load($cid[$i]);

			// Check for an error.
			if ($return === false) {
				$this->setError($table->getError());
				return false;
			}

			// Delete the row.
			$return = $table->delete();

			// Check for an error.
			if ($return === false) {
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}
	
	/**
	 * Get TableCharacter
	 *
	 * @param int $id
	 * @return TableCharacter
	 */
	function getCharacter($characterID = null) {
		return $this->getInstance('Character', $characterID);
	}
	
	function apiGetCharacterSheet($cid) {
		global $mainframe;
		JArrayHelper::toInteger($cid);
		
		if (!count($cid)) {
			JError::raiseWarning(500, JText::_('NO CHARACTER SELECTED'));
			return false;
		}
		
		JPluginHelper::importPlugin('eveapi');
		$dispatcher =& JDispatcher::getInstance();
		
		$count = 0;
		$ale = $this->getAleEVEOnline();
		foreach ($cid as $characterID) {
			try {
				$character  = $this->getCharacter($characterID);
				$account = $this->getInstance('Account', $character->userID);
				if (!$account->apiKey) {
					continue;
				}
				$ale->setCredentials($account->userID, $account->apiKey, $character->characterID);
				$xml = $ale->char->CharacterSheet();
				$dispatcher->trigger('charCharacterSheet', 
					array($xml, $ale->isFromCache(), array('characterID'=>$character->characterID)));
				$count += 1;
			}
			catch (AleExceptionEVEAuthentication $e) {
				$this->updateApiStatus($account, $e->getCode(), true);
				JError::raiseWarning($e->getCode(), $e->getMessage());
			}
			catch (RuntimeException $e) {
				JError::raiseWarning($e->getCode(), $e->getMessage());
			}
			catch (Exception $e) {
				JError::raiseError($e->getCode(), $e->getMessage());
			}
		}
		if ($count == 1) {
			$mainframe->enqueueMessage(JText::_('CHARACTER SHEET SUCCESSFULLY IMPORTED'));
		}
		if ($count > 1) {
			$mainframe->enqueueMessage(JText::sprintf('%s CHARACTER SHEETS SUCCESSFULLY IMPORTED', $count));
		}
	}
	
	function apiGetCorporationSheet($cid) {
		global $mainframe;
		JArrayHelper::toInteger($cid);
		
		if (!count($cid)) {
			JError::raiseWarning(500, JText::_('NO CHARACTER SELECTED'));
			return false;
		}
		
		JPluginHelper::importPlugin('eveapi');
		$dispatcher =& JDispatcher::getInstance();
		
		$count = 0;
		$ale = $this->getAleEVEOnline();
		$finishedCorps = array();
		foreach ($cid as $characterID) {
			try {
				$character = $this->getCharacter($characterID);
				if (in_array($character->corporationID, $finishedCorps)) {
					continue;
				}
				$account = $this->getInstance('Account', $character->userID);
				$ale->setCredentials($account->userID, $account->apiKey, $character->characterID);
				$xml = $ale->corp->CorporationSheet();
				$dispatcher->trigger('corpCorporationSheet', 
					array($xml, $ale->isFromCache(), array('characterID'=>$character->characterID)));
				$finishedCorps[] = $character->corporationID;
				$count += 1;
			}
			catch (AleExceptionEVEAuthentication $e) {
				$this->updateApiStatus($account, $e->getCode(), true);
				JError::raiseWarning($e->getCode(), $e->getMessage());
			}
			catch (RuntimeException $e) {
				JError::raiseWarning($e->getCode(), $e->getMessage());
			}
			catch (Exception $e) {
				JError::raiseError($e->getCode(), $e->getMessage());
			}
		}
		if ($count == 1) {
			$mainframe->enqueueMessage(JText::_('CORPORATION SHEET SUCCESSFULLY IMPORTED'));
		}
		if ($count > 1) {
			$mainframe->enqueueMessage(JText::sprintf('%s CORPORATION SHEETS SUCCESSFULLY IMPORTED', $count));
		}
	}
		
}
