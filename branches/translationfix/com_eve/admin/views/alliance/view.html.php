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

class EveViewAlliance extends JView {
	
	function display($tpl = null) {
		$item = $this->get('Item');
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->assignRef('item', $item);
		parent::display($tpl);
		$this->_setToolBar();
	}
	
	protected function _setToolBar()
	{
		JRequest::setVar('hidemainmenu', 1);
		if ($this->item->allianceID > 0) {
			$title = JText::_('EDIT ALLIANCE');
		} else {
			$title = JText::_('NEW ALLIANCE');
		}
		JToolBarHelper::title($title, 'alliance');
		
		JToolBarHelper::apply('alliance.apply');
		JToolBarHelper::save('alliance.save');
		JToolBarHelper::addNew('alliance.save2new', 'Save and new');
		if ($this->item->allianceID > 0) {
			JToolBarHelper::cancel('alliance.cancel');
		} else {
			JToolBarHelper::cancel('alliance.cancel', 'Close');
		}
		
	}

}

