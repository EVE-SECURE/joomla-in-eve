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

class EveModelAlliance extends EveModel {
	
	function __construct($config = array()) {
		$config['table_path'] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'tables';
		parent::__construct($config);
	}
	
	function getAlliance($id = null) {
		return $this->getInstance('Alliance', $id);
	}
	
	function eveAllianceId() {
		return EveHelperIgb::value('allianceid');
	}
	
	/**
	 * Binds sent data to table instance
	 *
	 * @param TableEVE_Alliances $instance
	 */
	function bindAllianceData(&$instance) {
		$instance->allianceID 			= EveHelperIgb::value('allianceid', 'int'); 
		$instance->allianceName 		= EveHelperIgb::value('alliancename');
	}
	
	function apiGetAllianceList() {
		$ale = $this->getAleEVEOnline();
		$xml = $ale->eve->AllianceList();
		
		JPluginHelper::importPlugin('eveapi');
		$dispatcher =& JDispatcher::getInstance();
		
		$dispatcher->trigger('eveAlianceList', 
			array($xml, $ale->isFromCache(), array()));
		
		return true;
	}
	
	function apiGetAllianceMembers($cid) {

		JArrayHelper::toInteger($cid);
		
		if (!count($cid)) {
			JError::raiseWarning(500, JText::_('NO ALLIANCE SELECTED'));
			return false;
		}
		
		$ale = $this->getAleEVEOnline();
		$xml = $ale->eve->AllianceList();
		
		JPluginHelper::importPlugin('eveapi');
		$dispatcher =& JDispatcher::getInstance();
		
		$dispatcher->trigger('eveAlianceList', 
			array($xml, $ale->isFromCache(), array()));
		
		$conditions = array();
		foreach ($cid as $allianceID) {
			$conditions[] = "@allianceID='$allianceID'";
		}
		$_condition = implode(' or ', $conditions);
		$corps = $xml->xpath('/eveapi/result/rowset/row['.$_condition.']/rowset/row');
		
		foreach ($corps as $corp) {
			$corporationID = (int) $corp->corporationID;
			$corporation = $this->getInstance('Corporation', $corporationID);
			$xml = $ale->corp->CorporationSheet(array('corporationID' => $corporationID), ALE_AUTH_NONE);
			$dispatcher->trigger('corpCorporationSheet', 
				array($xml, $ale->isFromCache(), array()));
			}
		return true;
	}
	
}
