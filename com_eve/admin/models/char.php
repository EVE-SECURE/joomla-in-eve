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

jimport('joomla.application.component.model');

class EveModelChar extends EveModel {
	
	function __construct($config = array()) {
		$config['table_path'] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'tables';
		parent::__construct($config);
	}
	
	function getChar($id = null) {
		//TODO: delete this method later
		return $this->getCharacter($id);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param int $id
	 * @return TableCharacter
	 */
	function getCharacter($id = null) {
		return $this->getInstance('Character', $id);
	}
	
	function eveCharId() {
		return EveHelperIgb::value('charid');
	}
	
	function charCharacterSheet($xml, $fromCache) {
		$character = $this->getCharacter($xml->result->characterID);
		$character->save($xml->result->toArray());
	}
	
	function apiGetCharacterSheet($cid) {
		JArrayHelper::toInteger($cid);
		
		if (!count($cid)) {
			JError::raiseWarning(500, JText::_('NO CHARACTER SELECTED'));
			return false;
		}
		
		//TODO: Handle exceptions and errors
		JPluginHelper::importPlugin('eveapi');
		$dispatcher =& JDispatcher::getInstance();
		
		$ale = $this->getAleEVEOnline();
		foreach ($cid as $characterID) {
			$character  = $this->getCharacter($characterID);
			$account = $this->getInstance('Account', $character->userID);
			if (!$account->apiKey) {
				continue;
			}
			$ale->setCredentials($account->userID, $account->apiKey, $character->characterID);
			$xml = $ale->char->CharacterSheet();
			$sheet = $xml->result->toArray();
			$character->save($sheet);
			$dispatcher->trigger('onFetchEveapi', 
				array('charCharacterSheet', $xml, $ale->isFromCache(), array('characterID'=>$character->characterID)));
		}
		return ! (bool) JError::getError();
	}
	
	function apiGetCorporationSheet($cid) {
		JArrayHelper::toInteger($cid);
		
		if (!count($cid)) {
			JError::raiseWarning(500, JText::_('NO CHARACTER SELECTED'));
			return false;
		}
		
		//TODO: Handle exceptions and errors
		$ale = $this->getAleEVEOnline();
		$finishedCorps = array();
		foreach ($cid as $characterID) {
			$character = $this->getCharacter($characterID);
			if (in_array($character->corporationID, $finishedCorps)) {
				continue;
			}
			$account = $this->getInstance('Account', $character->userID);
			$corporation = $this->getInstance('Corporation', $character->corporationID);
			$ale->setCredentials($account->userID, $account->apiKey, $character->characterID);
			$xml = $ale->corp->CorporationSheet();
			$sheet = $xml->result->toArray();
			$corporation->save($sheet);
			$finishedCorps[] = $character->corporationID;
		}
		return ! (bool) JError::getError();
	}
		
}


?>