<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Character Tracking
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

require_once JPATH_SITE.DS.'components'.DS.'com_eve'.DS.'models'.DS.'corporation.php';

class EvechartrackingModelCorporation extends EveModelCorporation {
	
	protected $_context = 'com_evechartracking.corporation';
	
	protected function _populateState()
	{
		parent::_populateState();
		$app = JFactory::getApplication();
		$context = $this->_context.'.';
		
		$id = JRequest::getInt('corporationID');
		$params = JComponentHelper::getParams('com_evechartracking');
		$this->setState('params', $params);
		
		$defaultColums = $this->getColumns(true);
		if (JRequest::getInt('reset') && is_null(JRequest::getVar('selectedColumns'))) {
			$app->setUserState($context.'selectedColumns', array());
		}
		$selectedColumns = $app->getUserStateFromRequest( $context.'selectedColumns', 'selectedColumns', $defaultColums, 'array' );
		$this->setState('selectedColumns', $selectedColumns);
		
	}
	
	public function getQuery()
	{
		$dbo = $this->getDBO();
		return EveFactory::getQuery($dbo);
	}
	
	function getMembers() {
		$corporation = $this->getCorporation();
		$q = $this->getQuery();
		$q->addTable('#__eve_characters', 'ch');
		$q->addJoin('#__eve_accounts', 'ac', 'ch.userID=ac.userID');
		$q->addJoin('mapDenormalize', 'md', 'ch.locationID=md.itemID');
		$q->addJoin('staStations', 'st', 'ch.locationID=st.stationID');
		$q->addJoin('invTypes', 'iv', 'ch.shipTypeID=iv.typeID');
		$q->addQuery('ch.*');
		$q->addQuery('ac.owner');
		$q->addQuery('md.itemName AS locationName, md.typeID AS locationTypeID');
		$q->addQuery('st.stationName AS baseName, stationTypeID AS baseTypeID');
		$q->addQuery('iv.typeName AS shipTypeName');
		$q->addOrder('ch.name', 'ASC');
		$q->addJoin('#__users', 'us', 'ac.owner=us.id');
		$q->addQuery('us.name AS userName');
		$q->addWhere('ch.corporationID = %s', $corporation->corporationID);
		/*if ($limit > 0) {
			//$q->setLimit($limit, $limitstart);
		}*/
		return $q->loadObjectList();	
	}
	
	function getColumns($onlyShown = false) {
		$params = &JComponentHelper::getParams( 'com_evechartracking' );
		
		$availables =  array('race', 'gender', 'bloodLine', 'balance', 'startDateTime', 'title', 'baseName', 
			'logonDateTime', 'logoffDateTime', 'locationName', 'shipTypeName');
		
		$result = array();
		foreach ($availables as $name) {
			if ($params->get('show_'.$name) > intval($onlyShown)) {
				$result[$name] = $name; 
			}
		}
		
		return $result;
	}
	
	function getSelectedColumns() {
		return $this->getState('selectedColumns');
	}
	
}