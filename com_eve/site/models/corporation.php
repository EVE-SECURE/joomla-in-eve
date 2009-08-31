<?php

/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Community Builder - Character Sheet
 * @copyright	Copyright (C) 2009 Pavol Kovalik. All rights reserved.
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

class EveModelCorporation extends JModelItem
{
	protected $_corporation = null;
	
	protected $_context = 'com_eve.corporation';
	
	protected function _populateState()
	{
		$id = JRequest::getInt('corporationID');
		$this->setState('corporation.corporationID', $id);
		$params = JComponentHelper::getParams('com_eve');
		$this->setState('params', $params);
		
	}
	
	protected function _loadCorporation()
	{
		if (isset($this->_corporation)) {
			return;
		}
		$id = $this->getState('corporation.corporationID');
		$dbo = $this->getDBO();
		$q = EveFactory::getQuery($dbo);
		$q->addTable('#__eve_corporations', 'co');
		$q->addJoin('#__eve_characters', 'ch', 'co.ceoID=ch.characterID');
		$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
		$q->addJoin('staStations', 'st', 'co.stationID=st.stationID');
		$q->addQuery('co.*');
		$q->addQuery('ch.name AS ceoName');
		$q->addQuery('al.name AS allianceName', 'al.shortName AS allianceShortName');
		$q->addQuery('stationName');
		$q->addWhere('co.corporationID='. $id);		
		$this->_corporation = $q->loadObject();
	}
	
	public function setCorporationID($id)
	{
		if (!$this->__state_set) {
			// Private method to auto-populate the model state.
			$this->_populateState();
			
			$this->setState('corporation.corporationID', $id);
			// Set the model state set flat to true.
			
			$this->__state_set = true;
		} else {
			//TODO: set error when trying to rewite corporationID
			$this->setError('');
		}
		
	}
	
	public function getParams()
	{
		$params = $this->getState('params');
		return $params;
	}
	
	public function getCorporation()
	{
		$this->_loadCorporation();
		return $this->_corporation;
	}
	
	function getMembers()
	{
		$id = $this->getState('corporation.corporationID');
		$dbo = $this->getDBO();
		$q = EveFactory::getQuery($dbo);
		$q->addTable('#__eve_characters');
		$q->addWhere('corporationID=%s', $id);
		$q->addOrder('name');
		return $q->loadObjectList();
		
	}
}