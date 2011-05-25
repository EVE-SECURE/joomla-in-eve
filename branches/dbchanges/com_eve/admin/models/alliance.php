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

class EveModelAlliance extends EveModel {
	
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
			$allianceID = (int) $app->getUserState('com_eve.edit.alliance.allianceID');
			$this->setState('alliance.allianceID', $allianceID);
		} else {
			$allianceID = (int) JRequest::getInt('allianceID');
			$this->setState('alliance.allianceID', $allianceID);
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
	public function &getItem($allianceID = null)
	{
		// Initialize variables.
		$allianceID	= (!empty($allianceID)) ? $allianceID : (int) $this->getState('alliance.allianceID');
		$false		= false;

		// Attempt to load the row.
		$return = $this->getAlliance($allianceID);
		
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
		$allianceID	= (int) $this->getState('alliance.allianceID');
		$isNew		= true;

		// Get a alliance row instance.
		$table = &$this->getItem($allianceID);
		
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
		
		return $table->allianceID;
	}

	protected function _prepareTable(&$alliance)
	{

	}
	
	public function validate($data = null)
	{
		if (!is_numeric($data['allianceID'])) {
			$this->setError(JText::_('Invalid allianceID'));
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
	public function checkin($allianceID = null)
	{
		// Initialize variables.
		$user		= &JFactory::getUser();
		$juserId	= (int) $user->get('id');
		$allianceID	= (int) $allianceID;

		if ($allianceID === 0) {
			$allianceID = $this->getState('alliance.allianceID');
		}

		if (empty($allianceID)) {
			return true;
		}

		// Get a EveTablealliance instance.
		$table = &$this->getAlliance();

		// Attempt to check-in the row.
		$return = $table->checkin($allianceID);
		// Check for a database error.
		if ($return === false) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to check-out a alliance for editing.
	 *
	 * @param	int		$allianceID	The numeric id of the alliance to check-out.
	 * @return	bool	False on failure or error, success otherwise.
	 * @since	1.6
	 */
	public function checkout($allianceID)
	{
		// Initialize variables.
		$user		= &JFactory::getUser();
		$juserId	= (int) $user->get('id');
		$allianceID	= (int) $allianceID;

		// Check for a new alliance id.
		if ($allianceID === -1) {
			return true;
		}

		$table = &$this->getAlliance();

		// Attempt to check-out the row.
		$return = $table->checkout($juserId, $allianceID);

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
	 * Tests if alliance is checked out
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

		$allianceID = (int) $this->getState('alliance.allianceID');

		if (empty($allianceID)) {
			return true;
		}

		$table = &$this->getAlliance();

		$return = $table->load($allianceID);

		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return false;
		}

		return $table->isCheckedOut($juserId);
	}
	
	/**
	 * Method to delete alliances from the database.
	 *
	 * @param	integer	$cid	An array of	numeric ids of the rows.
	 * @return	int|False	int on success/false on failure.
	 */
	public function delete($cid)
	{
		$i = 0;
		// Get a alliance row instance
		$table = $this->getInstance('Alliance');
		
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
	
	function getAlliance($id = null) {
		return $this->getInstance('Alliance', $id);
	}
	
	function store() {
		$app = JFactory::getApplication();
		$alliance = $this->getAlliance(JRequest::getInt('allianceID'));
		$post = JRequest::get('post');
		$ownerPast = $alliance->owner;
		
		if (!$alliance->bind( $post )) {
			return JError::raiseWarning( 500, $alliance->getError() );
		}
		
		if (!$alliance->check()) {
			return JError::raiseWarning( 500, $alliance->getError() );
		}
		
		if (!$alliance->store()) {
			return JError::raiseWarning( 500, $alliance->getError() );
		}

		
		$app->enqueueMessage(JText::_('ALLIANCE STORED'));
		
	}
	
	function apiGetAllianceList() {
		try {
			$ale = $this->getAleEVEOnline();
			$xml = $ale->eve->AllianceList();
			
			JPluginHelper::importPlugin('eveapi');
			$dispatcher =JDispatcher::getInstance();
			
			$dispatcher->trigger('eveAllianceList', array($xml, $ale->isFromCache(), array()));
			
			return true;
		}
		catch (RuntimeException $e) {
			JError::raiseWarning($e->getCode(), $e->getMessage());
		}
		catch (Exception $e) {
			JError::raiseError($e->getCode(), $e->getMessage());
		}
		return false;
	}
	
	function apiGetAllianceMembers($cid) {
		try {
			$ale = $this->getAleEVEOnline();
			$xml = $ale->eve->AllianceList();
			
			JPluginHelper::importPlugin('eveapi');
			$dispatcher =& JDispatcher::getInstance();
			$dispatcher->trigger('eveAllianceList', 
				array($xml, $ale->isFromCache(), array()));
			
			$conditions = array();
			foreach ($cid as $allianceID) {
				$conditions[] = "@allianceID='$allianceID'";
			}
			$_condition = implode(' or ', $conditions);
			$corps = $xml->xpath('/eveapi/result/rowset/row['.$_condition.']/rowset/row');
			
			$count = 0;
			foreach ($corps as $corp) {
				try {
					$corporationID = (int) $corp->corporationID;
					$corporation = $this->getInstance('Corporation', $corporationID);
					$xml = $ale->corp->CorporationSheet(array('corporationID' => $corporationID), ALE_AUTH_NONE);
					$dispatcher->trigger('corpCorporationSheet', 
						array($xml, $ale->isFromCache(), array()));
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
		catch (RuntimeException $e) {
			JError::raiseWarning($e->getCode(), $e->getMessage());
		}
		catch (Exception $e) {
			JError::raiseError($e->getCode(), $e->getMessage());
		}
	}

	public function getAllianceMemberIDs($cid)
	{
		$q = $this->getQuery();
		$q->addTable('#__eve_corporations', 'co');
		$q->addWhere('co.allianceID IN ('. implode(',', $cid).')');
		$q->addQuery('co.corporationID');
		$result = $q->loadResultArray();
		return $result;
	}

	
	public function setOwner($cid, $isOwner)
	{
		$count = 0;
		foreach ($cid as $allianceID) {
			$alliance = EveFactory::getInstance('Alliance', $allianceID);
			$alliance->owner = $isOwner ? 1 : null;
			$alliance->store(true);
			$count += 1;
			
		}
		return $count;
	}
	
}
