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

class EveModelAlliance extends JModelItem
{
	protected $_alliance = null;
	
	protected $_context = 'com_eve.alliance';
	
	protected function _populateState()
	{
		$id = JRequest::getInt('allianceID');
		$this->setState('alliance.allianceID', $id);
		$params = JComponentHelper::getParams('com_eve');
		$this->setState('params', $params);
		
	}
	
	protected function _loadAlliance()
	{
		if (isset($this->_alliance)) {
			return;
		}
		$id = $this->getState('alliance.allianceID');
		$dbo = $this->getDBO();
		$q = EveFactory::getQuery($dbo);
		$q->addTable('#__eve_alliances', 'al');
		$q->addJoin('#__eve_corporations', 'co', 'al.executorCorpID=co.corporationID');
		$q->addQuery('al.*');
		$q->addQuery('co.corporationName AS executorCorpName', 'co.ticker AS executorTicker');
		$q->addWhere('al.allianceID='. $id);
		$this->_alliance = $q->loadObject();
	}
	
	public function setAllianceID($id)
	{
		if (!$this->__state_set) {
			// Private method to auto-populate the model state.
			$this->_populateState();
			
			$this->setState('alliance.allianceID', $id);
			// Set the model state set flat to true.
			
			$this->__state_set = true;
		} else {
			//TODO: set error when trying to rewite allianceID
			$this->setError('');
		}
		
	}
	
	public function getParams()
	{
		$params = $this->getState('params');
		return $params;
	}
	
	public function getAlliance()
	{
		$this->_loadAlliance();
		return $this->_alliance;
	}
	
	function getMembers()
	{
		$id = $this->getState('alliance.allianceID');
		$dbo = $this->getDBO();
		$q = EveFactory::getQuery($dbo);
		$q->addTable('#__eve_corporations');
		$q->addWhere('allianceID=%s', $id);
		$q->addOrder('corporationName');
		return $q->loadObjectList();
		
	}
}