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

class EveModelApikey extends EveModel {


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
			$keyID = (int) $app->getUserState('com_eve.edit.apikey.keyID');
			$this->setState('apikey.keyID', $keyID);
		} else {
			$keyID = (int) JRequest::getInt('keyID');
			$this->setState('apikey.keyID', $keyID);
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
	public function &getItem($keyID = null)
	{
		// Initialize variables.
		$keyID	= (!empty($keyID)) ? $keyID : (int) $this->getState('apikey.keyID');
		$false		= false;

		// Attempt to load the row.
		$return = $this->getApikey($keyID);

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
		$keyID	= (int) $this->getState('apikey.keyID');
		$isNew		= true;

		// Get a apikey row instance.
		$table = &$this->getItem($keyID);

		$vCodePast = $table->vCode;
		$maskPast = $table->accessMask;
		$typePast = $table->type;
		// Bind the data
		if (!$table->bind($data)) {
			$this->setError(JText::sprintf('JTable_Error_Bind_failed', $table->getError()));
			return false;
		}

		//compare vCode with one stored in database
		$vCodeNow = $table->vCode;
		if ($vCodePast != $vCodeNow) {
			$table->status = 'Unknown';
		}

		// Prepare the row for saving
		$entities = array();
		//$this->_prepareTable($table, $entities);
		$ale = $this->getAleEVEOnline();
		$xml = $this->getAccountAPIKeyInfo($table, $ale);

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

		$query = $this->getQuery();
		$query->addTable('#__eve_apikey_entities');
		$query->addQuery('entityID');
		$query->addWhere('keyID = '.intval($table->keyID));
		$entitiesPast = $query->loadResultArray();

		if ($table->status == 'Active') {
			JPluginHelper::importPlugin('eveapi');
			$dispatcher =& JDispatcher::getInstance();
			$options = array();
			$dispatcher->trigger('accountAPIKeyInfo', array($table, $xml, $ale->isFromCache(), $options));
		}
		
		return $table->keyID;
	}

	/**
	 * @param EveTableApikey $apikey
	 */
	public function getAccountAPIKeyInfo($apikey, &$changed)
	{
		
		$apikey->status == "Unknown";
		//$apikey->type = "Unknown";
		//$changed = false;
		//$changed = ($apikey->mask != $xml->result->key->mask);
		//$changed = ($apikey->type != $xml->result->key->type);
		
		
		try {
			//print_r($apikey); die();
			$ale = $this->getAleEVEOnline();
			$ale->setKey($apikey->keyID, $apikey->vCode);
			$xml = $ale->account->APIKeyInfo();
			$apikey->status = 'Active';
			return $xml;
		}
		catch (AleExceptionEVEAuthentication $e) {
			EveHelper::updateApiStatus($apikey, $e->getCode());
			switch ($apikey->status) {
				case 'Limited':
					JError::raiseNotice(0, JText::_('API key offers limited access'));
					break;
				case 'Invalid':
					JError::raiseWarning(0, JText::_('API key is invalid'));
					break;
				case 'Inactive':
					JError::raiseWarning(0, JText::_('Apikey is inactive'));
					break;
			}
		}
		catch (RuntimeException $e) {
			JError::raiseWarning($e->getCode(), $e->getMessage());
		}
		catch (Exception $e) {
			JError::raiseError($e->getCode(), $e->getMessage());
		}
		return null;
	}

	/**
	 * @param EveTableApikey $apikey
	 */
	protected function _prepareTable($apikey, &$entities)
	{
		$app = JFactory::getApplication();
		if ($apikey->status == 'Unknown') {
			JPluginHelper::importPlugin('eveapi');
			$dispatcher =& JDispatcher::getInstance();
			$ale = $this->getAleEVEOnline();
			try {
				$ale->setKey($apikey->keyID, $apikey->vCode);
				$xml = $ale->account->APIKeyInfo();
				$apikey->accessMask = $xml->result->key->accessMask;
				$apikey->type = $xml->result->key->type;
				//$apikey->expires = $xml->result->key->expires;

				foreach ($xml->result->key->characters as $character) {
					if ($apikey->type == 'Corporation') {
						$entities[] = $character->corporationID;
					} else {
						$entities[] = $character->characterID;
					}
				}
				$apikey->status = 'Active';
				$app->enqueueMessage(JText::sprintf('Com_Eve_Api_Key_Type', $apikey->type));
			}
			catch (AleExceptionEVEAuthentication $e) {
				EveHelper::updateApiStatus($apikey, $e->getCode());
				switch ($apikey->status) {
					case 'Limited':
						JError::raiseNotice(0, JText::_('API key offers limited access'));
						break;
					case 'Invalid':
						JError::raiseWarning(0, JText::_('API key is invalid'));
						break;
					case 'Inactive':
						JError::raiseWarning(0, JText::_('Apikey is inactive'));
						break;
				}
			}
			catch (RuntimeException $e) {
				JError::raiseWarning($e->getCode(), $e->getMessage());
			}
			catch (Exception $e) {
				JError::raiseError($e->getCode(), $e->getMessage());
			}
			$dispatcher->trigger('onRegisterApikey', array($apikey->keyID, $apikey->status));
				
		}
	}

	public function validate($data = null)
	{
		if (!is_numeric($data['keyID'])) {
			$this->setError(JText::_('Invalid keyID'));
			return false;
		}
		if (isset($data['vCode']) && $data['vCode'] == '') {
			unset($data['vCode']);
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
	public function checkin($keyID = null)
	{
		// Initialize variables.
		$user		= &JFactory::getUser();
		$juserId	= (int) $user->get('id');
		$keyID	= (int) $keyID;

		if ($keyID === 0) {
			$keyID = $this->getState('apikey.keyID');
		}

		if (empty($keyID)) {
			return true;
		}

		// Get a EveTableapikey instance.
		$table = &$this->getApikey();

		// Attempt to check-in the row.
		$return = $table->checkin($keyID);
		// Check for a database error.
		if ($return === false) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to check-out a apikey for editing.
	 *
	 * @param	int		$keyID	The numeric id of the apikey to check-out.
	 * @return	bool	False on failure or error, success otherwise.
	 * @since	1.6
	 */
	public function checkout($keyID)
	{
		// Initialize variables.
		$user		= &JFactory::getUser();
		$juserId	= (int) $user->get('id');
		$keyID	= (int) $keyID;

		// Check for a new apikey id.
		if ($keyID === -1) {
			return true;
		}

		$table = &$this->getApikey();

		// Attempt to check-out the row.
		$return = $table->checkout($juserId, $keyID);

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
	 * Tests if apikey is checked out
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

		$keyID = (int) $this->getState('apikey.keyID');

		if (empty($keyID)) {
			return true;
		}

		$table = &$this->getApikey();

		$return = $table->load($keyID);

		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return false;
		}

		return $table->isCheckedOut($juserId);
	}

	/**
	 * Method to delete apikeys from the database.
	 *
	 * @param	integer	$cid	An array of	numeric ids of the rows.
	 * @return	int|False	int on success/false on failure.
	 */
	public function delete($cid)
	{
		$i = 0;
		// Get a apikey row instance
		$table = $this->getInstance('Apikey');

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
	 * Get instance of EveTableApikey
	 *
	 * @param int $id
	 * @return EveTableApikey
	 */
	function getApikey($keyID = null) {
		return $this->getInstance('Apikey', $keyID);
	}

	function getApiStates() {
		return array();
		return $this->getEnumOptions('#__eve_apikeys', 'status');
	}

	function apiGetCharacters($cid) {
		return;
		$app = JFactory::getApplication();
		JArrayHelper::toInteger($cid);

		if (!count($cid)) {
			JError::raiseWarning(500, JText::_('Com_Eve_No_Api_Key_Selected'));
			return false;
		}
		JPluginHelper::importPlugin('eveapi');
		$dispatcher =& JDispatcher::getInstance();

		$count = 0;
		$ale = $this->getAleEVEOnline();
		foreach ($cid as $keyID) {
			try {
				$apikey = $this->getApikey($keyID);
				$ale->setCredentials($apikey->keyID, $apikey->vCode);
				$xml = $ale->apikey->Characters();
				$dispatcher->trigger('apikeyCharacters',
				array($xml, $ale->isFromCache(), array('keyID' => $keyID)));
				$count += 1;
			}
			catch (AleExceptionEVEAuthentication $e) {
				EveHelper::updateApiStatus($apikey, $e->getCode(), true);
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
