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
	
			$userID = JArrayHelper::getValue($hash, 'userID', '', 'int');
			$apiKey = JArrayHelper::getValue($hash, 'apiKey', '', 'string');
			if (!preg_match('/[a-zA-Z0-9]{64}/', $apiKey)) {
				JError::raiseWarning('SOME_ERROR_CODE', JText::_("INVALID API KEY FORMAT"));
				return false;
			}

			$account = $this->getInstance('Account', $userID);
			
			$ale = $this->getAleEVEOnline();
			$ale->setCredentials($userID, $apiKey);
			$xml = $ale->account->Characters();
			
			JPluginHelper::importPlugin('eveapi');
			$dispatcher =& JDispatcher::getInstance();
			
			$dispatcher->trigger('accountCharacters', 
				array($xml, $ale->isFromCache(), array('userID' => $userID)));
			
			$corps = array();
			foreach ($xml->result->characters as $characterID => $character) {
				$corps[] = (string) $character->corporationID;;
			}
			
			try {
				$charRow = reset($xml->result->characters->getIterator());
				if ($charRow !== false) {
					$ale->setCharacterID($charRow->characterID);
					$xml = $ale->char->AccountBalance();
					$dispatcher->trigger('charAccountBalance', 
						array($xml, $ale->isFromCache(), array('characterID' => $charRow->characterID)));
				}
				$account->apiStatus = 'Full';
				$app->enqueueMessage(JText::_('API key offers full access'));
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
				
			$account->apiKey = $apiKey;
			$account->store();
			
			$dispatcher->trigger('onRegisterAccount', array($account->userID, $account->apiStatus));
			
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
				JError::raiseWarning('SOME_ERROR_CODE', JText::_("NO CHARACTER IS MEMBER OF NO CORPORATION"));
			}
	
			$q = $this->getQuery();
			$q->addTable('#__eve_corporations', 'co');
			$q->addJoin('#__eve_alliances', 'al', 'al.allianceID=co.allianceID');
			$q->addQuery('co.corporationID');
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
			$app->enqueueMessage(JText::_('Account unblocked'));
			return true;
			
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
