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

jimport('joomla.application.component.model');

class EveModelEve extends JModel {
	
	public function getIcons() {
		$user = JFactory::getUser();
		$result = array();
		$result[] = array('icon'=>'icon-48-character.png', 'view'=>'characters', 'caption'=>JText::_('Characters'));
		$result[] = array('icon'=>'icon-48-corporation.png', 'view'=>'corporations', 'caption'=>JText::_('Corporations'));
		$result[] = array('icon'=>'icon-48-alliance.png', 'view'=>'alliances', 'caption'=>JText::_('Alliances'));
		$result[] = array('icon'=>'icon-48-account.png', 'view'=>'accounts', 'caption'=>JText::_('Accounts'));
		$result[] = array('icon'=>'icon-48-schedule.png', 'view'=>'schedule', 'caption'=>JText::_('Schedule'));
		if ($user->authorize('com_config', 'manage') && !file_exists(JPATH_COMPONENT_ADMINISTRATOR.DS.'configs'.DS.'encryption.php')) {
			$result[] = array('icon'=>'icon-48-encryption.png', 'view'=>'encryption', 'caption'=>JText::_('API Key Encryption'));
		}
		return $result;
		
	}
}