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

class EveViewCorporations extends JView {
	public $state;
	public $items;
	public $pagination;


	function display($tpl = null) {
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->assignRef('state',			$state);
		$this->assignRef('items',			$items);
		$this->assignRef('pagination',		$pagination);

		parent::display($tpl);
		$this->_setToolbar();
	}


	/**
	 * Setup the Toolbar
	 */
	protected function _setToolbar()
	{
		$title = JText::_('EVE CORPORATION MANAGER');
		JToolBarHelper::title($title, 'corporation');
		JToolBarHelper::custom('corporation.getCorporationSheet', 'corporation', 'corporation', 'Corporation Sheet', true);
		JToolBarHelper::custom('corporation.getMemberTracking', 'character', 'character', 'Member Tracking', true);
		JToolBarHelper::spacer();
		JToolBarHelper::custom('corporation.setowner', 'default', 'default', 'Com_Eve_Set_Owner', true);;
		JToolBarHelper::custom('corporation.unsetowner', 'default-cancel', 'default-cancel', 'Com_Eve_Unset_Owner', true);;
		JToolBarHelper::spacer();
		JToolBarHelper::addNew('corporation.add');
		JToolBarHelper::editList('corporation.edit');
		JToolBarHelper::deleteList('', 'corporation.delete');
	}

}
