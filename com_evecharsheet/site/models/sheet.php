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

class EvecharsheetModelSheet extends EveModel {
	
	function getCharacter($characterID) {
		return $this->getInstance('Character', $characterID);
	}
	
	function getGroups() {
		$q = $this->getQuery();
		$q->addTable('invGroups');
		$q->addWhere('`categoryID` = 16');
		$q->addWhere('published = 1');
		return $q->loadObjectList();
	}
	
	function getSkills($characterID, $groupID) {
		$q = $this->getQuery();
		$q->addTable('#__eve_charskills', 'cs');
		$q->addJoin('invTypes', 'it', 'it.typeID=cs.typeID');
		$q->addWhere("it.groupID='%s'", $groupID);
		$q->addWhere("characterID='%s'", $characterID);
		return $q->loadObjectList();
		
	}
	
}