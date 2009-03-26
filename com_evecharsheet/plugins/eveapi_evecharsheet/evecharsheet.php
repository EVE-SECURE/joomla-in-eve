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
	function __construct($subject, $config = array()) {
		parent::__construct($subject, $config);
	}
	
	public function charCharacterSheet($xml, $fromCache, $options = array()) {
		$characterID = JArrayHelper::getValue($options, 'characterID', 0, 'int');
		$values = '';
		foreach ($xml->result->skills as $skill) {
			if ($values) {
				$values .= ",\n"; 
			}
			$values .= sprintf("('%s', '%s', '%s', '%s')", $characterID, 
				intval($skill->typeID), intval($skill->skillpoints), intval($skill->level));
		}
		
		if (!$values) {
			return;
		}
		
		$dbo = JFactory::getDBO();
		$sql = 'INSERT INTO #__eve_charskills (characterID, typeID, skillpoints, level) VALUES '.$values;
		$dbo->Execute('DELETE FROM #__eve_charskills WHERE characterID = '. $characterID);
		$dbo->Execute($sql);
	}
	
	public function charSkillInTraining($xml, $fromCache, $options = array()) {
		 
	}
	
	public function charSkillQueue($xml, $fromCache, $options = array()) {
		
	}
	
}
