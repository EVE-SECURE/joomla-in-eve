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

jimport( 'joomla.application.component.view');

class EveViewRoles extends JView {
	public $state;
	public $item;

	
	function display($tpl = null) {
		$state		= $this->get('State');
		$item 		= $this->get('Item');
		
		$acl = EveFactory::getACL();
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->assignRef('state',	$state);
		$this->assignRef('item',	$item);
		$this->assignRef('acl',		$acl);
		
		parent::display($tpl);
		$this->_setToolbar();
	}

	
	/**
	 * Setup the Toolbar
	 */
	protected function _setToolbar()
	{
		JRequest::setVar('hidemainmenu', 1);
		$title = JText::_('Roles').' - '.$this->item->title;
		JToolBarHelper::title($title, 'encryption');
		JToolBarHelper::save('roles.savesection');
		JToolBarHelper::apply('roles.applysection');
		JToolBarHelper::cancel('roles.cancelsection', 'Close');
	}
	
	
	protected function displayItem($name, $value)
	{
		$this->assign('itemName', $name);
		$this->assign('itemValue', $value);
		return $this->loadTemplate('item');
	}
}
