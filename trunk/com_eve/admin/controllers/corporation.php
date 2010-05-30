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

class EveControllerCorporation extends EveController {
	
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		
		$this->registerTask('save2new', 'save');
		$this->registerTask('apply', 'save');
		$this->registerTask('getCorporationSheet', 'getCorporationSheet');
		$this->registerTask('getMemberTracking', 'getMemberTracking');
	}
	
	/**
	 * Display method
	 *
	 * @return	void
	 */
	public function display($cachable = false)
	{
		$document =& JFactory::getDocument();

		$viewType	= $document->getType();
		$viewName	= JRequest::getCmd('view', $this->getName());
		$viewLayout	= JRequest::getCmd('layout', 'default');

		$view = & $this->getView($viewName, $viewType, '', array('base_path'=>$this->_basePath));

		// Get/Create the character model
		$corporationModel = & $this->getModel('Corporation');
		$view->setModel($corporationModel, true); 
		
		$sectionaccessModel = & $this->getModel('Sectionaccess');
		// Push the model into the view
		$view->setModel($sectionaccessModel);

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
		
	function add() {
		$app = &JFactory::getApplication();

		// Clear the level edit information from the session.
		$app->setUserState('com_eve.edit.corporation.corporationID', null);
		$app->setUserState('com_eve.edit.corporation.data', null);

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=corporation&layout=edit', false));
	}
	
	function edit() {
		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('Corporation', 'EveModel');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');
		
		$previousId		= (int) $app->getUserState('com_eve.edit.corporation.corporationID');
		$corporationID		= (int) (count($cid) ? $cid[0] : JRequest::getInt('corporationID'));
		// If corporation ids do not match, checkin previous corporation.
		if (($previousId > 0) && ($corporationID != $previousId)) {
			if (!$model->checkin($previousId)) {
				// Check-in failed, go back to the corporation and display a notice.
				$message = JText::sprintf('JError_Checkin_failed', $model->getError());
				$this->setRedirect('index.php?option=com_eve&view=corporation&layout=edit', $message, 'error');
				return false;
			}
		}
		
		// Attempt to check-out the new corporation for editing and redirect.
		if (!$model->checkout($corporationID)) {
			// Check-out failed, go back to the list and display a notice.
			$message = JText::sprintf('JError_Checkout_failed', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=corporation&corporationID='.$corporationID, $message, 'error');
			return false;
		}
		else {
			// Check-out succeeded, push the new corporation id into the session.
			$app->setUserState('com_eve.edit.corporation.corporationID',	$corporationID);
			$app->setUserState('com_eve.edit.corporation.data', null);
			$this->setRedirect('index.php?option=com_eve&view=corporation&layout=edit');
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
		$model	= &$this->getModel('Corporation', 'EveModel');

		// Get the corporation id.
		$corporationID = (int) $app->getUserState('com_eve.edit.corporation.corporationID');

		// Attempt to check-in the current corporation.
		if ($corporationID) {
			if (!$model->checkin($corporationID)) {
				// Check-in failed, go back to the corporation and display a notice.
				$message = JText::sprintf('JError_Checkin_failed', $model->getError());
				$this->setRedirect('index.php?option=com_eve&view=corporation&layout=edit', $message, 'error');
				return false;
			}
		}
		// Clean the session data and redirect.
		$app->setUserState('com_eve.edit.corporation.corporationID',		null);
		$app->setUserState('com_eve.edit.corporation.data',	null);
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=corporations', false));
	}

	/**
	 * Method to save a corporation.
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
		$model	= $this->getModel('Corporation');
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
			$app->setUserState('com_eve.edit.corporation.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_eve&view=corporation&layout=edit', false));
			return false;
		}

		// Attempt to save the corporation.
		$return = $model->save($data);

		if ($return === false) {
			// Save failed, go back to the corporation and display a notice.
			$message = JText::sprintf('JError_Save_failed', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=corporation&layout=edit', $message, 'error');
			return false;
		}

		// Save succeeded, check-in the corporation.
		if (!$model->checkin()) {
			// Check-in failed, go back to the corporation and display a notice.
			$message = JText::sprintf('JError_Checkin_saved', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=corporation&layout=edit', $message, 'error');
			return false;
		}

		$this->setMessage(JText::_('JController_Save_success'));

		// Redirect the user and adjust session state based on the chosen task.
		switch ($this->_task) {
			case 'apply':
				// Redirect back to the edit screen.
				$app->setUserState('com_eve.edit.corporation.corporationID', $return);
				$this->setRedirect(JRoute::_('index.php?option=com_eve&view=corporation&layout=edit', false));
				break;

			case 'save2new':
				// Clear the member id and data from the session.
				$app->setUserState('com_eve.edit.corporation.corporationID', null);
				$app->setUserState('com_eve.edit.corporation.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=com_eve&view=corporation&layout=edit', false));
				break;

			default:
				// Clear the member id and data from the session.
				$app->setUserState('com_eve.edit.corporation.corporationID', null);
				$app->setUserState('com_eve.edit.corporation.data', null);

				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option=com_eve&view=corporations', false));
				break;
		}
	}
	
	function delete() {
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$model	= &$this->getModel('Corporation');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');

		// Sanitize the input.
		JArrayHelper::toInteger($cid);

		// Attempt to delete the corporations
		$return = $model->delete($cid);

		// Delete the weblinks
		if ($return === false) {
			$message = JText::sprintf('JError_Occurred', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=corporations', $message, 'error');
			return false;
		}
		else {
			$message = JText::sprintf('JSuccess_N_items_deleted', $return);
			$this->setRedirect('index.php?option=com_eve&view=corporations', $message);
			return true;
		}
	}
	
	function getCorporationSheet() {
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		$model = & $this->getModel('Corporation', 'EveModel');
		
		// Sanitize the input.
		JArrayHelper::toInteger($cid);
		
		//@todo: message, error output
		$model->apiGetCorporationSheet($cid);
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=corporations', false));
	}
	
	function getMemberTracking() {
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		$model = & $this->getModel('Corporation', 'EveModel');
		
		// Sanitize the input.
		JArrayHelper::toInteger($cid);
		
		//@todo: message, error output
		$model->apiGetMemberTracking($cid);
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=corporations', false));
	}
	
	public function search()
	{
		$model = $this->getModel('Corporations', 'EveModel', array('ignore_request' => true));
		$model->setState('filter.search', JRequest::getString('filter_search', '', 'request'));
		$model->setState('filter.fullsearch', 0);
		$model->setState('list.filter_order', 'co.corporationName');
		$model->setState('list.query', 'co.corporationID, co.corporationName');
		$list = $model->getItems();
		echo json_encode($list);
	}
		
}
