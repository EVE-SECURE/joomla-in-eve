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

class EvechartrackingViewDbcheck extends JView {
	function display($tmpl = null) {
		global $mainframe;
		$template = $mainframe->getTemplate();
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/com_eve/assets/common.css');
		$document->addStyleDeclaration('.icon-32-refresh { background-image: url(templates/'.$template.'/images/toolbar/icon-32-refresh.png); }');
		
		$title = JText::_('EVE CHARACTER TRACKING');
		JToolBarHelper::title($title, 'char');
		JToolBarHelper::custom('', 'refresh', 'refresh', 'Refresh', false);
		
		$model = $this->getModel();
		$tables = $model->getTableCheck();
		
		
		$this->assignRef('tables', $tables);
		
		parent::display($tmpl);

	}
}

?>