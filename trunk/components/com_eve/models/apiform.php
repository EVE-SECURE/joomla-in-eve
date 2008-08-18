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

class EveModelApiform  extends JModel {
	
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
				return false;
			}
			$user = JFactory::getUser($credentials['username']);
		}
		 
		$apiUserID = JArrayHelper::getValue($hash, 'apiUserID', '', 'int');
		$apiKey = JArrayHelper::getValue($hash, 'apiKey', '', 'string');
		if (!preg_match('/[a-zA-Z0-9]{64}/', $apiKey)) {
			return false;
		}
		$api = new EveHelperApi();
		$api->setCredentials($apiUserID, $apiKey);
		$chars = $api->getCharacters();
		if (is_null($chars)) {
			return false; 
		}
		
		$xml = new SimpleXMLElement($chars);
		
		$xchars = $xml->xpath('/eveapi/result/rowset/row');
		
		
		
		//if ($user->)
		
		$corps = array();
		foreach ($xchars as $xchar) {
			$char = $this->getTable('Chars');
			$attribs = $xchar->attributes();
			
			//print_r($attribs);
			$char->loadByEveId((string)$attribs->characterID);
			$char->userID						= $user->id;
			$char->name 				= (string)$attribs->name;
			$char->corporationID 	= (string)$attribs->corporationID;
			$char->apiUserID					= $apiUserID;
			$char->apiKey						= $apiKey;
			
			//$char->corporationName	= (string)$attribs->corporationName;
			$char->store();
			$corps[] = (string)$attribs->corporationID;
		}
		
		if (!$user->block) {
			return true;
		}
		
		JArrayHelper::toInteger($corps);
		
		if (!count($corps)) {
			return false;
		}
		
		
		$q = new JQuery();
		$q->addTable('#__eve_corporations', 'co');
		$q->addJoin('#__eve_alliances', 'al', 'al.allianceID=co.allianceID');
		$q->addQuery('co.id');
		$q->addWhere('co.corporationID IN ('.implode(',', $corps).')');
		$q->addWhere('(co.owner OR al.owner)');
		$ok = (int) $q->loadResult();
		if ($ok) {
			$user->set('block', '0');
			$user->set('activation', '');
			if (!$user->save()) {
				JError::raiseWarning( "SOME_ERROR_CODE", $user->getError() );
				return false;
			}
		} else {
			JError::raiseWarning( "SOME_ERROR_CODE_NOT_IN_OWNER_CORP", 1 );
			return false;
		}
		
		//var_dump($chars);
		//echo $chars->asXML;
		return true;
		
	}
	
	
}

?>