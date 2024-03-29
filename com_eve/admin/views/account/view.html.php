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
	public $apiStates = null;
	public $item = null;


	function display($tpl = null) {
		$item = $this->get('Item');
		$apiStates = $this->get('ApiStates');;

		$config = EveFactory::getConfig();
		if (!intval($config->getValue('encryption.showapikey'))) {
			$item->apiKey = '';
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->assignRef('apiStates', $apiStates);
		$this->assignRef('item', $item);
		parent::display($tpl);
		$this->_setToolbar();
	}

	protected function _setToolbar() {
		JRequest::setVar('hidemainmenu', 1);

		if ($this->item->userID > 0) {
			$title = JText::_('EDIT ACCOUNT');
		} else {
			$title = JText::_('NEW ACCOUNT');
		}
		JToolBarHelper::title($title, 'account');

		JToolBarHelper::apply('account.apply');
		JToolBarHelper::save('account.save');
		JToolBarHelper::addNew('account.save2new', 'Save and new');
		if ($this->item->userID > 0) {
			JToolBarHelper::cancel('account.cancel');
		} else {
			JToolBarHelper::cancel('account.cancel', 'Close');
		}
	}

}
