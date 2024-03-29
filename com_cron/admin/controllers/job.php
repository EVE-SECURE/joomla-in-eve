<?php
/**
 * @version		$Id$
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class CronControllerJob extends JController
{
	function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('apply',	'save');
		$this->registerTask('save2new',	'save');
		$this->registerTask('save2view','save');
	}


	/**
	 * Dummy method to redirect back to standard controller
	 *
	 * @return	void
	 */
	public function display()
	{
		$this->setRedirect(JRoute::_('index.php?option=com_cron', false));
	}

	/**
	 * Method to add a new job.
	 *
	 * @return	void
	 */
	public function add()
	{
		// Initialize variables.
		$app = &JFactory::getApplication();

		// Clear the level edit information from the session.
		$app->setUserState('com_cron.edit.job.id', null);
		$app->setUserState('com_cron.edit.job.data', null);

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_cron&view=job&layout=edit', false));
	}

	public function view()
	{
		// Initialize variables.
		$app = &JFactory::getApplication();
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');
		$jobId		= (int) (count($cid) ? $cid[0] : JRequest::getInt('id'));
		$app->setUserState('com_cron.edit.job.id',	$jobId);
		$this->setRedirect('index.php?option=com_cron&view=job&id='.$jobId);
	}

	/**
	 * Method to edit an existing job.
	 *
	 * @return	void
	 */
	public function edit()
	{
		// Initialize variables.
		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('Job');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');

		// Get the previous job id (if any) and the current job id.
		$previousId		= (int) $app->getUserState('com_cron.edit.job.id');
		$jobId		= (int) (count($cid) ? $cid[0] : JRequest::getInt('id'));

		// If job ids do not match, checkin previous job.
		if (($previousId > 0) && ($jobId != $previousId)) {
			if (!$model->checkin($previousId)) {
				// Check-in failed, go back to the job and display a notice.
				$message = JText::sprintf('JError_Checkin_failed', $model->getError());
				$this->setRedirect('index.php?option=com_cron&view=job&layout=edit', $message, 'error');
				return false;
			}
		}

		// Attempt to check-out the new job for editing and redirect.
		if (!$model->checkout($jobId)) {
			// Check-out failed, go back to the list and display a notice.
			$message = JText::sprintf('JError_Checkout_failed', $model->getError());
			$this->setRedirect('index.php?option=com_cron&view=job&id='.$jobId, $message, 'error');
			return false;
		} else {
			// Check-out succeeded, push the new job id into the session.
			$app->setUserState('com_cron.edit.job.id',	$jobId);
			$app->setUserState('com_cron.edit.job.data', null);
			$this->setRedirect('index.php?option=com_cron&view=job&layout=edit');
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
		$model	= &$this->getModel('Job');

		// Get the job id.
		$jobId = (int) $app->getUserState('com_cron.edit.job.id');

		// Attempt to check-in the current job.
		if ($jobId) {
			if (!$model->checkin($jobId)) {
				// Check-in failed, go back to the job and display a notice.
				$message = JText::sprintf('JError_Checkin_failed', $model->getError());
				$this->setRedirect('index.php?option=com_cron&view=job&layout=edit', $message, 'error');
				return false;
			}
		}

		// Clean the session data and redirect.
		$app->setUserState('com_cron.edit.job.id',		null);
		$app->setUserState('com_cron.edit.job.data',	null);
		$this->setRedirect(JRoute::_('index.php?option=com_cron&view=jobs', false));
	}

	/**
	 * Method to save a job.
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
		$model	= $this->getModel('job');
		$data	= JRequest::getVar('jform', array(), 'post', 'array');
		$data['content'] = JRequest::getVar('content', '', 'post', 'none', JREQUEST_ALLOWHTML);

		$data = $model->validate($data);

		// Check for validation errors.
		if ($data === false) {
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
			$app->setUserState('com_cron.edit.job.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_cron&view=job&layout=edit', false));
			return false;
		}

		// Attempt to save the job.
		$return = $model->save($data);

		if ($return === false) {
			// Save failed, go back to the job and display a notice.
			$message = JText::sprintf('JError_Save_failed', $model->getError());
			$this->setRedirect('index.php?option=com_cron&view=job&layout=edit', $message, 'error');
			return false;
		}

		// Save succeeded, check-in the job.
		if (!$model->checkin()) {
			// Check-in failed, go back to the job and display a notice.
			$message = JText::sprintf('JError_Checkin_saved', $model->getError());
			$this->setRedirect('index.php?option=com_cron&view=job&layout=edit', $message, 'error');
			return false;
		}

		$this->setMessage(JText::_('Save success'));

		// Redirect the user and adjust session state based on the chosen task.
		switch ($this->_task) {
			case 'apply':
				// Redirect back to the edit screen.
				$app->setUserState('com_cron.edit.job.id', $return);
				$this->setRedirect(JRoute::_('index.php?option=com_cron&view=job&layout=edit', false));
				break;

			case 'save2new':
				// Clear the member id and data from the session.
				$app->setUserState('com_cron.edit.job.id', null);
				$app->setUserState('com_cron.edit.job.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=com_cron&view=job&layout=edit', false));
				break;
			case 'save2view':
				// Clear the member id and data from the session.
				$app->setUserState('com_cron.edit.job.id', $return);

				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=com_cron&view=job&id='.$return, false));
				break;

			default:
				// Clear the member id and data from the session.
				$app->setUserState('com_cron.edit.job.id', null);
				$app->setUserState('com_cron.edit.job.data', null);

				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option=com_cron&view=jobs', false));
				break;
		}
	}

}