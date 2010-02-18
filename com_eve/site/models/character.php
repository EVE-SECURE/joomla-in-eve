<?php

/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Community Builder - Character Sheet
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
	protected $_character = null;
	
	protected $_context = 'com_eve.character';
	
	protected function _populateState()
	{
		$id = JRequest::getInt('characterID');
		$this->setState('character.characterID', $id);
		$params = JComponentHelper::getParams('com_eve');
		$this->setState('params', $params);
	}
	
	protected function _loadCharacter()
	{
		if (isset($this->_character)) {
			return;
		}
		$id = $this->getState('character.characterID');
		$dbo = $this->getDBO();
		$q = EveFactory::getQuery($dbo);
		$q->addTable('#__eve_characters', 'ch');
		$q->addJoin('#__eve_corporations', 'co', 'co.corporationID=ch.corporationID');
		$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
		$q->addQuery('ch.*');
		$q->addQuery('co.corporationName', 'co.ticker AS corporationTicker');
		$q->addQuery('al.allianceID', 'al.name AS allianceName', 'al.shortName AS allianceShortName');
		$q->addWhere('ch.characterID='. $id);		
		$this->_character = $q->loadObject();
	}
	
	public function setCharacterID($id)
	{
		if (!$this->__state_set) {
			// Private method to auto-populate the model state.
			$this->_populateState();
			
			$this->setState('character.characterID', $id);
			// Set the model state set flat to true.
			
			$this->__state_set = true;
		} else {
			//TODO: set error when trying to rewite corporationID
			$this->setError('');
		}
	}
	
	public function getParams()
	{
		$params = $this->getState('params');
		return $params;
	}
	
	public function getCharacter()
	{
		$this->_loadCharacter();
		return $this->_character;
	}
	
	public function getComponents()
	{
		$dbo = $this->getDBO();
		$q = EveFactory::getQuery($dbo);
		$q->addTable('#__eve_sections');
		$q->addWhere("entity = 'character'");
		$q->addWhere('published');
		$q->addOrder('ordering');
		$result = $q->loadObjectList();
		return $result;
	}
	
}