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
	
	/**
	 * Get TableCharacter
	 *
	 * @param int $id
	 * @return TableCharacter
	 */
	function getCharacter($id = null) {
		return $this->getInstance('Character', $id);
	}
	
	function store() {
		global $mainframe;
		
		$character = $this->getCharacter(JRequest::getInt('characterID'));
		$post = JRequest::get('post');
		
		if (!$character->bind( $post )) {
			return JError::raiseWarning( 500, $character->getError() );
		}
		
		if (!$character->check()) {
			return JError::raiseWarning( 500, $character->getError() );
		}
		
		if (!$character->store()) {
			return JError::raiseWarning( 500, $character->getError() );
		}
		
		$mainframe->enqueueMessage(JText::_('CHARACTER STORED'));
	}
	
	function apiGetCharacterSheet($cid) {
		global $mainframe;
		JArrayHelper::toInteger($cid);
		
		if (!count($cid)) {
			JError::raiseWarning(500, JText::_('NO CHARACTER SELECTED'));
			return false;
		}
		
		JPluginHelper::importPlugin('eveapi');
		$dispatcher =& JDispatcher::getInstance();
		
		$count = 0;
		$ale = $this->getAleEVEOnline();
		foreach ($cid as $characterID) {
			try {
				$character  = $this->getCharacter($characterID);
				$account = $this->getInstance('Account', $character->userID);
				if (!$account->apiKey) {
					continue;
				}
				$ale->setCredentials($account->userID, $account->apiKey, $character->characterID);
				$xml = $ale->char->CharacterSheet();
				$dispatcher->trigger('charCharacterSheet', 
					array($xml, $ale->isFromCache(), array('characterID'=>$character->characterID)));
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
			$mainframe->enqueueMessage(JText::_('CHARACTER SHEET SUCCESSFULLY IMPORTED'));
		}
		if ($count > 1) {
			$mainframe->enqueueMessage(JText::sprintf('%s CHARACTER SHEETS SUCCESSFULLY IMPORTED', $count));
		}
	}
	
	function apiGetCorporationSheet($cid) {
		global $mainframe;
		JArrayHelper::toInteger($cid);
		
		if (!count($cid)) {
			JError::raiseWarning(500, JText::_('NO CHARACTER SELECTED'));
			return false;
		}
		
		JPluginHelper::importPlugin('eveapi');
		$dispatcher =& JDispatcher::getInstance();
		
		$count = 0;
		$ale = $this->getAleEVEOnline();
		$finishedCorps = array();
		foreach ($cid as $characterID) {
			try {
				$character = $this->getCharacter($characterID);
				if (in_array($character->corporationID, $finishedCorps)) {
					continue;
				}
				$account = $this->getInstance('Account', $character->userID);
				$ale->setCredentials($account->userID, $account->apiKey, $character->characterID);
				$xml = $ale->corp->CorporationSheet();
				$dispatcher->trigger('corpCorporationSheet', 
					array($xml, $ale->isFromCache(), array('characterID'=>$character->characterID)));
				$finishedCorps[] = $character->corporationID;
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
			$mainframe->enqueueMessage(JText::_('CORPORATION SHEET SUCCESSFULLY IMPORTED'));
		}
		if ($count > 1) {
			$mainframe->enqueueMessage(JText::sprintf('%s CORPORATION SHEETS SUCCESSFULLY IMPORTED', $count));
		}
	}
		
}
