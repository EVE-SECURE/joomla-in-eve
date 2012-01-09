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

class EveModelUser extends JModelItem
{
	protected function _populateState()
	{
		global $option;
		$this->setState('entity', 'user');
		$user = JFactory::getUser();
		$id = intval($user->id);
		$this->setState('user.id', $id);
		$app = JFactory::getApplication();
		$params = $app->getParams();
		if ($option != 'com_eve') {
			$eveparams = JComponentHelper::getParams('com_eve');
			$params->merge($eveparams);
		}

		$this->setState('params', $params);
	}

	protected function _loadItem($pk) {
		return JFactory::getUser($pk);
	}

	public function setUserID($id)
	{
		if (!$this->__state_set) {
			// Private method to auto-populate the model state.
			$this->_populateState();
				
			$this->setState('user.id', $id);
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

	function getCharacters()
	{
		$id = $this->getState('user.id');
		if (!$id) {
			throw new Exception(JText::_('ALERTNOTAUTH'), 403);
		}
		$dbo = $this->getDBO();
		$q = EveFactory::getQuery($dbo);
		$q->addTable('#__eve_characters', 'c');
		$q->addJoin('#__eve_corporations', 'co', 'c.corporationID=co.corporationID');
		$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
		$q->addQuery('c.*');
		$q->addQuery('co.corporationName');
		$q->addQuery('al.allianceID, al.name AS allianceName');
		$q->addWhere('c.user_id=%s', $id);
		$q->addOrder('name');
		return $q->loadObjectList('characterID');

	}

	public function getComponents()
	{
		$user = JFactory::getUser();
		$dbo = $this->getDBO();
		$q = EveFactory::getQuery($dbo);
		$q->addTable('#__eve_sections');
		$q->addWhere("entity = 'user'");
		$q->addWhere('published');
		$q->addWhere('access <='.intval($user->get('aid')));
		$q->addOrder('title');
		$result = $q->loadObjectList();
		return $result;
	}

}