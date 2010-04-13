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

jimport('joomla.application.component.controller');

class EveControllerCharacter extends EveController {
	
	public function __construct($config = array()) 
	{
		parent::__construct($config);
	}
	
	public function display($cachable = false)
	{
		$document =& JFactory::getDocument();

		$viewType	= $document->getType();
		$viewName	= JRequest::getCmd( 'view', $this->getName() );
		$viewLayout	= JRequest::getCmd( 'layout', 'default' );

		$view = & $this->getView($viewName, $viewType, '', array( 'base_path'=>$this->_basePath));

		// Get/Create the character model
		$characterModel = & $this->getModel('Character');
		$view->setModel($characterModel, true);

		// Get/Create the schedule model
		$character = $characterModel->getItem();
		$user = JFactory::getUser();
		if ($user->get('id') && $user->get('id') == $character->ownerID) {
			$apischeduleModel = & $this->getModel('Apischedule');
			// Push the model into the view
			$view->setModel($apischeduleModel);
			
			$sectionaccessModel = & $this->getModel('Sectionaccess');
			// Push the model into the view
			$view->setModel($sectionaccessModel);
		}

		// Set the layout
		$view->setLayout($viewLayout);

		// Display the view
		if ($cachable && $viewType != 'feed') {
			global $option;
			$cache =& JFactory::getCache($option, 'view');
			$cache->get($view, 'display');
		} else {
			$view->display();
		}
	}
	
	public function apischedule()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$user = JFactory::getUser();
		if (!$user->get('id')) {
			JError::raiseError(403, JText::_('ALERTNOTAUTH'));
			return false;
		}
		// Get/Create the character model
		$characterModel = & $this->getModel('Character');
		$character = $characterModel->getItem();
		if ($character->ownerID != $user->get('id')) {
			JError::raiseError(403, JText::_('ALERTNOTAUTH'));
			return false;
		}
		
		$apischeduleModel = & $this->getModel('Apischedule');
		$data = JRequest::getVar('apischedule', array(), 'post', 'array');
		$apischeduleModel->setCharacterList($data, $character);
		$msg = JText::_('Com_Eve_Api_Calls_Stored');
		//TODO: check/display errors
		$this->setRedirect(EveRoute::character($character, null, null, false), $msg);
		
	}
	
	public function sectionaccess()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$user = JFactory::getUser();
		if (!$user->get('id')) {
			JError::raiseError(403, JText::_('ALERTNOTAUTH'));
			return false;
		}
		// Get/Create the character model
		$characterModel = & $this->getModel('Character');
		$character = $characterModel->getItem();
		if ($character->ownerID != $user->get('id')) {
			JError::raiseError(403, JText::_('ALERTNOTAUTH'));
			return false;
		}
		
		$sectionaccessModel = & $this->getModel('Sectionaccess');
		$data = JRequest::getVar('sectionaccess', array(), 'post', 'array');
		$sectionaccessModel->setCharacterList($data, $character);
		$msg = JText::_('Com_Eve_Character_Section_Access_Stored');
		//TODO: check/display errors
		$this->setRedirect(EveRoute::character($character, null, null, false), $msg);
		
	}
	
}
