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
	
	function getCorporation($corporationID) {
		return $this->getInstance('Corporation', $corporationID);
	}
	
	function getOwner($characterID) {
		$character = $this->getCharacter($characterID);
		$account = $this->getInstance('account', $character->userID);
		if (!$account->owner) {
			return null; 
		} else {
			return JFactory::getUser($account->owner);
		}
	}
	
	function getOwnersCharacters($characterID, $showAll = false) {
		$owner = $this->getOwner($characterID);
		if (is_null($owner)) {
			return array();
		}
		$q = $this->getQuery();
		$q->addTable('#__eve_characters', 'ch');
		$q->addJoin('#__eve_accounts', 'ac', 'ac.userID = ch.userID');
		$q->addWhere('ac.owner = %s', $owner->id);
		if (!$showAll) {
			$corps = EveHelper::getOwnerCoroprationIDs($this->getDBO());
			if (!$corps) {
				return array();
			} else {
				$q->addWhere('corporationID IN (%s)', implode(', ', $corps));
			}
		}
		return $q->loadObjectList();
	}
	
	function getQueue($characterID) {
		$q = $this->getQuery();
		$q->addTable('#__eve_skillqueue', 'sq');
		$q->addJoin('invTypes', 'it', 'it.typeID=sq.typeID');
		$q->addWhere("characterID='%s'", $characterID);
		$q->addOrder('queuePosition', 'ASC');
		$queue =  $q->loadObjectList();
		
		return $queue;
	}
	
	function getSkillGroups($characterID) {
		$q = $this->getQuery();
		$q->addTable('#__eve_charskills', 'cs');
		$q->addJoin('invTypes', 'it', 'it.typeID=cs.typeID', 'inner');
		$q->addWhere("characterID='%s'", $characterID);
		$q->addOrder('typeName', 'ASC');
		$skills =  $q->loadObjectList();
		
		$q = $this->getQuery();
		$q->addTable('invGroups');
		$q->addWhere('`categoryID` = 16');
		$q->addWhere('published = 1');
		$q->addOrder('groupName', 'ASC');
		$groups = $q->loadObjectList('groupID');
		
		foreach ($groups as &$group) {
			$group->skills = array();
			$group->skillCount = 0;
			$group->skillpoints = 0;
			$group->skillPrice = 0;
		}
		
		foreach ($skills as $skill) {
			$groups[$skill->groupID]->skills[] = $skill;
			$groups[$skill->groupID]->skillCount += 1;
			$groups[$skill->groupID]->skillpoints += $skill->skillpoints;
			$groups[$skill->groupID]->skillPrice += $skill->basePrice;
		}
		
		return $groups;
	}
	
	function getCertificateCategories($characterID)
	{
		$q = $this->getQuery();
		$q->addTable('#__eve_charcertificates', 'cs');
		$q->addJoin('crtCertificates', 'cr', 'cr.certificateID=cs.certificateID', 'inner');
		$q->addJoin('crtClasses', 'cl', 'cr.classID=cl.classID');
		$q->addWhere("characterID='%s'", $characterID);
		$q->addQuery('cr.*', 'cl.className');
		$q->addOrder('cr.classID', 'ASC');
		$q->addOrder('cr.grade', 'ASC');
		$certificates = $q->loadObjectList('classID');

		$q = $this->getQuery();
		$q->addTable('crtCategories', 'ct');
		$q->addOrder('categoryName', 'ASC');
		$categories = $q->loadObjectList('categoryID');
		
		foreach ($categories as $category) {
			$category->certificates = array();
		}
		
		foreach ($certificates as $certificate) {
			$categories[$certificate->categoryID]->certificates[] = $certificate; 
		}
		
		return $categories;
	}
	
	function getAttributes($characterID)
	{
		$q = $this->getQuery();
		$q->addTable('#__eve_charattributes', 'cc');
		$q->addJoin('chrAttributes', 'ca', 'ca.attributeID=cc.attributeID');
		$q->addJoin('invTypes', 'it', 'it.typeID=cc.augmentatorID');
		$q->addQuery('cc.*');
		$q->addQuery('it.typeName AS augmentatorName');
		$q->addQuery('ca.attributeName', 'ca.description', 'ca.shortDescription');
		$q->addWhere("characterID='%s'", $characterID);
		$q->addOrder('ca.attributeName');
		return $q->loadObjectList();
	}
	
	function getRoles($characterID)
	{
		
		$q = $this->getQuery();
		$q->addTable('#__eve_charroles', 'cr');
		$q->addJoin('#__eve_roles', 'ro', 'cr.roleID=ro.roleID');
		$q->addGroup('cr.roleID');
		$q->addQuery('cr.*');
		$q->addQuery('ro.roleName');
		foreach ($this->getRoleLocations() as $i => $location) {
			$q->addQuery(sprintf('MAX(IF(cr.location=%s, 1, 0)) AS %s', $i, $location));
		}
		$q->addWhere("characterID='%s'", $characterID);
		$q->addOrder('cr.roleID');
		return $q->loadObjectList();
	}
	
	
	function getRoleLocations()
	{
		return array('corporationRoles', 'corporationRolesAtHQ', 'corporationRolesAtBase', 'corporationRolesAtOther');
	}
	
	function getTitles($characterID)
	{
		$q = $this->getQuery();
		$character = $this->getCharacter($characterID);
		$q->addTable('#__eve_chartitles', 'ch');
		$q->addTable('#__eve_corptitles', 'co');
		$q->addWhere('ch.titleID=co.titleID');
		$q->addWhere("characterID='%s'", $character->characterID);
		$q->addWhere("corporationID='%s'", $character->corporationID);
		return $q->loadObjectList();
	}
}