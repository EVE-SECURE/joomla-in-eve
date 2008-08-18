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

class EveModelChar extends JModel {
	
	function __construct($config = array()) {
		$config['table_path'] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'tables';
		parent::__construct($config);
	}
	
	function &getChar($id = null) {
		static $instances;
		static $eve_instances;
		
		if (!isset($instances)) {
			$instances = array();
			$eve_instances = array();
		}
		
		if (!isset($instances[$id])) {
			$instance = $this->getTable( 'Chars' );
			
			if (!$instance->load( (int) $id )) {
				return $instance;
			}
			
			$instances[$id] = &$instance;
			$eve_instances[$instance->characterID] = &$instance; 
		}
		return $instances[$id];
	}
	
	function &getCharByEveId($characterID = null) {
		static $instances;
		static $eve_instances;
		
		if (is_null($characterID)) {
			$characterID = self::eveCharId();
		}
		
		if (!isset($eve_instances)) {
			$instances = array();
			$eve_instances = array();
		}
		
		if (!isset($eve_instances[$characterID])) {
			$instance = $this->getTable( 'Chars' );
			
			if ($instance->loadByEveId( (int) $characterID )) {
				$instances[$instance->id] = &$instance;
			}
			
			$eve_instances[$characterID] = &$instance;
			
			
		}
		return $eve_instances[$characterID];
	}
	
	function eveCharId() {
		return EveHelperIgb::value('charid');
	}
	
	function apiGetCharacterSheet($chars, $inner_ids = true) {
		JArrayHelper::toInteger($chars);
		
		if (!count($chars)) {
			JError::raiseWarning(500, JText::_('NO CHARACTER SELECTED'));
			return false;
		}
		
		$api = new EveHelperApi();
		$api->includeClass('CharacterSheet');
		foreach ($chars as $char) {
			$obj = $this->getTable('Chars');
			if ($inner_ids) {
				$obj = $this->getChar($char);
			} else {
				$obj = $this->getCharByEveId($char);
			}
			if (!($obj->apiUserID && $obj->apiKey)) {
				$msg = sprintf(JText::_('MISSING_CREDENTIALS'), $obj->characterID,  $obj->name);
				JError::raiseWarning(500, $msg);
				continue;
			}
			$api->setCredentials($obj->apiUserID, $obj->apiKey, $obj->characterID);
			$content = $api->getCharacterSheet();
			$sheet = CharacterSheet::getCharacterSheet($content);
			if (empty($sheet)) {
				$msg = sprintf(JText::_('GET_XML_FAILED_FOR'), $obj->characterID,  $obj->name);
				JError::raiseWarning(500, $msg);
				continue;
			}
			
			$obj->name = $sheet['info']['name'];
			$obj->race = $sheet['info']['race'];
			$obj->bloodLine = $sheet['info']['bloodLine'];
			$obj->gender = $sheet['info']['gender'];
			$obj->corporationID = $sheet['info']['corporationID'];
			$obj->balance = $sheet['info']['balance'];
			$obj->store();
		}
		return ! (bool) JError::getError();
	}
	
	function apiGetCorporationSheet($chars, $inner_ids = true) {
		JArrayHelper::toInteger($chars);
		
		if (!count($chars)) {
			JError::raiseWarning(500, JText::_('NO CHARACTER SELECTED'));
			return false;
		}
		$_chars = array();
		foreach ($chars as $char) {
			$obj = $this->getTable('Chars');
			if ($inner_ids) {
				$obj = $this->getChar($char);
			} else {
				$obj = $this->getCharByEveId($char);
			}
			$_chars[] = $obj;
		}
		
		$corpModel  = $this->getInstance('Corp', 'EveModel');
		return $corpModel->apiGetAuthorizedCorporationSheet($_chars);
	}
		
}


?>