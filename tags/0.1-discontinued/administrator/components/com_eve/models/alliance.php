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

class EveModelAlliance extends JModel {
	
	function __construct($config = array()) {
		$config['table_path'] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'tables';
		parent::__construct($config);
	}
	
	function &getAlliance($id = null) {
		static $instances;
		static $eve_instances;
		
		if (!isset($instances)) {
			$instances = array();
			$eve_instances = array();
		}
		
		if (!isset($instances[$id])) {
			$instance = $this->getTable( 'Alliances' );
			
			if (!$instance->load( (int) $id )) {
				return $instance;
			}
			
			$instances[$id] = &$instance;
			$eve_instances[$instance->allianceID] = &$instance; 
		}
		return $instances[$id];
	}
	
	function &getAllianceByEveId($allianceID = null) {
		static $instances;
		static $eve_instances;
		
		if (is_null($allianceID)) {
			$allianceID = self::eveAllianceId();
		}
		
		if (!isset($eve_instances)) {
			$instances = array();
			$eve_instances = array();
		}
		
		if (!isset($eve_instances[$allianceID])) {
			$instance = $this->getTable( 'Alliances' );
			
			if ($instance->loadByEveId( (int) $allianceID )) {
				$instances[$instance->id] = &$instance;
			}
			
			$eve_instances[$allianceID] = &$instance;
			
			
		}
		return $eve_instances[$allianceID];
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
		$instance->name 		= EveHelperIgb::value('alliancename');
	}
	
	function apiGetAllianceList() {
		$api = new EveHelperApi();
		$content = $api->getAllianceList();

		
		if (is_null($content)) {
			JError::raiseWarning(500, JText::_('GET_XML_FAILED'));
			return false;
		}
		
		$xml = new SimpleXMLElement($content);

		$xallies = $xml->xpath('/eveapi/result/rowset/row');
		
		foreach ($xallies as $xally) {
			$attribs = $xally->attributes();
			
			$ally = $this->getTable('Alliances');
			$ally->allianceID = (string) $attribs->allianceID;
			$ally->loadByEveId();
			$ally->name 			= (string) $attribs->name;
			$ally->shortName 		= (string) $attribs->shortName;
			$ally->executorCorpID 	= (string) $attribs->executorCorpID;
			$ally->memberCount 		= (string) $attribs->memberCount;
			$ally->store();
			
		}
		return true;
	}
	
	function apiGetAllianceMembers($allies, $inner_ids = true) {

		JArrayHelper::toInteger($allies);
		
		if (!count($allies)) {
			JError::raiseWarning(500, JText::_('NO ALLIANCE SELECTED'));
			return false;
		}
		if ($inner_ids) {
			$_cid = implode(',', $allies);
			$q = new JQuery();
			$q->addTable('#__eve_alliances');
			$q->addQuery('allianceID');
			$q->addWhere('id IN ('.$_cid.')');
			$allies = $q->loadResultArray();
		}
		
		$api = new EveHelperApi();
		$content = $api->getAllianceList();

		if (is_null($content)) {
			JError::raiseWarning(500, JText::_('GET_XML_FAILED'));
			return false;
		}
		
		$conditions = array();
		foreach ($allies as $ally) {
			$conditions[] = "@allianceID='$ally'";
		}
		$_condition = implode(' or ', $conditions);
		
		$xml = new SimpleXMLElement($content);

		$xcorps = $xml->xpath('/eveapi/result/rowset/row['.$_condition.']/rowset/row/@corporationID');
		JArrayHelper::toInteger($xcorps);
		
		$corp_model = $this->getInstance('Corp', 'EveModel');
		$result = $corp_model->apiGetCorpSheet($xcorps, false);
		return $result;		
	}
	
}


?>