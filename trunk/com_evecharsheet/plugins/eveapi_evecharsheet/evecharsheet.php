<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Character sheet
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
jimport('joomla.plugin.plugin');

/**
 * Joomla! in EVE Api core plugin
 *
 * @author		Pavol Kovalik  <kovalikp@gmail.com>
 * @package		Joomla! in EVE
 * @subpackage	Character Sheet
 * @since 		1.5
 */
class plgEveapiEvecharsheet extends JPlugin {
	static private $attributes;
	static private $enhancers;
	
	function __construct($subject, $config = array()) {
		parent::__construct($subject, $config);
	}
	
	public function charCharacterSheet($xml, $fromCache, $options = array()) {
		//TODO: update(starTime) and delete(endTime) skills in skillquee queue 
		$characterID = JArrayHelper::getValue($options, 'characterID', 0, 'int');
		
		$values = '';
		foreach ($xml->result->skills as $skill) {
			if ($values) {
				$values .= ",\n"; 
			}
			$values .= sprintf("(%s, %s, %s, %s)", $characterID, 
				intval($skill->typeID), intval($skill->skillpoints), intval($skill->level));
		}
		
		if ($values) {
			$dbo = JFactory::getDBO();
			$sql = 'INSERT INTO #__eve_charskills (characterID, typeID, skillpoints, level) VALUES '.$values;
			$dbo->Execute('DELETE FROM #__eve_charskills WHERE characterID = '. $characterID);
			$dbo->Execute($sql);
		}
		
		
		$values = '';
		foreach ($xml->result->certificates as $certificate) {
			if ($values) {
				$values .= ",\n"; 
			}
			$values .= sprintf("(%s, %s)", $characterID, intval($certificate->certificateID));
		}
		if ($values) {
			$sql = 'INSERT INTO #__eve_charcertificates (characterID, certificateID) VALUES '.$values;
			$dbo->Execute('DELETE FROM #__eve_charcertificates WHERE characterID = '. $characterID);
			$dbo->Execute($sql);
		}
		
		$app = JFactory::getApplication();
		$this->loadAttributes();
		$values = '';
		foreach (self::$attributes as $attribute) {
			$attributeName = strtolower($attribute->attributeName);
			$enhancer = $xml->xpath('//result/attributeEnhancers/'.$attributeName.'Bonus');
			$enhancer = reset($enhancer);
			if ($enhancer !== false) {
				$augmentatorValue = intval((string) $enhancer->augmentatorValue);
				$augmentatorID	= $this->getAugmentatorID((string) $enhancer->augmentatorName);
			} else {
				$augmentatorValue = 0;
				$augmentatorID = 'NULL';
			}
			$attributeValue = intval((string) $xml->result->attributes->$attributeName);
			if ($values) {
				$values .= ",\n"; 
			}
			$values .= sprintf("(%s, %s, %s, %s, %s)", $characterID, $attribute->attributeID, $attributeValue, $augmentatorID, $augmentatorValue);
		}
		if ($values) {
			$sql = 'INSERT INTO #__eve_charattributes (characterID, attributeID, value, augmentatorID, augmentatorValue) VALUES '.$values;
			$dbo->Execute('DELETE FROM #__eve_charattributes WHERE characterID = '. $characterID);
			$dbo->Execute($sql);
		}
		
	}
	
	public function charSkillQueue($xml, $fromCache, $options = array()) {
		//TODO: update skills
		$dbo = JFactory::getDBO();
		
		$characterID = JArrayHelper::getValue($options, 'characterID', 0, 'int');
		$values = '';
		foreach ($xml->result->skillqueue as $skill) {
			if ($values) {
				$values .= ",\n"; 
			}
			$values .= sprintf("('%s', '%s', '%s', '%s', '%s', '%s', %s, %s)", $characterID, 
				intval($skill->queuePosition), intval($skill->typeID), intval($skill->level), 
				intval($skill->startSP), intval($skill->endSP), $dbo->Quote($skill->startTime), $dbo->Quote($skill->endTime));
		}
		
		if (!$values) {
			return;
		}
		
		$sql = 'INSERT INTO #__eve_skillqueue (characterID, queuePosition, typeID, level, startSP, endSP, startTime, endTime) VALUES '.$values;
		$dbo->Execute('DELETE FROM #__eve_skillqueue WHERE characterID = '. $characterID);
		$dbo->Execute($sql);
		
	}
	
	private function loadAttributes()
	{
		if (isset(self::$attributes)) {
			return;
		}
		$sql = 'SELECT * FROM chrAttributes';
		$dbo = JFactory::getDBO();
		$dbo->setQuery($sql);
		self::$attributes = $dbo->loadObjectList();
	}
	
	private function getAugmentatorID($augmentatorName)
	{
		if (!isset(self::$enhancers)) {
			$sql = 'SELECT typeID, typeName FROM invTypes WHERE groupID IN (300, 745)'; 
			$dbo = JFactory::getDBO();
			$dbo->setQuery($sql);
			self::$enhancers = $dbo->loadObjectList('typeName');
		}
		if (isset(self::$enhancers[$augmentatorName])) {
			return self::$enhancers[$augmentatorName]->typeID;
		}
		return 'NULL';
	}
	
}
