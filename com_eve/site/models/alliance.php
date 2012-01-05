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

class EveModelAlliance extends JModelItem
{
	protected function _populateState()
	{
		global $option;
		$this->setState('entity', 'alliance');
		$id = JRequest::getInt('allianceID');
		$this->setState('alliance.id', $id);
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
		$q->addTable('#__eve_alliances', 'al');
		$q->addJoin('#__eve_corporations', 'co', 'al.executorCorpID=co.corporationID');
		$q->addQuery('al.*');
		$q->addQuery('co.corporationName AS executorCorpName', 'co.ticker AS executorCorpTicker');
		$q->addWhere('al.allianceID='. intval($id));
		$data = $q->loadObject();

		if ($error = $dbo->getErrorMsg()) {
			throw new Exception($error, 500);
		}

		if (empty($data)) {
			throw new Exception(JText::_('Com_Eve_Error_Alliance_not_found'), 404);
		}

		return $data;
	}

	public function setAllianceID($id)
	{
		if (!$this->__state_set) {
			// Private method to auto-populate the model state.
			$this->_populateState();
				
			$this->setState('alliance.id', $id);
			// Set the model state set flat to true.
				
			$this->__state_set = true;
		} else {
			$this->setState('alliance.id', $id);
		}

	}

	public function getParams()
	{
		$params = $this->getState('params');
		return $params;
	}

	function getMembers()
	{
		$id = $this->getState('alliance.id');
		$dbo = $this->getDBO();
		$q = EveFactory::getQuery($dbo);
		$q->addTable('#__eve_corporations');
		$q->addWhere('allianceID=%s', $id);
		$q->addOrder('corporationName');
		return $q->loadObjectList();
	}

	public function getComponents()
	{
		$user = JFactory::getUser();
		$dbo = $this->getDBO();
		$q = EveFactory::getQuery($dbo);
		$q->addTable('#__eve_sections');
		$q->addWhere("entity = 'alliance'");
		$q->addWhere('published');
		$q->addWhere('access <='.intval($user->get('aid')));
		$q->addOrder('title');
		$result = $q->loadObjectList();
		return $result;
	}

}