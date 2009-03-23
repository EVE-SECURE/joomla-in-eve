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
	
	/**
	 * Enter description here...
	 *
	 * @param int $id
	 * @return TableCorporation
	 */
	function getCorporation($id = null) {
		return $this->getInstance('Corporation', $id);
	}
	
	function store() {
		global $mainframe;
		
		$corporation = $this->getCorporation(JRequest::getInt('corporationID'));
		$post = JRequest::get('post');
		
		if (!$corporation->bind( $post )) {
			return JError::raiseWarning( 500, $corporation->getError() );
		}
		
		if ($corporation->standings === '') {
			$corporation->standings = null;
		}
		
		if (!$corporation->check()) {
			return JError::raiseWarning( 500, $corporation->getError() );
		}
		
		if (!$corporation->store(true)) {
			return JError::raiseWarning( 500, $corporation->getError() );
		}
		
		$mainframe->enqueueMessage(JText::_('CORPORATION STORED'));
		
	}
	
	function apiGetCorporationSheet($cid) {
		global $mainframe;
		JArrayHelper::toInteger($cid);
		
		if (!count($cid)) {
			JError::raiseWarning(500, JText::_('NO CORPORATION SELECTED'));
			return false;
		}
		JPluginHelper::importPlugin('eveapi');
		$dispatcher =& JDispatcher::getInstance();
		
		$count = 0;
		$ale = $this->getAleEVEOnline();
		foreach ($cid as $corporationID) {
			try {
				$corporation = $this->getCorporation($corporationID);
				$ceo = $this->getInstance('Character', $corporation->ceoID);
				$account = $this->getInstance('Account', $ceo->userID);
				if ($ceo->apiKey) {
					try {
						$ale->setCredentials($account->userID, $account->apiKey, $ceo->characterID);
						$xml = $ale->corp->CorporationSheet();
					}
					catch (AleExceptionEVEAuthentication $e) {
						$xml = $ale->corp->CorporationSheet(array('corporationID' => $corporationID), ALE_AUTH_NONE);
					}
				} else {
					$xml = $ale->corp->CorporationSheet(array('corporationID' => $corporationID), ALE_AUTH_NONE);
				}
				$dispatcher->trigger('corpCorporationSheet', 
					array($xml, $ale->isFromCache(), array('characterID'=>$character->characterID)));
				$count += 1;
			}
			catch (RuntimeException $e) {
				JError::raiseWarning($e->getCode(), $e->getMessage());
			}
			catch (Exception $e) {
				JError::raiseError($e->getCode(), $e->getMessage());
			}
		}
		if ($count == 1) {
			$mainframe->enqueueMessage(JText::_('CORPORATION SHEET SUCCESSFULLY IMPORTED'));
		}
		if ($count > 1) {
			$mainframe->enqueueMessage(JText::sprintf('%s CORPORATION SHEETS SUCCESSFULLY IMPORTED', $count));
		}
	}
	
	function apiGetMemberTracking($cid) {
		global $mainframe;
		JArrayHelper::toInteger($cid);
				
		if (!count($cid)) {
			JError::raiseWarning(500, JText::_('NO CORPORATION SELECTED'));
			return false;
		}
		JPluginHelper::importPlugin('eveapi');
		$dispatcher =& JDispatcher::getInstance();
		
		$count = 0;
		$ale = $this->getAleEVEOnline();
		foreach ($cid as $corporationID) {
			try {
				$corporation = $this->getCorporation($corporationID);
				$ceo = $this->getInstance('Character', $corporation->ceoID);
				$account = $this->getInstance('Account', $ceo->userID);
				
				$ale->setCredentials($account->userID, $account->apiKey, $ceo->characterID);
				$xml = $ale->corp->MemberTracking();
	
				$dispatcher->trigger('corpMemberTracking', 
					array($xml, $ale->isFromCache(), array('characterID' => $ceo->characterID, 'corporationID' => $ceo->corporationID)));
				$count += 1;
			}
			catch (AleExceptionEVEAuthentication $e) {
				$this->updateApiStatus($account, $e->getCode(), true);
				JError::raiseWarning($e->getCode(), $e->getMessage());
			}
			catch (RuntimeException $e) {
				JError::raiseWarning($e->getCode(), $e->getMessage());
			}
			catch (Exception $e) {
				JError::raiseError($e->getCode(), $e->getMessage());
			}
		}
		if ($count == 1) {
			$mainframe->enqueueMessage(JText::_('MEMBERS FROM CORPORATION SUCCESSFULLY IMPORTED'));
		}
		if ($count > 1) {
			$mainframe->enqueueMessage(JText::sprintf('MEMBERS FROM %s CORPORATIONS SUCCESSFULLY IMPORTED', $count));
		}
	}
	
}
