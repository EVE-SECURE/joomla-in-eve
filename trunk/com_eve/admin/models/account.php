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

class EveModelAccount extends EveModel {
	

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
			$userID = (int) $app->getUserState('com_eve.edit.account.userID');
			$this->setState('account.userID', $userID);
		} else {
			$userID = (int) JRequest::getInt('userID');
			$this->setState('account.userID', $userID);
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
	public function &getItem($userID = null)
	{
		// Initialize variables.
		$userID	= (!empty($userID)) ? $userID : (int) $this->getState('account.userID');
		$false		= false;

		// Attempt to load the row.
		$return = $this->getAccount($userID);
		
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
		$userID	= (int) $this->getState('account.userID');
		$isNew		= true;

		// Get a account row instance.
		$table = &$this->getItem($userID);
		
		$apiKeyPast = $table->apiKey;
		// Bind the data
		if (!$table->bind($data)) {
			$this->setError(JText::sprintf('JTable_Error_Bind_failed', $table->getError()));
			return false;
		}
		
		//compare apiKey with one stored in database
		$apiKeyNow = $table->apiKey;
		if ($apiKeyPast != $apiKeyNow) {
			$table->apiStatus = 'Unknown';
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

		return $table->userID;
	}

	protected function _prepareTable(&$account)
	{
		$app = JFactory::getApplication();
		if ($account->apiStatus == 'Unknown') {
			JPluginHelper::importPlugin('eveapi');
			$dispatcher =& JDispatcher::getInstance();
			$ale = $this->getAleEVEOnline();
			try {
				$ale->setCredentials($account->userID, $account->apiKey);
				$xml = $ale->account->Characters();
				$dispatcher->trigger('accountCharacters', 
					array($xml, $ale->isFromCache(), array('userID' => $account->userID)));
				
				$charRow = reset($xml->result->characters->getIterator());
				if ($charRow !== false) {
					$ale->setCharacterID($charRow->characterID);
					$xml = $ale->char->AccountBalance();
					$dispatcher->trigger('charAccountBalance', 
						array($xml, $ale->isFromCache(), array('characterID' => $charRow->characterID)));
				}
				$account->apiStatus = 'Full';
				$app->enqueueMessage(JText::_('API key offers full access'));
			}
			catch (AleExceptionEVEAuthentication $e) {
				EveHelper::updateApiStatus($account, $e->getCode());
				switch ($account->apiStatus) {
					case 'Limited':
						JError::raiseNotice(0, JText::_('API key offers limited access'));
						break;
					case 'Invalid':
						JError::raiseWarning(0, JText::_('API key is invalid'));
						break;
					case 'Inactive':
						JError::raiseWarning(0, JText::_('Account is inactive'));
						break;
				}
			}
			catch (RuntimeException $e) {
				JError::raiseWarning($e->getCode(), $e->getMessage());
			}
			catch (Exception $e) {
				JError::raiseError($e->getCode(), $e->getMessage());
			}
			$dispatcher->trigger('onRegisterAccount', array($account->userID, $account->apiStatus));
			
		}	
	}
	
	public function validate($data = null)
	{
		if (!is_numeric($data['userID'])) {
			$this->setError(JText::_('Invalid userID'));
			return false;
		}
		if (isset($data['apiKey']) && $data['apiKey'] == '') {
			unset($data['apiKey']);
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
	public function checkin($userID = null)
	{
		// Initialize variables.
		$user		= &JFactory::getUser();
		$juserId	= (int) $user->get('id');
		$userID	= (int) $userID;

		if ($userID === 0) {
			$userID = $this->getState('account.userID');
		}

		if (empty($userID)) {
			return true;
		}

		// Get a EveTableaccount instance.
		$table = &$this->getAccount();

		// Attempt to check-in the row.
		$return = $table->checkin($userID);
		// Check for a database error.
		if ($return === false) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to check-out a account for editing.
	 *
	 * @param	int		$userID	The numeric id of the account to check-out.
	 * @return	bool	False on failure or error, success otherwise.
	 * @since	1.6
	 */
	public function checkout($userID)
	{
		// Initialize variables.
		$user		= &JFactory::getUser();
		$juserId	= (int) $user->get('id');
		$userID	= (int) $userID;

		// Check for a new account id.
		if ($userID === -1) {
			return true;
		}

		$table = &$this->getAccount();

		// Attempt to check-out the row.
		$return = $table->checkout($juserId, $userID);

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
	 * Tests if account is checked out
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

		$userID = (int) $this->getState('account.userID');

		if (empty($userID)) {
			return true;
		}

		$table = &$this->getAccount();

		$return = $table->load($userID);

		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return false;
		}

		return $table->isCheckedOut($juserId);
	}
	
	/**
	 * Method to delete accounts from the database.
	 *
	 * @param	integer	$cid	An array of	numeric ids of the rows.
	 * @return	int|False	int on success/false on failure.
	 */
	public function delete($cid)
	{
		$i = 0;
		// Get a account row instance
		$table = $this->getInstance('Account');
		
		foreach ($cid as $id) {
			// Load the row.
			$return = $table->load($id);

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
			$i += 1;
		}
		return $i;
	}

	/**
	 * Get instance of EveTableAccount
	 *
	 * @param int $id
	 * @return EveTableAccount
	 */
	function getAccount($userID = null) {
		return $this->getInstance('Account', $userID);
	}
	
	function getApiStates() {
		return $this->getEnumOptions('#__eve_accounts', 'apiStatus');
	}
	
	function apiGetCharacters($cid) {
		$app = JFactory::getApplication();
		JArrayHelper::toInteger($cid);
		
		if (!count($cid)) {
			JError::raiseWarning(500, JText::_('NO ACCOUNTS SELECTED'));
			return false;
		}
		JPluginHelper::importPlugin('eveapi');
		$dispatcher =& JDispatcher::getInstance();
		
		$count = 0;
		$ale = $this->getAleEVEOnline();
		foreach ($cid as $userID) {
			try {
				$account = $this->getAccount($userID);
				$ale->setCredentials($account->userID, $account->apiKey);
				$xml = $ale->account->Characters();
				$dispatcher->trigger('accountCharacters', 
					array($xml, $ale->isFromCache(), array('userID' => $userID)));
				$count += 1;
			}
			catch (AleExceptionEVEAuthentication $e) {
				EveHelper::updateApiStatus($account, $e->getCode(), true);
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
			$app->enqueueMessage(JText::_('CHARACTERS FROM ACCOUNT SUCCESSFULLY IMPORTED'));
		}
		if ($count > 1) {
			$app->enqueueMessage(JText::sprintf('CHARACTERS FROM %s ACCOUNTS SUCCESSFULLY IMPORTED', $count));
		}
	}
	
}
