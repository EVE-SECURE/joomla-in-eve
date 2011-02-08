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

jimport('joomla.application.component.view');

class EveViewUser extends JView 
{
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		
		$params = $this->get('Params');
		$user = $this->get('Item');
		$characters = $this->get('Characters');
		$components = $this->get('Components');

		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		if (is_object($menu)
				&& JArrayHelper::getValue($menu->query, 'option') == 'com_eve'
				&& JArrayHelper::getValue($menu->query, 'view') == 'user') {
			$menu_params = new JParameter($menu->params);
			if (!$menu_params->get('page_title')) {
				$params->set('page_title', JText::_('COM_EVE_USER_CHARACTERS'));
			}
		} else {
			$params->set('page_title', JText::_('COM_EVE_USER_CHARACTERS'));
		}
		$document->setTitle($params->get('page_title'));
		
		$this->assignRef('components', $components);
		$this->assignRef('user', $user);
		$this->assignRef('params', $params);
		$this->assignRef('characters', $characters);
		
		parent::display();
		$this->_setPathway();
	}
	
	protected function _setPathway()
	{
	}
}