<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Character Sheet
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

class EvecharsheetModelList extends EveModel {
	
	function _setWhere(&$q) {
		$q->addJoin('#__eve_accounts', 'ac', 'ac.userID=ch.userID');
		$q->addJoin('#__eve_corporations', 'co', 'co.corporationID=ch.corporationID');
		$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
		
		$show_all = $this->get('show_all');
		if (!$show_all) {
			$q->addWhere('(co.owner OR al.owner)');
		}
		
		$owner = $this->get('owner');
		if ($owner) {
			$q->addWhere('ac.owner = %s', intval($owner));
		}
		
		$corporationID = $this->get('corporationID');
		if ($corporationID) {
			$q->addWhere('ch.corporationID = %s', intval($corporationID));
		}
	}
	
	function getCharacterCount() {
		$q = $this->getQuery();
		$q->addTable('#__eve_characters', 'ch');
		$this->_setWhere($q);
		$q->addQuery('COUNT(characterID)');
		return $q->loadResult();
	}
	
	function getCharacters($limitstart, $limit) {
		$q = $this->getQuery();
		$q->addTable('#__eve_characters', 'ch');
		$this->_setWhere($q);
		$q->addJoin('#__users', 'us', 'ac.owner=us.id');
		$q->addQuery('ch.characterID', 'ch.name AS characterName');
		$q->addQuery('co.corporationID', 'co.corporationName');
		$q->addQuery('al.allianceID', 'al.name AS allianceName');
		$q->addQuery('ac.owner', 'us.name AS ownerName');
		$q->addOrder('characterName');
		$q->setLimit($limit, $limitstart);
		
		return $q->loadObjectList();
	}
	
}

