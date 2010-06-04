<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Character Tracking
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

jimport('joomla.application.component.view');

class EvechartrackingViewEvechartracking extends JView {
	function display($tmpl = null) {
		$title = JText::_('Com_Evechartracking_Character_Tracking_Title');
		JToolBarHelper::title($title, 'corporation');
		JToolBarHelper::preferences('com_evechartracking', 480, 640);
		JToolBarHelper::custom('', 'refresh', 'refresh', 'Com_Evechartracking_Task_Refresh', false);
		
		$model = $this->getModel();
		$tables = $model->getTableCheck();
		
		$this->assignRef('tables', $tables);
		
		parent::display($tmpl);

	}
}
