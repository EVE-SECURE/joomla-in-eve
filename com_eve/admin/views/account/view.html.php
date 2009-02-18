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

class EveViewAccount extends JView {
	
	function display($tpl = null) {
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/com_eve/assets/common.css');
		
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		JToolBarHelper::back();
		
		$model = $this->getModel();
		
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		JArrayHelper::toInteger($cid);
		
		$id = reset($cid);
		
		if ($id > 0) {
			$title = JText::_('EDIT ACCOUNT');
		} else {
			$title = JText::_('NEW ACCOUNT');
		}
		JToolBarHelper::title($title, 'account');
		
		$item = $model->getAccount($id);
		$dbo = $model->getDBO();
		$q = new JQuery($dbo);
		$q->addTable('#__users');
		$q->addQuery('id, name');
		$users = $q->loadObjectList();
		
		$nouser = array('id' => '0', 'name'=>JText::_('UNKNOWN OWNER'));
		$nouser = array('0' => JArrayHelper::toObject($nouser));
		$users = array_merge($nouser, $users);
		
		$this->assignRef('users', $users);
		$this->assignRef('item', $item);
		parent::display($tpl);
	}
}
