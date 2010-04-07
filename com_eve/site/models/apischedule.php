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

class EveModelApischedule extends EveModel 
{
	protected function _populateState()
	{
		$id = JRequest::getInt('characterID');
		$this->setState('character.id', $id);
	}
	
	public function getCharacterList()
	{
		$characterID = $this->getState('character.id');
		$q = $this->getQuery();
		$q->addTable('#__eve_apicalls', 'a');
		$q->addJoin('#__eve_schedule', 's', 's.apicall=a.id AND characterID='.$characterID);
		$q->addWhere("a.type='char'");
		$q->addQuery("a.id AS apicall, s.id, s.published, a.call, s.next");
		$list = $q->loadObjectList();
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
	

}