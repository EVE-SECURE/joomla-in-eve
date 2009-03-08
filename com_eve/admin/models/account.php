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

class EveModelAccount extends EveModel {
	
	function __construct($config = array()) {
		$config['table_path'] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'tables';
		parent::__construct($config);
	}
	
	function getAccount($id = null) {
		return $this->getInstance('Account', $id);
	}
	
	function getApiStates() {
		return $this->getEnumOptions('#__eve_accounts', 'apiStatus');
	}
	
	function store() {
		global $mainframe;
		
		$account = $this->getAccount(JRequest::getInt('userID'));
		$post = JRequest::get('post');
		
		if (!$account->bind( $post )) {
			return JError::raiseWarning( 500, $account->getError() );
		}
		
		//check status of apiKey
		if ($account->apiStatus == 'Unknown') {
			JPluginHelper::importPlugin('eveapi');
			$dispatcher =& JDispatcher::getInstance();
			$ale = $this->getAleEVEOnline();
			try {
				$ale->setCredentials($account->userID, $account->apiKey);
				$xml = $ale->account->Characters();
				$dispatcher->trigger('accountCharacters', 
					array($xml, $ale->isFromCache(), array('userID' => $account->userID)));
				
				$charRow = reset($xml->result->characters->getIterator());
				if ($charRow !== false) {
					$ale->setCharacterID($charRow->characterID);
					$xml = $ale->char->AccountBalance();
					$dispatcher->trigger('charAccountBalance', 
						array($xml, $ale->isFromCache(), array('characterID' => $charRow->characterID)));
				}
				$account->apiStatus = 'Full';
				$mainframe->enqueueMessage(JText::_('API key offers full access'));
			}
			catch (AleExceptionEVEAuthentication $e) {
				$this->updateApiStatus($account, $e->getCode());
				switch ($account->apiStatus) {
					case 'Limited':
						JError::raiseNotice(0, JText::_('API key offers limited access'));
						break;
					case 'Invalid':
						JError::raiseWarning(0, JText::_('API key is invalid'));
						break;
					case 'Inactive':
						JError::raiseWarning(0, JText::_('Account is inactive'));
						break;
				}
			}
			catch (RuntimeException $e) {
				JError::raiseWarning($e->getCode(), $e->getMessage());
			}
			catch (Exception $e) {
				JError::raiseError($e->getCode(), $e->getMessage());
			}
		}
		
		if (!$account->check()) {
			return JError::raiseWarning( 500, $account->getError() );
		}	
		if (!$account->store()) {
			return JError::raiseWarning( 500, $account->getError() );
		}
		$mainframe->enqueueMessage(JText::_('ACCOUNT STORED'));
		
	}
	
	function apiGetCharacters($cid) {
		global $mainframe;
		JArrayHelper::toInteger($cid);
		
		if (!count($cid)) {
			JError::raiseWarning(500, JText::_('NO ACCOUNTS SELECTED'));
			return false;
		}
		JPluginHelper::importPlugin('eveapi');
		$dispatcher =& JDispatcher::getInstance();
		
		$count = 0;
		$ale = $this->getAleEVEOnline();
		foreach ($cid as $userID) {
			try {
				$account = $this->getAccount($userID);
				$ale->setCredentials($account->userID, $account->apiKey);
				$xml = $ale->account->Characters();
				$dispatcher->trigger('accountCharacters', 
					array($xml, $ale->isFromCache(), array('userID' => $userID)));
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
			$mainframe->enqueueMessage(JText::_('CHARACTERS FROM ACCOUNT SUCCESSFULLY IMPORTED'));
		}
		if ($count > 1) {
			$mainframe->enqueueMessage(JText::sprintf('CHARACTERS FROM %s ACCOUNTS SUCCESSFULLY IMPORTED', $count));
		}
	}
	
}
