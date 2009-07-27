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

class EveControllerAccount extends EveController {
	
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		
		$this->registerTask('save2new', 'save');
		$this->registerTask('apply', 'save');
		$this->registerTask('get_characters', 'getCharacters');
	}
	
	/**
	 * Dummy method to redirect back to standard controller
	 *
	 * @return	void
	 */
	public function display()
	{
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=accounts', false));
	}
	
	function add() {
		$app = &JFactory::getApplication();

		// Clear the level edit information from the session.
		$app->setUserState('com_eve.edit.account.userID', null);
		$app->setUserState('com_eve.edit.account.data', null);

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=account&layout=edit', false));
	}
	
	function edit() {
		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('Account', 'EveModel');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');
		
		$previousId		= (int) $app->getUserState('com_eve.edit.account.userID');
		$userID		= (int) (count($cid) ? $cid[0] : JRequest::getInt('userID'));
		// If account ids do not match, checkin previous account.
		if (($previousId > 0) && ($userID != $previousId)) {
			if (!$model->checkin($previousId)) {
				// Check-in failed, go back to the account and display a notice.
				$message = JText::sprintf('JError_Checkin_failed', $model->getError());
				$this->setRedirect('index.php?option=com_eve&view=account&layout=edit', $message, 'error');
				return false;
			}
		}
		
		// Attempt to check-out the new account for editing and redirect.
		if (!$model->checkout($userID)) {
			// Check-out failed, go back to the list and display a notice.
			$message = JText::sprintf('JError_Checkout_failed', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=account&userID='.$userID, $message, 'error');
			return false;
		}
		else {
			// Check-out succeeded, push the new account id into the session.
			$app->setUserState('com_eve.edit.account.userID',	$userID);
			$app->setUserState('com_eve.edit.account.data', null);
			$this->setRedirect('index.php?option=com_eve&view=account&layout=edit');
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
		$model	= &$this->getModel('Account', 'EveModel');

		// Get the account id.
		$userID = (int) $app->getUserState('com_eve.edit.account.userID');

		// Attempt to check-in the current account.
		if ($userID) {
			if (!$model->checkin($userID)) {
				// Check-in failed, go back to the account and display a notice.
				$message = JText::sprintf('JError_Checkin_failed', $model->getError());
				$this->setRedirect('index.php?option=com_eve&view=account&layout=edit', $message, 'error');
				return false;
			}
		}
		// Clean the session data and redirect.
		$app->setUserState('com_eve.edit.account.userID',		null);
		$app->setUserState('com_eve.edit.account.data',	null);
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=accounts', false));
	}

	/**
	 * Method to save a account.
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
		$model	= $this->getModel('Account');
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
			$app->setUserState('.edit.account.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_eve&view=account&layout=edit', false));
			return false;
		}

		// Attempt to save the account.
		$return = $model->save($data);

		if ($return === false) {
			// Save failed, go back to the account and display a notice.
			$message = JText::sprintf('JError_Save_failed', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=account&layout=edit', $message, 'error');
			return false;
		}

		// Save succeeded, check-in the account.
		if (!$model->checkin()) {
			// Check-in failed, go back to the account and display a notice.
			$message = JText::sprintf('JError_Checkin_saved', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=account&layout=edit', $message, 'error');
			return false;
		}

		$this->setMessage(JText::_('JController_Save_success'));

		// Redirect the user and adjust session state based on the chosen task.
		switch ($this->_task) {
			case 'apply':
				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=com_eve&view=account&layout=edit', false));
				break;

			case 'save2new':
				// Clear the member id and data from the session.
				$app->setUserState('com_eve.edit.account.userID', null);
				$app->setUserState('com_eve.edit.account.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=com_eve&view=account&layout=edit', false));
				break;

			default:
				// Clear the member id and data from the session.
				$app->setUserState('com_eve.edit.account.userID', null);
				$app->setUserState('com_eve.edit.account.data', null);

				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option=com_eve&view=accounts', false));
				break;
		}
	}	
	
	function delete() {
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('Account');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');

		// Sanitize the input.
		JArrayHelper::toInteger($cid);

		// Attempt to delete the accounts
		$return = $model->delete($cid);

		// Delete the weblinks
		if ($return === false) {
			$message = JText::sprintf('JError_Occurred', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=accounts', $message, 'error');
			return false;
		}
		else {
			$message = JText::sprintf('JSuccess_N_items_deleted', $return);
			$this->setRedirect('index.php?option=com_eve&view=accounts', $message);
			return true;
		}
	}
	
	function getCharacters() {
		$model = & $this->getModel('Account');
		$cid = JRequest::getVar('cid', array(), '', 'array');
		
		// Sanitize the input.
		JArrayHelper::toInteger($cid);
		
		//@todo: message, error output
		$model->apiGetCharacters($cid);
		
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=accounts', false));
	}

}
