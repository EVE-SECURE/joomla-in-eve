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

class EveModelApiform  extends EveModel {
	
	function processForm($hash) {
		$user = JFactory::getUser();
		
		if (!$user->id) {
			$credentials = array();
			$credentials['username'] = JRequest::getVar('username', '', 'method', 'username');
			$credentials['password'] = JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);
			
			$options = array();
			jimport( 'joomla.user.authentication');
			$authenticate = & JAuthentication::getInstance();
			$response	  = $authenticate->authenticate($credentials, $options);
			if ($response->status !== JAUTHENTICATE_STATUS_SUCCESS) {
				JError::raiseWarning('SOME_ERROR_CODE', JText::_("AUTHENTICATION FAILED"));
				return false;
			}
			$user = JFactory::getUser($credentials['username']);
		}

		$userID = JArrayHelper::getValue($hash, 'userID', '', 'int');
		$apiKey = JArrayHelper::getValue($hash, 'apiKey', '', 'string');
		if (!preg_match('/[a-zA-Z0-9]{64}/', $apiKey)) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_("INVALID API KEY FORMAT"));
			return false;
		}
		$ale = $this->getAleEVEOnline();
		$ale->setCredentials($userID, $apiKey);
		
		$xml = $ale->account->Characters();
		
		$dbo = $this->getDBO();
		$sql = 'UPDATE #__eve_characters SET userID=0 WHERE userID='.$userID;
		$dbo->Execute($sql);
		
		$corps = array();
		foreach ($xml->result->characters->toArray() as $characterID => $array) {
			$character = $this->getInstance('Character', $characterID);
			$character->userID = $userID;
			$character->save($array);
			
			$corporation = $this->getInstance('Corporation', $array['corporationID']);
			if (!$corporation->isLoaded()) {
				$corporation->save($array);
			}
		}
		
		
		$account = $this->getInstance('Account', $userID);
		$account->apiKey = $apiKey;
		$account->store();
		
		if ($account->owner > 0 && $account->owner != $user->id) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_("ACCOUNT ALREADY ASSIGNED TO ANOTHER OWNER"));
			return false;
		}
		
		$account->owner = $user->id;
		$account->store();
		
		if (!$user->block) {
			return true;
		}
		
		if (!count($corps)) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_("NO CHARACTER IS MEMBER OF OWNER CORPORATION"));
		}

		$q = $this->getQuery();
		$q->addTable('#__eve_corporations', 'co');
		$q->addJoin('#__eve_alliances', 'al', 'al.allianceID=co.allianceID');
		$q->addQuery('co.id');
		$q->addWhere('co.corporationID IN ('.implode(',', $corps).')');
		$q->addWhere('(co.owner OR al.owner)');
		$ok = (int) $q->loadResult();
		if (!$ok) {
			JError::raiseWarning( "SOME_ERROR_CODE", JText::_("NO CHARACTER IS MEMBER OF OWNER CORPORATION") );
			return false;
		}
		$user->set('block', '0');
		$user->set('activation', '');
		if (!$user->save()) {
			JError::raiseWarning( "SOME_ERROR_CODE", $user->getError() );
			return false;
		}
		
		return true;
	}
	
}
