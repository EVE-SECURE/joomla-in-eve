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

class EveControllerCharacter extends EveController {
	
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		
		$this->registerTask('save2new', 'save');
		$this->registerTask('apply', 'save');
		$this->registerTask('get_character_sheet', 'getCharacterSheet');
		$this->registerTask('get_corporation_sheet', 'getCorporationSheet');
	}
	
	function add() {
		$app = &JFactory::getApplication();

		// Clear the level edit information from the session.
		$app->setUserState('com_eve.edit.character.characterID', null);
		$app->setUserState('com_eve.edit.character.data', null);

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=character&layout=edit', false));
	}
	
	function edit() {
		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('Character', 'EveModel');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');
		
		$previousId		= (int) $app->getUserState('com_eve.edit.character.characterID');
		$characterID		= (int) (count($cid) ? $cid[0] : JRequest::getInt('characterID'));
		// If character ids do not match, checkin previous character.
		if (($previousId > 0) && ($characterID != $previousId)) {
			if (!$model->checkin($previousId)) {
				// Check-in failed, go back to the character and display a notice.
				$message = JText::sprintf('JError_Checkin_failed', $model->getError());
				$this->setRedirect('index.php?option=com_eve&view=character&layout=edit', $message, 'error');
				return false;
			}
		}
		
		// Attempt to check-out the new character for editing and redirect.
		if (!$model->checkout($characterID)) {
			// Check-out failed, go back to the list and display a notice.
			$message = JText::sprintf('JError_Checkout_failed', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=character&characterID='.$characterID, $message, 'error');
			return false;
		}
		else {
			// Check-out succeeded, push the new character id into the session.
			$app->setUserState('com_eve.edit.character.characterID',	$characterID);
			$app->setUserState('com_eve.edit.character.data', null);
			$this->setRedirect('index.php?option=com_eve&view=character&layout=edit');
			return true;
		}		
	}

	/**
	 * Method to cancel an edit
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	public function cancel()
	{
		JRequest::checkToken() or jExit(JText::_('JInvalid_Token'));

		// Initialize variables.
		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('Character', 'EveModel');

		// Get the character id.
		$characterID = (int) $app->getUserState('com_eve.edit.character.characterID');

		// Attempt to check-in the current character.
		if ($characterID) {
			if (!$model->checkin($characterID)) {
				// Check-in failed, go back to the character and display a notice.
				$message = JText::sprintf('JError_Checkin_failed', $model->getError());
				$this->setRedirect('index.php?option=com_eve&view=character&layout=edit', $message, 'error');
				return false;
			}
		}
		// Clean the session data and redirect.
		$app->setUserState('com_eve.edit.character.characterID',		null);
		$app->setUserState('com_eve.edit.character.data',	null);
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=characters', false));
	}

	/**
	 * Method to save a character.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	public function save()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Initialize variables.
		$app	= &JFactory::getApplication();
		$model	= $this->getModel('Character');
		$data	= JRequest::getVar('jform', array(), 'post', 'array');

		// Validate the posted data.
		$data	= $model->validate($data);
		
		// Check for validation errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'notice');
				} else {
					$app->enqueueMessage($errors[$i], 'notice');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_eve.edit.character.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_eve&view=character&layout=edit', false));
			return false;
		}

		// Attempt to save the character.
		$return = $model->save($data);

		if ($return === false) {
			// Save failed, go back to the character and display a notice.
			$message = JText::sprintf('JError_Save_failed', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=character&layout=edit', $message, 'error');
			return false;
		}

		// Save succeeded, check-in the character.
		if (!$model->checkin()) {
			// Check-in failed, go back to the character and display a notice.
			$message = JText::sprintf('JError_Checkin_saved', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=character&layout=edit', $message, 'error');
			return false;
		}

		$this->setMessage(JText::_('JController_Save_success'));

		// Redirect the user and adjust session state based on the chosen task.
		switch ($this->_task) {
			case 'apply':
				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=com_eve&view=character&layout=edit', false));
				break;

			case 'save2new':
				// Clear the member id and data from the session.
				$app->setUserState('com_eve.edit.character.characterID', null);
				$app->setUserState('com_eve.edit.character.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=com_eve&view=character&layout=edit', false));
				break;

			default:
				// Clear the member id and data from the session.
				$app->setUserState('com_eve.edit.character.characterID', null);
				$app->setUserState('com_eve.edit.character.data', null);

				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option=com_eve&view=characters', false));
				break;
		}
	}	
	
	function remove() {
		JRequest::checkToken() or die( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_eve&view=characters' );
		
		$db 			=& JFactory::getDBO();
		$cid 			= JRequest::getVar( 'cid', array(), '', 'array' );
		$model 			= & $this->getModel('Char');
		$table 			= $model->getTable('Character');

		JArrayHelper::toInteger( $cid );
		
		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select a Character to delete', true ) );
		}
		
		foreach ($cid as $id) {
			$table->delete($id);
		}
		
		$url = JRoute::_('index.php?option=com_eve&view=characters', false);
		$this->setRedirect($url, JText::_('CHARACTER DELETED'));
	}
	
	
	function getCharacterSheet() {
		$model = & $this->getModel('Char', 'EveModel');
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		
		$msg = null;
		if ($model->apiGetCharacterSheet($cid)) {
			$msg = JText::_('CHARACTERS SUCCESSFULLY IMPORTED');
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=characters', false), $msg);
	}
	
	function getCorporationSheet() {
		$model = & $this->getModel('Char', 'EveModel');
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		
		if ($model->apiGetCorporationSheet($cid)) {
			$msg = JText::_('CORPORATIONS SUCCESSFULLY IMPORTED');
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=characters', false), $msg);
		
	}
	
}
