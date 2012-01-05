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
		$this->registerTask('unsetOwner', 'setOwner');
	}

	/**
	 * Dummy method to redirect back to standard controller
	 *
	 * @return	void
	 */
	public function display()
	{
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=alliances', false));
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
				$app->setUserState('com_eve.edit.alliance.allianceID', $return);
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

	function delete() {
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('alliance');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');

		// Sanitize the input.
		JArrayHelper::toInteger($cid);

		// Attempt to delete the alliances
		$return = $model->delete($cid);

		// Delete the weblinks
		if ($return === false) {
			$message = JText::sprintf('JError_Occurred', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=alliances', $message, 'error');
			return false;
		}
		else {
			$message = JText::sprintf('JSuccess_N_items_deleted', $return);
			$this->setRedirect('index.php?option=com_eve&view=alliances', $message);
			return true;
		}
	}

	function getAllianceList() {
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));
		// Set default redirect
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=alliances', false));
		// Get application
		$app = JFactory::getApplication();

		$model = $this->getModel('Alliance', 'EveModel');
		$result = $model->apiGetAllianceList();

		foreach ($model->getErrors() as $error) {
			$app->enqueueMessage($error, 'error');
		}
		if ($result) {
			$app->enqueueMessage(JText::_('ALLIANCES SUCCESSFULLY IMPORTED'));
		}
	}

	function getAllianceMembers() {
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));
		// Set default redirect
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=alliances', false));
		// Get application
		$app = JFactory::getApplication();

		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		// Sanitize the input.
		JArrayHelper::toInteger($cid);
		if (!count($cid)) {
			JError::raiseWarning(500, JText::_('Com_Eve_Error_No_Item_Selected'));
			return false;
		}

		//@todo: message, error output
		$model = $this->getModel('Alliance', 'EveModel');
		$model->apiGetAllianceMembers($cid);

		foreach ($model->getErrors() as $error) {
			$app->enqueueMessage($error, 'error');
		}
		$app->enqueueMessage(JText::sprintf('%s CORPORATION SUCCESSFULLY IMPORTED', $count));

	}

	public function setOwner()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));
		// Set default redirect
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=alliances', false));
		// Get application
		$app = JFactory::getApplication();

		$isOwner = strtolower($this->_task) == 'setowner';
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		// Sanitize the input.
		JArrayHelper::toInteger($cid);
		if (!count($cid)) {
			JError::raiseWarning(500, JText::_('Com_Eve_Error_No_Item_Selected'));
			return false;
		}

		$model = $this->getModel('Alliance', 'EveModel');
		if ($isOwner) {
			$model->apiGetAllianceMembers($cid);
		}
		$result = $model->setOwner($cid, $isOwner);
		foreach ($model->getErrors() as $error) {
			$app->enqueueMessage($error, 'error');
		}

		$corporationIDs = $model->getAllianceMemberIDs($cid);
		//print_r($corporationIDs);die;
		if ($corporationIDs) {
			$model = $this->getModel('Corporation', 'EveModel');
			$result = $model->setOwner($corporationIDs, $isOwner, false);
			foreach ($model->getErrors() as $error) {
				$app->enqueueMessage($error, 'error');
			}
			if ($result) {
				$app = JFactory::getApplication();
				if ($isOwner) {
					$app->enqueueMessage(JText::sprintf('Com_Eve_N_Corporations_Set_As_Owner', $result));
				} else {
					$app->enqueueMessage(JText::sprintf('Com_Eve_N_Corporations_Unset_As_Owner', $result));
				}
			}
		}

	}

	public function search()
	{
		$model = $this->getModel('Alliances', 'EveModel', array('ignore_request' => true));
		$model->setState('filter.search', JRequest::getString('filter_search', '', 'request'));
		$model->setState('filter.fullsearch', 0);
		$model->setState('list.ordering', 'al.name');
		$model->setState('list.query', 'al.allianceID, al.name');
		$list = $model->getItems();
		echo json_encode($list);
	}
}
