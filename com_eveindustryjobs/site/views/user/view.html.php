<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Industry Jobs
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

require_once JPATH_COMPONENT_SITE.DS.'view.php';

class EveindustryjobsViewUser extends EveindustryjobsView
{
	public $user;

	protected function _setEntity($user, $params)
	{
		$document = JFactory::getDocument();
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		if (is_object($menu)
		&& JArrayHelper::getValue($menu->query, 'option') == 'com_eveindustryjobs'
		&& JArrayHelper::getValue($menu->query, 'view') == 'user') {
			$menu_params = new JParameter($menu->params);
			if (!$menu_params->get('page_title')) {
				$params->set('page_title',	JText::_('Com_Eveindustryjobs_Industry_Jobs_Title'));
			}
		} else {
			$params->set('page_title',JText::_('Com_Eveindustryjobs_Industry_Jobs_Title'));
		}
		$document->setTitle($params->get('page_title'));
		$this->assignRef('user', $user);

		$characters = $this->get('Characters');
		$this->assignRef('characters', $characters);
	}

	protected function _setPathway()
	{
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		if ($menu && $menu->component == 'com_eveindustryjobs') {
			return;
		}

		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		$pathway->addItem(JText::_('Com_Eveindustryjobs_Industry_Jobs_Title'),
		EveRoute::_('userindustryjobs'));
	}
}
