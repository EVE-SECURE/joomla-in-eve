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
class plgEveapiEve extends EveApiPlugin {
	function __construct($subject, $config = array()) {
		parent::__construct($subject, $config);
	}
	
	private static $apicalls = null;
	
	private function getApiCallList()
	{
		if (!isset(self::$apicalls)) {
			$dbo = JFactory::getDBO();
			$query = EveFactory::getQuery($dbo);
			$query->addQuery('*');
			$query->addTable('#__eve_apicalls');
			//$query->addWhere('published = 1');
			self::$apicalls = $query->loadObjectList();
		}
		return self::$apicalls;
	}
	
	private function canSchedule($apikey, $apiCall)
	{
		if (($apiCall->type == 'account') && ($apiCall->name == 'APIKeyInfo')) {
			return true;
		}
		switch ($apikey->type) {
			case 'Corporation':
				if ($apiCall->type != 'corp') {
					return false;
				}
				break;
			case 'Character':
			case 'Account':
				if ($apiCall->type != 'char') {
					return false;
				}
				break;
			default:
				return false;
		}
		//check access mask
		if (!(intval($apiCall->accessMask) & intval($apikey->accessMask))) {
			return false;
		}
		return true;
		
	}
	
	public function onScheduleForApiKey($apikey, $entities)
	{
		$dbo = JFactory::getDBO();
		$query = 'DELETE FROM #__eve_schedule WHERE keyID = '.intval($apikey->keyID);
		$dbo->Execute($query);
		$next = new DateTime();
		$apiCallList = $this->getApiCallList();
		foreach ($apiCallList as $apiCall) {
			if (!$this->canSchedule($apikey, $apiCall)) {
				continue;
			}
			if (($apiCall->type == 'char') && ($apikey->type == 'Account')) {
				$tmpEntities = $entities;
			} else {
				$tmpEntities = array(null);
			}
			foreach ($tmpEntities as $entityID)
			{
				$schedule = JTable::getInstance('Schedule', 'EveTable');
				$schedule->apicall = $apiCall->id;
				$schedule->keyID = $apikey->keyID;
				$schedule->characterID = $entityID;
				$schedule->next = $next->format('Y-m-d H:i:s');
				$schedule->published = 0;
				$schedule->store(true);
			}
		}
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
		$this->_registerCharacter('char', 'CharacterSheet', $userID, $characterID);
	}

	public function onSetOwnerCorporation($userID, $characterID, $owner) {
		$this->_setOwnerCorporation('corp', 'CorporationSheet', $owner, $userID, $characterID);
	}

	public function accountAPIKeyInfo($apikey, $xml, $fromCache, $options = array()) {
		$dbo = JFactory::getDBO();
		$changed = false;
		//check changes in mask or type
		$changed = $changed || ($apikey->accessMask != $xml->result->key->accessMask);
		$changed = $changed || ($apikey->type != $xml->result->key->type);

		//update mask and type if necessary
		if ($changed) {
			$apikey->accessMask = $xml->result->key->accessMask;
			$apikey->type = $xml->result->key->type;
			$apikey->store();
		}
		
		//get entity IDs from xml
		$entities = array();
		foreach ($xml->result->key->characters as $character) {
			if ($apikey->type == 'Corporation') {
				$entityID = $character->corporationID;
			} else {
				$entityID = $character->characterID;
			}
			$entities[] = intval($entityID);
		}
		//check changes in entities
		if (!$changed) {
			$query = EveFactory::getQuery($dbo);
			$query->addQuery('COUNT(*)');
			$query->addTable('#__eve_apikey_entities');
			$query->addWhere('keyID = '.$dbo->Quote($apikey->keyID));
			$changed = $changed || (count($entities) != $query->loadResult());
			if (count($entities) > 0) {
				$query->addWhere('entityID IN ('.implode(', ', $entities).')'); 
				$changed = $changed || (count($entities) != $query->loadResult());
			}
		}
		
		//update entities if necessary
		if ($changed) {
			$query = 'DELETE FROM #__eve_apikey_entities WHERE keyID = '.intval($apikey->keyID);
			$dbo->Execute($query);
			foreach ($entities as $entityID) {
				$query = 'INSERT INTO #__eve_apikey_entities (keyID, entityID) VALUES ('
					.intval($apikey->keyID). ', '.intval($entityID).');';
				$dbo->Execute($query);
			}
		}
		
		//reset schedule
		if ($changed) {
			$this->onScheduleForApiKey($apikey, $entities);
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
		foreach ($xml->result->alliances as $ally) {
			$array = $ally->toArray();
			$alliance = EveFactory::getInstance('Alliance', $array['allianceID']);
			$alliance->save($array);
		}
	}
	
	public function apiCallList($xml, $fromCache, $options = array())
	{
		$dbo = JFactory::getDBO();
		$sql = 'UPDATE #__eve_apicalls SET accessMask=NULL';
		$dbo->Execute($sql);
		foreach ($xml->result->calls as $callByMask) {
			foreach ($callByMask as $call) {
				switch ($call->type) {
					case 'Character':
						$type = 'char';
						break;
					case 'Corporation':
						$type = 'corp';
						break;
					default:
						$type = '';
				}
				$sql = sprintf('UPDATE #__eve_apicalls SET accessMask=%s WHERE `type`=%s AND `name`=%s',
					$dbo->Quote($call->accessMask), $dbo->Quote($type), $dbo->Quote($call->name));
				$dbo->Execute($sql);
			}
		}
	} 

}
