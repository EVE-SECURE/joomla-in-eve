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

class EveModelSectionCorporationRoles extends JModelItem {
	protected $_context = 'sectionCorporation';

	public function validate(&$data)
	{
		$data['corporationID'] = JArrayHelper::getValue($data, 'corporationID', 0, 'int');
		$data['section'] = JArrayHelper::getValue($data, 'section', 0, 'int');
		$roles = JArrayHelper::getValue($data, 'roles', array());
		$acl = EveFactory::getACL();
		$data['roles'] = $acl->sumRoles($roles);
		$data['access'] = EveACL::CORPORATION_MEMBER_ROLES;
		return $data;
	}

	protected function _populateState()
	{
		$app = &JFactory::getApplication();

		$this->setState('context', $this->_context);

		// Load state from the request.
		if (!($section = (int) $app->getUserState($this->_option.'.'.$this->_context.'.section'))) {
			$section = (int) JRequest::getInt('section');
		}
		$this->setState($this->_context.'.section', $section);

		// Load state from the request.
		if (!($corporationID = (int) $app->getUserState($this->_option.'.'.$this->_context.'.corporationID'))) {
			$corporationID = (int) JRequest::getInt('corporationID');
		}
		$this->setState($this->_context.'.corporationID', $corporationID);

		// Load the parameters.
		if ($app->isSite()) {
			$params	= $app->getParams();
		} else {
			$params = JComponentHelper::getParams($this->_option);
		}
		$this->setState('params', $params);
	}

	protected function _loadItem()
	{
		$dbo = $this->getDBO();
		$section = $this->getState($this->_context.'.section');
		$corporationID = $this->getState($this->_context.'.corporationID');
		$q = EveFactory::getQuery($dbo);
		$q->addTable('#__eve_sections', 's');
		$q->addJoin('#__eve_section_corporation_access', 'cs', 'cs.section=s.id AND corporationID='.$corporationID);
		$q->addWhere("entity = 'corporation'");
		$q->addWhere('published = 1');
		$q->addWhere('s.id='.$section);
		$q->addQuery('s.title, s.id AS section, cs.access, cs.roles');
		$result = $q->loadObject();
		return $result;
	}

	public function save($data)
	{
		$corporationID = JArrayHelper::getValue($data, 'corporationID', 0, 'int');
		$section = JArrayHelper::getValue($data, 'section', 0, 'int');
		$roles = JArrayHelper::getValue($data, 'roles');
		$access = JArrayHelper::getValue($data, 'access');

		$dbo = $this->getDBO();
		$sql = sprintf('INSERT INTO #__eve_section_corporation_access (section, corporationID, access, roles) VALUES (%1$s, %2$s, %3$s, %4$s) '.
			'ON DUPLICATE KEY UPDATE access = %3$s, roles=%4$s', $section, $corporationID, $access, $roles);
		$dbo->setQuery($sql);
		$dbo->query();
		if ($error = $dbo->getError()) {
			$this->setError($error);
			return false;
		}

		return true;
	}

}