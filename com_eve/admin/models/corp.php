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

class EveModelCorp extends EveModel {
	
	function __construct($config = array()) {
		$config['table_path'] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'tables';
		parent::__construct($config);
	}
	
	function getCorp($id = null) {
		return $this->getInstance('Corporation', $id);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param int $id
	 * @return TableCorporation
	 */
	function getCorporation($id = null) {
		return $this->getInstance('Corporation', $id);
	}
	
	function apiGetCorporationSheet($cid) {
		JArrayHelper::toInteger($cid);
		
		if (!count($cid)) {
			JError::raiseWarning(500, JText::_('NO CORPORATION SELECTED'));
			return false;
		}
		JPluginHelper::importPlugin('eveapi');
		$dispatcher =& JDispatcher::getInstance();
		
		$ale = $this->getAleEVEOnline();
		foreach ($cid as $corporationID) {
			$corporation = $this->getCorporation($corporationID);
			$ceo = $this->getInstance('Character', $corporation->ceoID);
			$account = $this->getInstance('Account', $ceo->userID);
			
			try {
				$ale->setCredentials($account->userID, $account->apiKey, $ceo->characterID);
				$xml = $ale->corp->CorporationSheet();
			}
			catch (Exception $e) {
				$xml = $ale->corp->CorporationSheet(array('corporationID' => $corporationID), ALE_AUTH_NONE);
			}
			
			$dispatcher->trigger('corpCorporationSheet', 
				array($xml, $ale->isFromCache(), array('characterID'=>$character->characterID)));
			
		}
		return ! (bool) JError::getError();
	}
	
	function apiGetMemberTracking($cid) {
		JArrayHelper::toInteger($cid);
				
		if (!count($cid)) {
			JError::raiseWarning(500, JText::_('NO CORPORATION SELECTED'));
			return false;
		}
		JPluginHelper::importPlugin('eveapi');
		$dispatcher =& JDispatcher::getInstance();
		
		$ale = $this->getAleEVEOnline();
		foreach ($cid as $corporationID) {
			$corporation = $this->getCorporation($corporationID);
			$ceo = $this->getInstance('Character', $corporation->ceoID);
			$account = $this->getInstance('Account', $ceo->userID);
			
			$ale->setCredentials($account->userID, $account->apiKey, $ceo->characterID);
			$xml = $ale->corp->MemberTracking();

			$dispatcher->trigger('corpMemberTracking', 
				array($xml, $ale->isFromCache(), array('characterID' => $ceo->characterID, 'corporationID' => $ceo->corporationID)));
		}
		return ! (bool) JError::getError();
		
	}
	
}
