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

class EveViewAccess extends JView {
	public $state;
	public $items;
	public $pagination;

	
	function display($tpl = null) {
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$groups		= $this->get('Groups');
		$pagination	= $this->get('Pagination');
		$characterGroups = $this->get('CharacterGroups', 'Sectionaccess');
		$corporationGroups = $this->get('CorporationGroups', 'Sectionaccess');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->assignRef('state',			$state);
		$this->assignRef('items',			$items);
		$this->assignRef('groups',			$groups);
		$this->assignRef('characterGroups',	$characterGroups);
		$this->assignRef('corporationGroups',	$corporationGroups);
		$this->assignRef('pagination',		$pagination);
		
		parent::display($tpl);
		$this->_setToolbar();
	}

	
	/**
	 * Setup the Toolbar
	 */
	protected function _setToolbar()
	{
		JRequest::setVar('hidemainmenu', 1);
		$title = JText::_('COM_EVE_ACCESS_CONTROL');
		JToolBarHelper::title($title, 'encryption');
		JToolBarHelper::apply('access.apply');
		JToolBarHelper::cancel('access.cancel');
	}
	
}
