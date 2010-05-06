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

require_once JPATH_SITE.DS.'components'.DS.'com_eve'.DS.'models'.DS.'character.php';

class EvecharsheetModelCharacter extends EveModelCharacter {
	protected $dbdump;
	protected $skillGroups = array();
	protected $skillToAttributes = array (
		3377 	=> 1,
		12387 	=> 3,
		12385 	=> 4,
		3376 	=> 2,
		12386 	=> 5,
		3378 	=> 4,
		3375 	=> 5,
		12376 	=> 1,
		12383 	=> 2,
		3379 	=> 3,
	);
	protected $learningGroupID = 267;
	protected $learningTypeID = 3374;

	public function __construct($config = array())
	{
		parent::__construct($config);
		$eveparams = JComponentHelper::getParams('com_eve');
		$dbdump_database = $eveparams->get('dbdump_database');
		$this->dbdump = $dbdump_database ? $dbdump_database.'.' :''; 
		
	}
	
	protected function _populateState()
	{
		parent::_populateState();
		$id = JRequest::getInt('characterID');
		$params = JComponentHelper::getParams('com_evecharsheet');
		$this->setState('params', $params);
	}
	
	public function getQuery()
	{
		$dbo = $this->getDBO();
		return EveFactory::getQuery($dbo);
	}
	
	function getClone($characterID = null)
	{
		$character = $this->getItem($characterID);
		$q = $this->getQuery();
		$q->addTable('#__eve_charclone', 'cc');
		$q->addJoin($this->dbdump.'invTypes', 'it', 'cc.cloneID=it.typeID');
		$q->addJoin($this->dbdump.'dgmTypeAttributes', 'dta', 'dta.typeID=it.typeID AND dta.attributeID=419'); //FIXME: magical consant
		$q->addQuery('it.typeID AS cloneID', 'it.typeName AS cloneName', 'dta.valueInt AS cloneSkillPoints');
		$q->addWhere('cc.characterID='.intval($character->characterID));
		$clone = $q->loadObject();
		return $clone;
	}
	
	function getQueue($characterID = null) 
	{
		$character = $this->getItem($characterID);
		$q = $this->getQuery();
		$q->addTable('#__eve_skillqueue', 'sq');
		$q->addJoin($this->dbdump.'invTypes', 'it', 'it.typeID=sq.typeID');
		$q->addWhere("characterID='%s'", $character->characterID);
		$q->addOrder('queuePosition', 'ASC');
		$queue =  $q->loadObjectList();
		
		return $queue;
	}
	
	function getSkillGroups($characterID = null) 
	{
		$character = $this->getItem($characterID);
		
		if (!isset($this->skillGroups[$character->characterID])) {
			$q = $this->getQuery();
			$q->addTable('#__eve_charskills', 'cs');
			$q->addJoin($this->dbdump.'invTypes', 'it', 'it.typeID=cs.typeID', 'inner');
			$q->addWhere("characterID='%s'", $character->characterID);
			$q->addOrder('typeName', 'ASC');
			$skills =  $q->loadObjectList();
			
			$q = $this->getQuery();
			$q->addTable($this->dbdump.'invGroups');
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
			$this->skillGroups[$character->characterID] = $groups;
		}
		
		return $this->skillGroups[$character->characterID];
	}
	
	function getCertificateCategories($characterID = null)
	{
		$character = $this->getItem($characterID);
		$q = $this->getQuery();
		$q->addTable('#__eve_charcertificates', 'cs');
		$q->addJoin($this->dbdump.'crtCertificates', 'cr', 'cr.certificateID=cs.certificateID', 'inner');
		$q->addJoin($this->dbdump.'crtClasses', 'cl', 'cr.classID=cl.classID');
		$q->addWhere("characterID='%s'", $character->characterID);
		$q->addQuery('cr.*', 'cl.className');
		$q->addOrder('cr.classID', 'ASC');
		$q->addOrder('cr.grade', 'ASC');
		$certificates = $q->loadObjectList('classID');

		$q = $this->getQuery();
		$q->addTable($this->dbdump.'crtCategories', 'ct');
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
	
	function getAttributes($characterID = null)
	{
		$character = $this->getItem($characterID);
		$q = $this->getQuery();
		$q->addTable('#__eve_charattributes', 'cc');
		$q->addJoin($this->dbdump.'chrAttributes', 'ca', 'ca.attributeID=cc.attributeID');
		$q->addJoin($this->dbdump.'invTypes', 'it', 'it.typeID=cc.augmentatorID');
		$q->addQuery('cc.*');
		$q->addQuery('it.typeName AS augmentatorName');
		$q->addQuery('ca.attributeName', 'ca.description', 'ca.shortDescription');
		$q->addWhere("characterID='%s'", $character->characterID);
		$q->addOrder('ca.attributeName');
		$attributes = $q->loadObjectList('attributeID');
		
		$skillGroups = $this->getSkillGroups($characterID);
		$skillMultiplier = 1.0;
		foreach ($attributes as $attribute) {
			$attribute->skillValue = 0;
		}
		$learningSkills = JArrayHelper::getValue($skillGroups, $this->learningGroupID, array());
		foreach ($learningSkills->skills as $learningSkill) {
			if ($learningSkill->typeID == $this->learningTypeID) {
				$skillMultiplier = 1.0 + $learningSkill->level / 50;
			} else {
				$attributeID = JArrayHelper::getValue($this->skillToAttributes, $learningSkill->typeID);
				$attribute = JArrayHelper::getValue($attributes, $attributeID);
				if ($attribute) {
					$attribute->skillValue += $learningSkill->level;
				}
			}
		}
		foreach ($attributes as $attribute) {
			$attribute->skillMultiplier = $skillMultiplier;
		}
		
		return $attributes;
	}
	
	function getRoles($characterID = null)
	{
		$character = $this->getItem($characterID);
		$q = $this->getQuery();
		$q->addTable('#__eve_charroles', 'cr');
		$q->addJoin('#__eve_roles', 'ro', 'cr.roleID=ro.roleID');
		$q->addGroup('cr.roleID');
		$q->addQuery('cr.*');
		$q->addQuery('ro.roleName');
		foreach ($this->getRoleLocations() as $i => $location) {
			$q->addQuery(sprintf('MAX(IF(cr.location=%s, 1, 0)) AS %s', $i, $location));
		}
		$q->addWhere("characterID='%s'", $character->characterID);
		$q->addOrder('cr.roleID');
		return $q->loadObjectList();
	}
	
	
	function getRoleLocations()
	{
		return array('corporationRoles', 'corporationRolesAtHQ', 'corporationRolesAtBase', 'corporationRolesAtOther');
	}
	
	function getTitles($characterID = null)
	{
		$character = $this->getItem($characterID);
		$q = $this->getQuery();
		$q->addTable('#__eve_chartitles', 'ch');
		$q->addTable('#__eve_corptitles', 'co');
		$q->addWhere('ch.titleID=co.titleID');
		$q->addWhere("characterID='%s'", $character->characterID);
		$q->addWhere("corporationID='%s'", $character->corporationID);
		return $q->loadObjectList();
	}
}