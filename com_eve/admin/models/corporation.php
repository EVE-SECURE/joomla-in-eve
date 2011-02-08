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

class EveModelCorporation extends EveModel {
	
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
			$corporationID = (int) $app->getUserState('com_eve.edit.corporation.corporationID');
			$this->setState('corporation.corporationID', $corporationID);
		} else {
			$corporationID = (int) JRequest::getInt('corporationID');
			$this->setState('corporation.corporationID', $corporationID);
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
	public function &getItem($corporationID = null)
	{
		// Initialize variables.
		$corporationID	= (!empty($corporationID)) ? $corporationID : (int) $this->getState('corporation.corporationID');
		$false		= false;

		// Attempt to load the row.
		$return = $this->getCorporation($corporationID);
		
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
		$corporationID	= (int) $this->getState('corporation.corporationID');
		$isNew		= true;

		// Get a corporation row instance.
		$table = &$this->getItem($corporationID);
		
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
		if (!$table->store(true)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
			
		return $table->corporationID;
	}

	protected function _prepareTable(&$corporation)
	{

	}
	
	public function validate($data = null)
	{
		if (!is_numeric($data['corporationID'])) {
			$this->setError(JText::_('COM_EVE_ERROR_INVALID_CORPORATIONID'));
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
	public function checkin($corporationID = null)
	{
		// Initialize variables.
		$user		= &JFactory::getUser();
		$juserId	= (int) $user->get('id');
		$corporationID	= (int) $corporationID;

		if ($corporationID === 0) {
			$corporationID = $this->getState('corporation.corporationID');
		}

		if (empty($corporationID)) {
			return true;
		}

		// Get a EveTablecorporation instance.
		$table = &$this->getCorporation();

		// Attempt to check-in the row.
		$return = $table->checkin($corporationID);
		// Check for a database error.
		if ($return === false) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to check-out a corporation for editing.
	 *
	 * @param	int		$corporationID	The numeric id of the corporation to check-out.
	 * @return	bool	False on failure or error, success otherwise.
	 * @since	1.6
	 */
	public function checkout($corporationID)
	{
		// Initialize variables.
		$user		= &JFactory::getUser();
		$juserId	= (int) $user->get('id');
		$corporationID	= (int) $corporationID;

		// Check for a new corporation id.
		if ($corporationID === -1) {
			return true;
		}

		$table = &$this->getCorporation();

		// Attempt to check-out the row.
		$return = $table->checkout($juserId, $corporationID);

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
	 * Tests if corporation is checked out
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

		$corporationID = (int) $this->getState('corporation.corporationID');

		if (empty($corporationID)) {
			return true;
		}

		$table = &$this->getCorporation();

		$return = $table->load($corporationID);

		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return false;
		}

		return $table->isCheckedOut($juserId);
	}
	

	/**
	 * Method to delete corporations from the database.
	 *
	 * @param	integer	$cid	An array of	numeric ids of the rows.
	 * @return	int|False	int on success/false on failure.
	 */
	public function delete($cid)
	{
		$i = 0;
		// Get a corporation row instance
		$table = $this->getInstance('Corporation');
		
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
	 * Enter description here...
	 *
	 * @param int $id
	 * @return EveTableCorporation
	 */
	function getCorporation($corporationID = null) {
		return $this->getInstance('Corporation', $corporationID);
	}
	
	protected function corporationSheet($corporation, $useCeoApi = true)
	{
		JPluginHelper::importPlugin('eveapi');
		$dispatcher =& JDispatcher::getInstance();
		
		$ale = $this->getAleEVEOnline();
		$xml = null;
		$options = array();
		if ($useCeoApi) {
			$ceo = $this->getInstance('Character', $corporation->ceoID);
			$account = $this->getInstance('Account', $ceo->userID);
			if ($account->apiKey) {
				try {
					$ale->setCredentials($account->userID, $account->apiKey, $ceo->characterID);
					$xml = $ale->corp->CorporationSheet();
					$options['characterID'] = $ceo->characterID;
				}
				catch (AleExceptionEVEAuthentication $e) {
					$xml = $ale->corp->CorporationSheet(array('corporationID' => $corporation->corporationID), ALE_AUTH_NONE);
				}
			}
		}
		if (is_null($xml)) {
			$xml = $ale->corp->CorporationSheet(array('corporationID' => $corporation->corporationID), ALE_AUTH_NONE);
		}
		$dispatcher->trigger('corpCorporationSheet', array($xml, $ale->isFromCache(), $options));
	}
	
	public function apiGetCorporationSheet($cid) {
		$count = 0;
		foreach ($cid as $corporationID) {
			try {
				$corporation = $this->getCorporation($corporationID);
				$this->corporationSheet($corporation, true);
				$count += 1;
			}
			catch (RuntimeException $e) {
				JError::raiseWarning($e->getCode(), $e->getMessage());
			}
			catch (Exception $e) {
				JError::raiseError($e->getCode(), $e->getMessage());
			}
		}
		return $count;
	}
	
	function apiGetMemberTracking($cid) {
		JPluginHelper::importPlugin('eveapi');
		$dispatcher =& JDispatcher::getInstance();
		
		$count = 0;
		$ale = $this->getAleEVEOnline();
		foreach ($cid as $corporationID) {
			try {
				$corporation = $this->getCorporation($corporationID);
				$ceo = $this->getInstance('Character', $corporation->ceoID);
				$account = $this->getInstance('Account', $ceo->userID);
				if (!$account->isLoaded()) {
					$this->setError(JText::_('COM_EVE_ERROR_CORPORATION_NO_CEO_APIKEY', $corporation->corporationName, $corporation->corporationID));
					continue;
				}
				
				$ale->setCredentials($account->userID, $account->apiKey, $ceo->characterID);
				$xml = $ale->corp->MemberTracking();
	
				$dispatcher->trigger('corpMemberTracking', 
					array($xml, $ale->isFromCache(), array('characterID' => $ceo->characterID, 'corporationID' => $ceo->corporationID)));
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
		return $count;
	}
	
	public function setOwner($cid, $isOwner, $store = true)
	{
		$q = $this->getQuery();
		$q->addTable('#__eve_corporations', 'co');
		$q->addJoin('#__eve_characters', 'ch', 'co.ceoID=ch.characterID');
		$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
		$q->addWhere('co.corporationID IN ('. implode(',', $cid).')');
		$q->addQuery('co.corporationID', 'co.corporationName');
		$q->addQuery('ch.characterID', 'ch.userID');
		$q->addQuery('co.owner', 'al.owner AS derived_owner');
		$ceos = $q->loadObjectList();
		
		$result = 0;
		JPluginHelper::importPlugin('eveapi');
		foreach ($ceos as $ceo) {
			$corporation = EveFactory::getInstance('Corporation', $ceo->corporationID);
			if ($store) {
				$corporation->owner = $isOwner ? 1 : null;
				$corporation->store(true);
			}

			$setOwner = $ceo->derived_owner || $corporation->owner;
			if (!$store && $ceo->owner && !$isOwner) {
				//unseting alliance, but corporation is set as owner
				continue;
			} 
			if ($store && $ceo->derived_owner && !$isOwner) {
				//unsetting corporatation, but alliance is owner
				continue;
			}
			$result += 1;
			
			if ($ceo->userID && $ceo->characterID) {
				$dispatcher =& JDispatcher::getInstance();
				$dispatcher->trigger('onSetOwnerCorporation', array($ceo->userID, $ceo->characterID, $setOwner));
				continue;
			} else {
				if ($setOwner) {
					$this->setError(JText::sprintf('COM_EVE_ERROR_CORPORATION_NO_CEO_APIKEY', $ceo->corporationName, $ceo->corporationID));
				}
			}
		}
		
		return $result;
		
	}
	
	
}
