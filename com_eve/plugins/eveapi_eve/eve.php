<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Core
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
 * @subpackage	Core
 */
class plgEveapiEve extends JPlugin {
	function __construct($subject, $config = array()) {
		parent::__construct($subject, $config);
	}
	
	public function onRegisterAccount($userID, $apiStatus) {
		$schedule = JTable::getInstance('Schedule', 'EveTable');
		$schedule->loadExtra('account', 'Characters', $userID);
		if (!$schedule->id && $schedule->apicall) {
			$next = new DateTime();
			$schedule->next = $next->format('Y-m-d H:i:s');
			$schedule->store();
		}
		$query = null;
		$dbo = JFactory::getDBO();
		switch ($apiStatus) {
			case 'Inactive':
				break;
			case 'Invalid':
				$query = 'UPDATE #__eve_schedule AS sc SET sc.published=0 WHERE userID='.intval($userID);
				break;
			case 'Limited':
				$query = 'UPDATE #__eve_schedule AS sc LEFT JOIN #__eve_apicalls AS ap ON sc.apicall=ap.id 
					SET sc.published=IF(ap.authorization=\'Full\', 0, 1) WHERE userID='.intval($userID);
				break;
			case 'Full':
				$query = 'UPDATE #__eve_schedule AS sc SET sc.published=1 WHERE userID='.intval($userID);
				break;
		}
		if ($query) {
			$dbo->Execute($query);
		}
	}
	
	public function onRegisterCharacter($userID, $characterID) {
		//TODO: superclass this
		$next = new DateTime();
		$schedule = JTable::getInstance('Schedule', 'EveTable');
		$schedule->loadExtra('char', 'CharacterSheet', $userID, $characterID);
		if (!$schedule->id && $schedule->apicall) {
			$schedule->next = $next->format('Y-m-d H:i:s');
			$schedule->store();
		}
	}
	
	public function onSetOwnerCorporation($userID, $characterID, $owner) {
		//TODO: superclass EveapiPlugin
		$schedule = JTable::getInstance('Schedule', 'EveTable');
		$schedule->loadExtra('corp', 'CorporationSheet', $userID, $characterID);
		if ($owner && !$schedule->id && $schedule->apicall) {
			$next = new DateTime();
			$schedule->next = $next->format('Y-m-d H:i:s');
			$schedule->store();
		}
		if (!$owner && $schedule->id) {
			$schedule->delete();
		}
	}
	
	public function accountCharacters($xml, $fromCache, $options = array()) {
		//JPluginHelper::importPlugin('eveapi');
		$dispatcher =& JDispatcher::getInstance();
		$dbo = JFactory::getDBO();
		$userID = JArrayHelper::getValue($options, 'userID', null, 'int');
		$sql = 'UPDATE #__eve_characters SET userID=0 WHERE userID='.$userID;
		$dbo->Execute($sql);
		foreach ($xml->result->characters->toArray() as $characterID => $array) {
			$character = EveFactory::getInstance('Character', $characterID);
			$character->userID = $userID;
			$character->save($array);
			
			$dispatcher->trigger('onRegisterCharacter', array($userID, $characterID));
			
			$corporation = EveFactory::getInstance('Corporation', $array['corporationID']);
			if (!$corporation->isLoaded()) {
				$corporation->save($array);
			}
		}
	}
	
	public function charCharacterSheet($xml, $fromCache, $options = array()) {
		$character = EveFactory::getInstance('Character', (string) $xml->result->characterID);
		$sheet = $xml->result->toArray();
		$character->save($sheet);
	}
	
	public function corpCorporationSheet($xml, $fromCache, $options = array()) {
		$corporation = EveFactory::getInstance('Corporation', (string) $xml->result->corporationID);
		$sheet = $xml->result->toArray();
		$corporation->save($sheet);
	}
	
	public function eveAllianceList($xml, $fromCache, $options = array()) {
		if ($fromCache) {
			return;
		}
		$alliances = $xml->result->alliances->toArray();
		foreach ($alliances as $array) {
			$alliance = EveFactory::getInstance('Alliance', $array['allianceID']);
			$alliance->save($array);
		}
	}
	
}
