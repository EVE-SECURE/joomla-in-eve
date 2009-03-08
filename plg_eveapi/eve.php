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
	
	public function accountCharacters($xml, $fromCache, $options = array()) {
		$dbo = JFactory::getDBO();
		$userID = JArrayHelper::getValue($options, 'userID', null, 'int');
		$sql = 'UPDATE #__eve_characters SET userID=0 WHERE userID='.$userID;
		$dbo->Execute($sql);
		foreach ($xml->result->characters->toArray() as $characterID => $array) {
			$character = EveFactory::getInstance('Character', $characterID);
			$character->userID = $userID;
			$character->save($array);
			
			$corporation = EveFactory::getInstance('Corporation', $array['corporationID']);
			if (!$corporation->isLoaded()) {
				$corporation->save($array);
			}
		}
	}
	
	public function charCharacterTracking($xml, $fromCache, $options = array()) {
		$character = EveFactory::getInstance('Character', $xml->result->characterID);
		$sheet = $xml->result->toArray();
		$character->save($sheet);
	}
	
	public function corpCorporationSheet($xml, $fromCache, $options = array()) {
		$corporation = EveFactory::getInstance('Corporation', $xml->result->corporationID);
		$sheet = $xml->result->toArray();
		$corporation->save($sheet);
	}
	
	public function corpMemberTracking($xml, $fromCache, $options = array()) {
		foreach ($xml->result->members as $characterID => $member) {
			$sheet = $member->toArray();
			$sheet['corporationID'] = $options['corporationID'];
			$character = EveFactory::getInstance('Character', $characterID);
			$character->save($sheet);
		}
	}
	
	public function eveAlianceList($xml, $fromCache, $options = array()) {
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
