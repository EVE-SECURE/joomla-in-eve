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

class EveControllerAlliance extends EveController {
	
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		
		$this->registerTask('save2new', 'save');
		$this->registerTask('apply', 'save');
		$this->registerTask('get_alliance_list', 'getAllianceList');
		$this->registerTask('get_alliance_members', 'getAllianceMembers');
	}
	
	function add() {
		$app = &JFactory::getApplication();

		// Clear the level edit information from the session.
		$app->setUserState('com_eve.edit.alliance.allianceID', null);
		$app->setUserState('com_eve.edit.alliance.data', null);

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=alliance&layout=edit', false));
	}
	
	function edit() {
		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('Alliance', 'EveModel');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');
		
		$previousId		= (int) $app->getUserState('com_eve.edit.alliance.allianceID');
		$allianceID		= (int) (count($cid) ? $cid[0] : JRequest::getInt('allianceID'));
		// If alliance ids do not match, checkin previous alliance.
		if (($previousId > 0) && ($allianceID != $previousId)) {
			if (!$model->checkin($previousId)) {
				// Check-in failed, go back to the alliance and display a notice.
				$message = JText::sprintf('JError_Checkin_failed', $model->getError());
				$this->setRedirect('index.php?option=com_eve&view=alliance&layout=edit', $message, 'error');
				return false;
			}
		}
		
		// Attempt to check-out the new alliance for editing and redirect.
		if (!$model->checkout($allianceID)) {
			// Check-out failed, go back to the list and display a notice.
			$message = JText::sprintf('JError_Checkout_failed', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=alliance&allianceID='.$allianceID, $message, 'error');
			return false;
		}
		else {
			// Check-out succeeded, push the new alliance id into the session.
			$app->setUserState('com_eve.edit.alliance.allianceID',	$allianceID);
			$app->setUserState('com_eve.edit.alliance.data', null);
			$this->setRedirect('index.php?option=com_eve&view=alliance&layout=edit');
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
		$model	= &$this->getModel('Alliance', 'EveModel');

		// Get the alliance id.
		$allianceID = (int) $app->getUserState('com_eve.edit.alliance.allianceID');

		// Attempt to check-in the current alliance.
		if ($allianceID) {
			if (!$model->checkin($allianceID)) {
				// Check-in failed, go back to the alliance and display a notice.
				$message = JText::sprintf('JError_Checkin_failed', $model->getError());
				$this->setRedirect('index.php?option=com_eve&view=alliance&layout=edit', $message, 'error');
				return false;
			}
		}
		// Clean the session data and redirect.
		$app->setUserState('com_eve.edit.alliance.allianceID',		null);
		$app->setUserState('com_eve.edit.alliance.data',	null);
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=alliances', false));
	}

	/**
	 * Method to save a alliance.
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
		$model	= $this->getModel('Alliance');
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
			$app->setUserState('com_eve.edit.alliance.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_eve&view=alliance&layout=edit', false));
			return false;
		}

		// Attempt to save the alliance.
		$return = $model->save($data);

		if ($return === false) {
			// Save failed, go back to the alliance and display a notice.
			$message = JText::sprintf('JError_Save_failed', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=alliance&layout=edit', $message, 'error');
			return false;
		}

		// Save succeeded, check-in the alliance.
		if (!$model->checkin()) {
			// Check-in failed, go back to the alliance and display a notice.
			$message = JText::sprintf('JError_Checkin_saved', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=alliance&layout=edit', $message, 'error');
			return false;
		}

		$this->setMessage(JText::_('JController_Save_success'));

		// Redirect the user and adjust session state based on the chosen task.
		switch ($this->_task) {
			case 'apply':
				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=com_eve&view=alliance&layout=edit', false));
				break;

			case 'save2new':
				// Clear the member id and data from the session.
				$app->setUserState('com_eve.edit.alliance.allianceID', null);
				$app->setUserState('com_eve.edit.alliance.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=com_eve&view=alliance&layout=edit', false));
				break;

			default:
				// Clear the member id and data from the session.
				$app->setUserState('com_eve.edit.alliance.allianceID', null);
				$app->setUserState('com_eve.edit.alliance.data', null);

				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option=com_eve&view=alliances', false));
				break;
		}
	}
		
	function remove() {
		JRequest::checkToken() or die( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_eve&view=alliances' );
		
		$db 			=& JFactory::getDBO();
		$cid 			= JRequest::getVar( 'cid', array(), '', 'array' );
		$model 			= & $this->getModel('Alliance', 'EveModel');
		$table 			= $model->getTable('Alliances');

		JArrayHelper::toInteger( $cid );
		
		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an Alliance to delete', true ) );
		}
		
		foreach ($cid as $id) {
			$table->delete($id);
		}
		
		$url = JRoute::_('index.php?option=com_eve&view=alliances', false);
		$this->setRedirect($url, JText::_('ALLIANCE DELETED'));
	}
	
	function getAllianceList() {
		$model = & $this->getModel('Alliance', 'EveModel');
		$model->apiGetAllianceList();
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=alliances', false));
	}
	
	function getAllianceMembers() {
		$model = & $this->getModel('Alliance', 'EveModel');
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		$model->apiGetAllianceMembers($cid);
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=alliances', false));
	}
	
}
