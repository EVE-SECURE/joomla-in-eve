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

class EveControllerRoles extends EveController {

	function __construct( $config = array() )
	{
		parent::__construct( $config );

		$this->registerTask('applysection', 'savesection');
		$this->registerTask('applysectioncorporation', 'savesectioncorporation');

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

		$app	= &JFactory::getApplication();
		$modelName = $app->getUserState('com_eve.roles.model');
		if (!$modelName) {
			$msg = JText::_('Unknown role context');
			$this->setRedirect(JRoute::_('index.php?option=com_eve&view=eve', false), $msg, 'error');
			return false;
		}
		$model = $this->getModel($modelName);
		$view->setModel($model, true);

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

	function editSection()
	{
		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('Sectionroles', 'EveModel');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');

		$id		= (int) (count($cid) ? $cid[0] : JRequest::getInt('id'));

		$app->setUserState('com_eve.roles.model', 'sectionRoles');
		$app->setUserState('com_eve.section.id', $id);
		$this->setRedirect('index.php?option=com_eve&view=roles&layout=edit');
		return true;
	}

	function editSectionCorporation()
	{
		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('Roles', 'EveModel');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');

		$section = (int) (count($cid) ? $cid[0] : JRequest::getInt('section'));
		$data	= JRequest::getVar('jform', array(), 'post', 'array');
		$corporationID = JArrayHelper::getValue($data, 'corporationID', null, 'int');

		$app->setUserState('com_eve.roles.model', 'sectionCorporationRoles');
		$app->setUserState('com_eve.sectionCorporation.corporationID', $corporationID);
		$app->setUserState('com_eve.sectionCorporation.section', $section);
		$this->setRedirect('index.php?option=com_eve&view=roles&layout=edit');
		return true;
	}

	/**
	 * Method to cancel an edit
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	public function cancelSection()
	{
		JRequest::checkToken() or jExit(JText::_('JInvalid_Token'));

		// Initialize variables.
		$app	= &JFactory::getApplication();

		$modelName = $app->getUserState('com_eve.roles.model');
		$app->setUserState('com_eve.roles.model', null);
		$app->setUserState('com_eve.sectionCorporation.corporationID', null);
		$app->setUserState('com_eve.sectionCorporation.section', null);

		$model	= &$this->getModel($modelName, 'EveModel');
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=access', false));
	}

	/*
	 * Method to cancel an edit
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	public function cancelSectionCorporation()
	{
		JRequest::checkToken() or jExit(JText::_('JInvalid_Token'));

		// Initialize variables.
		$app	= &JFactory::getApplication();

		$modelName = $app->getUserState('com_eve.roles.model');
		$app->setUserState('com_eve.roles.model', null);
		$app->setUserState('com_eve.section.id', null);

		$model	= &$this->getModel($modelName, 'EveModel');
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=corporation&layout=edit', false));
	}

	/**
	 * Method to save a Section.
	 *
	 * @access	public
	 * @return	void
	 * @since	0.6
	 */
	public function saveSection()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Initialize variables.
		$app	= &JFactory::getApplication();
		$model	= $this->getModel('Sectionroles');
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

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_eve&view=roles&layout=edit', false));
			return false;
		}

		// Attempt to save the character.
		$return = $model->save($data);

		if ($return === false) {
			// Save failed, go back to the character and display a notice.
			$message = JText::sprintf('JError_Save_failed', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=roles&layout=edit', $message, 'error');
			return false;
		}

		/*
		 // Save succeeded, check-in the character.
		 if (!$model->checkin()) {
			// Check-in failed, go back to the character and display a notice.
			$message = JText::sprintf('JError_Checkin_saved', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=character&layout=edit', $message, 'error');
			return false;
			}
			*/

		$this->setMessage(JText::_('JController_Save_success'));

		// Redirect the user and adjust session state based on the chosen task.
		switch (strtolower($this->_task)) {
			case 'applysection':
				// Redirect back to the edit screen.
				$app->setUserState('com_eve.section.id', $return);
				$this->setRedirect(JRoute::_('index.php?option=com_eve&view=roles&layout=edit', false));
				break;

			default:
				// Clear the member id and data from the session.
				$app->setUserState('com_eve.roles.model', null);
				$app->setUserState('com_eve.section.id', null);
				$app->setUserState('com_eve.section.data', null);

				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option=com_eve&view=access', false));
				break;
		}
	}

	/**
	 * Method to save a Section.
	 *
	 * @access	public
	 * @return	void
	 * @since	0.6
	 */
	public function saveSectionCorporation()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Initialize variables.
		$app	= &JFactory::getApplication();
		$model	= $this->getModel('SectionCorporationRoles');
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

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_eve&view=roles&layout=edit', false));
			return false;
		}

		// Attempt to save the character.
		$return = $model->save($data);

		if ($return === false) {
			// Save failed, go back to the character and display a notice.
			$message = JText::sprintf('JError_Save_failed', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=roles&layout=edit', $message, 'error');
			return false;
		}

		/*
		 // Save succeeded, check-in the character.
		 if (!$model->checkin()) {
			// Check-in failed, go back to the character and display a notice.
			$message = JText::sprintf('JError_Checkin_saved', $model->getError());
			$this->setRedirect('index.php?option=com_eve&view=character&layout=edit', $message, 'error');
			return false;
			}
			*/

		$this->setMessage(JText::_('JController_Save_success'));

		// Redirect the user and adjust session state based on the chosen task.
		switch (strtolower($this->_task)) {
			case 'applysectioncorporation':
				// Redirect back to the edit screen.
				$app->setUserState('com_eve.sectionCorporation.corporationID', $data['corporationID']);
				$app->setUserState('com_eve.sectionCorporation.section', $data['section']);
				$this->setRedirect(JRoute::_('index.php?option=com_eve&view=roles&layout=edit', false));
				break;

			default:
				// Clear the member id and data from the session.
				$app->setUserState('com_eve.roles.model', null);
				$app->setUserState('com_eve.sectionCorporation.corporationID', null);
				$app->setUserState('com_eve.sectionCorporation.section', null);

				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option=com_eve&task=corporation.edit&corporationID='.$data['corporationID'], false));
				break;
		}
	}

}
