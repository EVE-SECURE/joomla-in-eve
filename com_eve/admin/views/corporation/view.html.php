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

class EveViewCorporation extends JView {
	public $item;
	
	function display($tpl = null) {
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/com_eve/assets/common.css');
		
		$item = $this->get('Item');
		$sectionaccess = $this->get('CorporationList', 'Sectionaccess');
		$groups = $this->get('CorporationGroups', 'Sectionaccess');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->assignRef('item', $item);
		$this->assignRef('sectionaccess', $sectionaccess);
		$this->assignRef('groups', $groups);
		
		parent::display($tpl);
		$this->_setToolbar();
	}

	protected function _setToolbar() {
		JRequest::setVar('hidemainmenu', 1);

		if ($this->item->corporationID > 0) {
			$title = JText::_('EDIT CORPORATION');
		} else {
			$title = JText::_('NEW CORPORATION');
		}
		JToolBarHelper::title($title, 'corporation');
		
		JToolBarHelper::apply('corporation.apply');
		JToolBarHelper::save('corporation.save');
		JToolBarHelper::addNew('corporation.save2new', 'Save and new');
		if ($this->item->corporationID > 0) {
			JToolBarHelper::cancel('corporation.cancel');
		} else {
			JToolBarHelper::cancel('corporation.cancel', 'Close');
		}
	}
}
