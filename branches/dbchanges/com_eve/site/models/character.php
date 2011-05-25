<?php

/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Core
 * @copyright	Copyright (C) 2009 Pavol Kovalik. All rights reserved.
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

class EveModelCharacter extends JModelItem
{
	protected function _populateState()
	{
		global $option;
		$this->setState('entity', 'character');
		$id = JRequest::getInt('characterID');
		$this->setState('character.id', $id);
		$app = JFactory::getApplication();
		$params = $app->getParams();
		if ($option != 'com_eve') {
			$eveparams = JComponentHelper::getParams('com_eve');
			$params->merge($eveparams);
		}
		
		$this->setState('params', $params);
	}
	
	protected function _loadItem($id)
	{
		$dbo = $this->getDBO();
		$q = EveFactory::getQuery($dbo);
		$q->addTable('#__eve_characters', 'ch');
		$q->addJoin('#__eve_corporations', 'co', 'co.corporationID=ch.corporationID');
		$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
		$q->addJoin('#__eve_accounts', 'ac', 'ch.userID=ac.userID');
		$q->addJoin('#__users', 'owner', 'ac.owner=owner.id');
		$q->addQuery('ch.*');
		$q->addQuery('co.corporationName', 'co.ticker AS corporationTicker');
		$q->addQuery('al.allianceID', 'al.name AS allianceName', 'al.shortName AS allianceShortName');
		$q->addQuery('owner.id AS ownerID', 'owner.name AS ownerName');
		$q->addWhere('ch.characterID='. intval($id));
		$data = $q->loadObject();
		
		if ($error = $dbo->getErrorMsg()) {
			throw new Exception($error, 500);
		}

		if (empty($data)) {
			throw new Exception(JText::_('Com_Eve_Error_Character_not_found'), 404);
		}

		return $data;
	}
	
	public function setCharacterID($id)
	{
		if (!$this->__state_set) {
			// Private method to auto-populate the model state.
			$this->_populateState();
			
			$this->setState('character.id', $id);
			// Set the model state set flat to true.
			
			$this->__state_set = true;
		} else {
			$this->setState('character.id', $id);
		}
	}
	
	public function getParams()
	{
		$params = $this->getState('params');
		return $params;
	}
	
	public function getComponents()
	{
		$item = $this->getItem();
		$dbo = $this->getDBO();
		
		$q = EveFactory::getQuery($dbo);
		$q->addTable('#__eve_sections', 's');
		$q->addWhere("s.entity = 'character'");
		$q->addWhere('s.published');
		$q->addOrder('s.title');
		$acl = EveFactory::getACL();
		$acl->setCharacterQuery($q, 's.', $item);
		
		$q->addOrder('ordering');
		$result = $q->loadObjectList();
		return $result;
	}
	
	public function getIsUsersCharacter()
	{
		$acl = EveFactory::getACL();
		$characterIDs = $acl->getUserCharacterIDs();
		$item = $this->getItem();
		return $item && in_array($item->characterID, $characterIDs);
	}
	
}