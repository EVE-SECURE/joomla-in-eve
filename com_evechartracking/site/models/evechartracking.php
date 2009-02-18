<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Character Tracking
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

class EvechartrackingModelEvechartracking  extends JModel {
	
	function getUsers() {
		/*
		$user = JFactory::getUser();
		if (!$user->id) {
			return null;
		}
		
		$q = new JQuery();
		$q->addTable('#__eve_characters');
		$q->addWhere('userID = '.$user->id);
		$q->addQuery('DISTINCT corporationID');
		$corps = $q->loadResultArray();
		if (empty($corps)) {
			return null;
		}
		
		$corps = implode(', ', $corps);
		*/
		$q = new JQuery();
		$q->addTable('#__users', 'us');
		$q->addQuery('us.id, us.name');
		$q->addOrder('us.name');
		return $q->loadObjectList('id');
	}
	
	function getCorps() {
		$user = JFactory::getUser();
		if (!$user->id) {
			return null;
		}
		
		$q = new JQuery();
		$q->addTable('#__eve_characters', 'ch');
		$q->addJoin('#__eve_accounts', 'ac', 'ac.userID=ch.userID');
		$q->addWhere('ac.owner = %s', $user->id);
		$q->addQuery('DISTINCT corporationID');
		$corps = $q->loadResultArray();
		if (empty($corps)) {
			return null;
		}
		
		$corps = implode(', ', $corps);
		
		$q = new JQuery();
		$q->addTable('#__eve_corporations', 'co');
		$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
		$q->addWhere('co.owner OR al.owner');
		$q->addWhere('co.corporationID IN (%s)', $corps);
		$q->addQuery('co.corporationID, co.corporationName');
		$q->addOrder('co.corporationName');
		return $q->loadObjectList('corporationID');
	}
	
	function getMembers($ID, $layout, $limitstart, $limit) {
		$q = new JQuery();
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
		if ($layout == 'corp') {
			$q->addJoin('#__users', 'us', 'ac.owner=us.id');
			$q->addQuery('us.name AS userName');
			$q->addWhere('ch.corporationID = %s', $ID);
		} else {
			$q->addJoin('#__eve_corporations', 'co', 'ch.corporationID=co.corporationID');
			$q->addQuery('co.corporationName');
			$q->addWhere('ac.owner = %s', $ID);
		}
		if ($limit > 0) {
			$q->setLimit($limit, $limitstart);
		}
		return $q->loadObjectList();
	}
	
	function getMemberCount($ID, $layout) {
		$q = new JQuery();
		$q->addTable('#__eve_characters', 'ch');
		if ($layout == 'corp') {
			$q->addWhere('ch.corporationID = %s', $ID);
		} else {
			$q->addJoin('#__eve_accounts', 'ac', 'ch.userID=ac.userID');
			$q->addWhere('ac.owner = %s', $ID);
		}
		$q->addQuery('COUNT(ch.characterID)');
		return $q->loadResult();
		
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
	
}
