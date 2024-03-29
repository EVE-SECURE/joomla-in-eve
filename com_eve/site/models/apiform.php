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

	public function processForm($hash) {
		$app = JFactory::getApplication();
		try {
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

			$keyID = JArrayHelper::getValue($hash, 'keyID', '', 'int');
			$vCode = JArrayHelper::getValue($hash, 'vCode', '', 'string');
			if (!preg_match('/[a-zA-Z0-9]{64}/', $vCode)) {
				JError::raiseWarning('SOME_ERROR_CODE', JText::_("Com_Eve_Invalid_VCode_Format"));
				return false;
			}
			$ale = $this->getAleEVEOnline();
			$ale->setKey($keyID, $vCode);
			$xml = $ale->account->APIKeyInfo();
			
			if ((string) $xml->result->key->type == 'Corporation') {
				JError::raiseWarning('SOME_ERROR_CODE', JText::_("Com_Eve_Corporation_Key_Type_Not_Allowed"));
				return false;
			}
			
			if (count($xml->result->key->characters) == 0) {
				JError::raiseWarning('SOME_ERROR_CODE', JText::_("Com_Eve_No_Character_Assigned_To_Key"));
				return false;
			}

			if ($user->block) {
				if (!$this->unblock($user, $xml)) {
					return false;
				}
			}
			
			$apikey = $this->getInstance('Apikey', $keyID);
			$apikey->vCode = $vCode;
			
			JPluginHelper::importPlugin('eveapi');
			$dispatcher =& JDispatcher::getInstance();
			$options = array('user_id' => $user->id);
			$dispatcher->trigger('accountAPIKeyInfo', array($apikey, $xml, $ale->isFromCache(), $options));
			return true;
				
		}
		catch (AleExceptionEVEAuthentication $e) {
			JError::raiseWarning($e->getCode(), $e->getMessage());
			return false;
		}
		catch (RuntimeException $e) {
			JError::raiseWarning($e->getCode(), $e->getMessage());
			return false;
		}
		catch (Exception $e) {
			JError::raiseError($e->getCode(), $e->getMessage());
			return false;
		}

	}
	
	protected function unblock($user, $xml)
	{
		$app = JFactory::getApplication();
		$characters = array();
		$corporations = array();
		foreach ($xml->result->key->characters as $character) {
			$characters[] = $character->characterID;
			$corporations[] = $character->corporationID;
		}
		
		$q = $this->getQuery();
		$q->addTable('#__eve_corporations', 'co');
		$q->addJoin('#__eve_alliances', 'al', 'al.allianceID=co.allianceID');
		$q->addQuery('co.corporationID');
		$q->addWhere('co.corporationID IN ('.implode(',', $corporations).')');
		$q->addWhere('(co.owner OR al.owner)');
		$ok = (int) $q->loadResult();
		if (!$ok) {
			JError::raiseWarning( "SOME_ERROR_CODE", JText::_("Com_Eve_No_Character_Is_Member_Of_Owner_Corporation") );
			return false;
		}
		
		$q = $this->getQuery();
		$q->addTable("#__eve_characters");
		$q->addWhere('characterID IN ('.implode(',', $characters).')');
		$characterList = $q->loadObjectList();
		foreach ($characterList as $character) {
			if (($character->user_id > 0) && ($character->user_id != $user->id)) {
				JError::raiseWarning( "SOME_ERROR_CODE", JText::_("Com_Eve_Some_Of_Characters_Is_Already_Assigned_To_Another_User") );
				return false;
			}
		}
		
		$user->set('block', '0');
		$user->set('activation', '');
		if (!$user->save()) {
			JError::raiseWarning( "SOME_ERROR_CODE", $user->getError() );
			return false;
		}
		$app->enqueueMessage(JText::_('Account unblocked'));
		return true;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function _populateState()
	{
		$params		= &JComponentHelper::getParams('com_eve');
		// Load the parameters.
		$this->setState('params', $params);
	}


	public function getParams() {
		return $this->getState('params');
	}
}
