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

class EveModelSectionaccess extends EveModel 
{
	protected function _populateState()
	{
		$id = JRequest::getInt('characterID');
		$this->setState('character.id', $id);
	}
	
	public function getCharacterList()
	{
		$dbo = $this->getDBO();
		$characterID = $this->getState('character.id');
		$q = EveFactory::getQuery($dbo);
		$q->addTable('#__eve_sections', 's');
		$q->addJoin('#__eve_section_character_access', 'cs', 'cs.section=s.id AND characterID='.$characterID);
		$q->addWhere("entity = 'character'");
		$q->addWhere('published = 1');
		$q->addQuery('s.title, s.id AS section, cs.access');
		$list = $q->loadObjectList('section');
		return $list;
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
	
	public function setCharacterList($data, $character)
	{
		$characterID = $this->getState('character.id');
		$list = $this->getCharacterList();
		$dbo = $this->getDBO();
		foreach ($list as $i => $item) {
			$itemData = JArrayHelper::getValue($data, $i);
			if (!is_array($itemData)) {
				continue;
			}
			if (JArrayHelper::getValue($itemData, 'section') != $item->section) {
				continue;
			}
			$access = JArrayHelper::getValue($itemData, 'access');
			$access = is_numeric($access) ? (int) $access : 'NULL';
			$sql = sprintf('INSERT INTO #__eve_section_character_access (section, characterID, access) VALUES (%1$s, %2$s, %3$s) '.
				'ON DUPLICATE KEY UPDATE access = %3$s',
				$item->section, $characterID, $access);
			$dbo->setQuery($sql);
			$dbo->query();
		}
	}
	
	public function getCharacterGroups()
	{
		$dbo = $this->getDBO();

		$query = 'SELECT id AS value, name AS text'
		. ' FROM #__groups'
		. ' ORDER BY id'
		;
		$dbo->setQuery( $query );
		$groups = $dbo->loadObjectList();
		jimport('joomla.html.html');
		array_unshift($groups, JHTML::_('select.option', 'NULL', 'Com_Eve_Access_Option_Default'));
		$groups[] = JHTML::_('select.option', EveACL::CHARACTER_IN_OWNER_CORPORATION, 'Com_Eve_Access_Option_Corporation');
		$groups[] = JHTML::_('select.option', EveACL::CHARACTER_OWNED_BY_USER, 'Com_Eve_Access_Option_Personal');
		
		return $groups;
		
	}
	

}