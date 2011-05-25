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

class EveViewEncryption extends JView {
	protected $algorithms = null;
	protected $modes = null;
	protected $config = null;
	protected $path = null;
	
	public function display($tpl = null) {
		$values = array();
		$layout = $this->getLayout();
		if ($layout == 'config') {
			$values['config'] = $this->get('ConfigContent');
			$values['path'] = $this->get('Path');
		} else {
			$values['algorithms'] = $this->get('Algorithms');
			$values['modes'] = $this->get('Modes');
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->assign($values);
		parent::display($tpl);
		$this->_setToolbar();
	}

	protected function _setToolbar() {
		JRequest::setVar('hidemainmenu', 1);

		$title = JText::_('API Key Encryption');
		JToolBarHelper::title($title, 'encryption');
		$layout = $this->getLayout();
		
		if ($layout == 'config') {
			JToolBarHelper::cancel('encryption.cancel', JText::_('Close'));
		} else {
			JToolBarHelper::apply('encryption.configure');
			JToolBarHelper::cancel('encryption.cancel');
		}
	}
	
}