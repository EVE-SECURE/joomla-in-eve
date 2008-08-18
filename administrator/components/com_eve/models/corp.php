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

class EveModelCorp extends JModel {
	
	function __construct($config = array()) {
		$config['table_path'] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'tables';
		parent::__construct($config);
	}
	
	function &getCorp($id = null) {
		static $instances;
		static $eve_instances;
		
		if (!isset($instances)) {
			$instances = array();
			$eve_instances = array();
		}
		
		if (!isset($instances[$id])) {
			$instance = $this->getTable( 'Corps' );
			
			if (!$instance->load( (int) $id )) {
				return $instance;
			}
			
			$instances[$id] = &$instance;
			$eve_instances[$instance->corporationID] = &$instance; 
		}
		return $instances[$id];
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $corp_id
	 * @return TableCorps
	 */
	function &getCorpByEveId($corp_id = null) {
		static $instances;
		static $eve_instances;
		
		if (is_null($corp_id)) {
			$corp_id = self::eveCorpId();
		}
		
		if (!isset($eve_instances)) {
			$instances = array();
			$eve_instances = array();
		}
		
		if (!isset($eve_instances[$corp_id])) {
			$instance = $this->getTable( 'Corps' );
			
			if ($instance->loadByEveId( (int) $corp_id )) {
				$instances[$instance->id] = &$instance;
			}
			
			$eve_instances[$corp_id] = &$instance;
			
			
		}
		return $eve_instances[$corp_id];
	}
	
	function eveCorpId() {
		return EveHelperIgb::value('corpid');
	}
	
	/**
	 * Binds sent data to table instance
	 *
	 * @param TableEVE_Corps $instance
	 */
	function bindCorpData(&$instance) {
		$instance->corp_id 			= EveHelperIgb::value('corpid', 'int'); 
		$instance->corp_name 		= EveHelperIgb::value('corpname');
		$instance->allianceID		= EveHelperIgb::value('allianceid', 'int');
	}
	
	function apiGetCorpSheet($corps, $inner_ids = true) {
		JArrayHelper::toInteger($corps);
		
		if (!count($corps)) {
			JError::raiseWarning(500, JText::_('NO CORPORATION SELECTED'));
			return false;
		}
		$_cid = implode(',', $corps);
		if ($inner_ids) {
			$q = new JQuery();
			$q->addTable('#__eve_corporations');
			$q->addQuery('corporationID');
			$q->addWhere('id IN ('.$_cid.')');
			$corps = $q->loadResultArray();
		}
		
		$api = new EveHelperApi();
		$api->includeClass('CorporationSheet');
		
		foreach ($corps as $corp) {
			$content = $api->getCorporationSheet($corp);
			$sheet = CorporationSheet::getCorporationSheet($content);
			
			if (empty($sheet)) {
				$msg = sprintf(JText::_('GET_XML_FAILED_FOR'), $corp->corporationID, $corp->corporationName);
				JError::raiseWarning(500, $msg);
				continue;
			}
			
			$obj = $this->getCorpByEveId(JArrayHelper::getValue($sheet, 'corporationID'));
			$obj->corporationName = JArrayHelper::getValue($sheet, 'corporationName');
			$obj->ticker = JArrayHelper::getValue($sheet, 'ticker', '');
			$obj->allianceID = JArrayHelper::getValue($sheet, 'allianceID', 0);
			$obj->ceoID = JArrayHelper::getValue($sheet, 'ceoID', 0);
			$obj->stationID = JArrayHelper::getValue($sheet, 'stationID', 0);
			$obj->description = JArrayHelper::getValue($sheet, 'description', 0);
			$obj->url = JArrayHelper::getValue($sheet, 'url', 0);
			$obj->taxRate = JArrayHelper::getValue($sheet, 'taxRate', 0);
			$obj->memberCount = JArrayHelper::getValue($sheet, 'memberCount', 0);
			$obj->memberLimit = JArrayHelper::getValue($sheet, 'memberLimit', 0);
			$obj->shares = JArrayHelper::getValue($sheet, 'shares', 0);
			$obj->store();
		}
		return ! (bool) JError::getError();
	}
	
	function apiGetAuthorizedCorporationSheet($chars) {
		if (!count($chars)) {
			JError::raiseWarning(500, JText::_('NO CHARACTER SELECTED'));
			return false;
		}
		
		$api = new EveHelperApi();
		$api->includeClass('CorporationSheet');
		foreach ($chars as $char) {
			if (!($char->apiUserID && $char->apiKey)) {
				$msg = sprintf(JText::_('MISSING_CREDENTIALS'), $char->characterID,  $char->name);
				JError::raiseWarning(500, $msg);
				continue;
			}
			$api->setCredentials($char->apiUserID, $char->apiKey, $char->characterID);
			$content = $api->getCorporationSheet();
			$sheet = CorporationSheet::getCorporationSheet($content);
			
			if (empty($sheet)) {
				$msg = sprintf(JText::_('GET_XML_FAILED_FOR'), $char->characterID,  $char->name);
				JError::raiseWarning(500, $msg);
				continue;
			}
			
			$obj = $this->getCorpByEveId($sheet['corporationID']);
			$obj->corporationName = JArrayHelper::getValue($sheet, 'corporationName');
			$obj->ticker = JArrayHelper::getValue($sheet, 'ticker', '');
			$obj->allianceID = JArrayHelper::getValue($sheet, 'allianceID', 0);
			$obj->ceoID = JArrayHelper::getValue($sheet, 'ceoID', 0);
			$obj->stationID = JArrayHelper::getValue($sheet, 'stationID', 0);
			$obj->description = JArrayHelper::getValue($sheet, 'description', 0);
			$obj->url = JArrayHelper::getValue($sheet, 'url', 0);
			$obj->taxRate = JArrayHelper::getValue($sheet, 'taxRate', 0);
			$obj->memberCount = JArrayHelper::getValue($sheet, 'memberCount', 0);
			$obj->memberLimit = JArrayHelper::getValue($sheet, 'memberLimit', 0);
			$obj->shares = JArrayHelper::getValue($sheet, 'shares', 0);
			$obj->store();
		}
		return ! (bool) JError::getError();
	}
	
	function apiGetMemberTracking($corps, $inner_ids = true) {
		$dbo = JFactory::getDBO();
		
		if (!count($corps)) {
			JError::raiseWarning(500, JText::_('NO CORPORATION SELECTED'));
			return false;
		}
		
		$_cid = implode(',', $corps);
		
		$q = new JQuery();
		$q->addTable('#__eve_corporations', 'co');
		$q->addJoin('#__eve_characters', 'ch', 'co.ceoID=ch.characterID', 'left');
		$q->addQuery('ch.*, co.corporationID, co.corporationName');
		if ($inner_ids) {
			$q->addWhere('co.id IN ('.$_cid.')');
		} else {
			$q->addWhere('co.corporationID IN ('.$_cid.')');
		}
		$ceos = $q->loadObjectList();
		
		$api = new EveHelperApi();
		$api->includeClass('MemberTrack');
		
		$charModel  = $this->getInstance('Char', 'EveModel');
		foreach ($ceos as $ceo) {
			if (!($ceo->apiUserID && $ceo->apiKey)) {
				$msg = sprintf(JText::_('MISSING_CREDENTIALS'), $ceo->corporationID,  $ceo->corporationName);
				JError::raiseWarning(500, $msg);
				continue;
			}
			$api->setCredentials($ceo->apiUserID, $ceo->apiKey, $ceo->characterID);
			$content = $api->getMemberTracking();
			$members = MemberTrack::getMembers($content);
			
			if (empty($members)) {
				$msg = sprintf(JText::_('GET_XML_FAILED_FOR'), $ceo->corporationID,  $ceo->corporationName);
				JError::raiseWarning(500, $msg);
				continue;
			}
			
			$sql = 'UPDATE `#__eve_characters` SET `corporationID`=0 WHERE `corporationID`='.intval($ceo->corporationID);
			$dbo->setQuery($sql);
			$dbo->query();
			
			foreach($members as $member) {
				$obj = $charModel->getCharByEveId($member['characterID']);
				$obj->corporationID = $ceo->corporationID;
				
	            $obj->name = JArrayHelper::getValue($member, 'name', '');
	            $obj->startDateTime  = JArrayHelper::getValue($member, 'startDateTime', 0);
	            $obj->baseID = JArrayHelper::getValue($member, 'baseID', 0);
	            $obj->title = JArrayHelper::getValue($member, 'title', '');
	            $obj->logonDateTime = JArrayHelper::getValue($member, 'logonDateTime', null);
	            $obj->logoffDateTime = JArrayHelper::getValue($member, 'logoffDateTime', null);
	            $obj->locationID = JArrayHelper::getValue($member, 'locationID', 0);
	            $obj->shipTypeID = JArrayHelper::getValue($member, 'shipTypeID', 0);
	            $obj->roles = JArrayHelper::getValue($member, 'roles', 0);
	            $obj->grantableRoles = JArrayHelper::getValue($member, 'grantableRoles', 0);
	            $obj->store();
			}
			
		}
		return ! ((bool) JError::getError());
	}
	
}
?>