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

class EveControllerEncryption extends EveController {
	
	public function configure()
	{
		$user = JFactory::getUser();
		if (!$user->authorize('com_config', 'manage')) {
			$this->setRedirect(JRoute::_('index.php'), JText::_('ALERTNOTAUTH'));
			return;
		}
		$document =& JFactory::getDocument();

		$viewType	= $document->getType();
		$viewName	= 'Encryption';
		$viewLayout	= JRequest::getCmd( 'layout', 'default' );

		$view = & $this->getView($viewName, $viewType, '', array( 'base_path'=>$this->_basePath));
		
		$view = $this->getView('Encryption');
		$model = $this->getModel('Encryption');
		$view->setModel($model);
		
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		if (!$model->setConfiguration($data)) {
			$view->setLayout('default');
			$view->display();
			return;
		}
		if ($model->writeConfiguration()) {
			$this->setRedirect(JRoute::_('index.php?option=com_eve'), JText::_('Config saved'));
		} else {
			$view->setLayout('config');
			$view->display();
		}
		
	}
	
	
	
}