<?php
/**
 * @version		$Id$
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class CronControllerJobs extends JController
{
	function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('disable',		'enable');
		$this->registerTask('orderup',		'reorder');
		$this->registerTask('orderdown',	'reorder');
	}


	/**
	 * Method to delete item(s) from the database.
	 *
	 * @access	public
	 */
	public function delete()
	{
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		$app	= &JFactory::getApplication();
		$model	= &$this->getModel('Job');
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');

		// Sanitize the input.
		JArrayHelper::toInteger($cid);

		// Attempt to delete the jobs
		$return = $model->delete($cid);

		// Delete the jobs
		if ($return === false) {
			$message = JText::sprintf('JError_Occurred', $model->getError());
			$this->setRedirect('index.php?option=com_cron&view=jobs', $message, 'error');
			return false;
		}
		else {
			$message = JText::_('JSuccess_N_items_deleted');
			$this->setRedirect('index.php?option=com_cron&view=jobs', $message);
			return true;
		}
	}

	/**
	 * Method to publish unpublished item(s).
	 *
	 * @return	void
	 */
	public function enable()
	{
		JRequest::checkToken() or jExit(JText::_('JInvalid_Token'));

		$model	= &$this->getModel('Jobs');
		$cid	= JRequest::getVar('cid', null, 'post', 'array');

		JArrayHelper::toInteger($cid);

		// Check for items.
		if (count($cid) < 1) {
			$message = JText::_('JError_No_item_selected');
			$this->setRedirect('index.php?option=com_cron&view=jobs', $message, 'warning');
			return false;
		}

		// Attempt to publish the items.
		$task	= $this->getTask();
		if ($task == 'enable') {
			$value = 1;
		} else {
			$value = 0;
		}

		$return = $model->setEnabled($cid, $value);

		if ($return === false) {
			$message = JText::sprintf('JError_Occurred', $model->getError());
			$this->setRedirect('index.php?option=com_cron&view=jobs', $message, 'error');
			return false;
		}
		else {
			$message = $value ? JText::_('JSuccess_N_items_published') : JText::_('JSuccess_N_items_unpublished');
			$this->setRedirect('index.php?option=com_cron&view=jobs', $message);
			return true;
		}
	}

	/**
	 * Method to reorder jobs.
	 *
	 * @return	bool	False on failure or error, true on success.
	 */
	public function reorder()
	{
		JRequest::checkToken() or jExit(JText::_('JInvalid_Token'));

		// Initialize variables.
		$model	= &$this->getModel('Job');
		$cid	= JRequest::getVar('cid', null, 'post', 'array');

		// Get the job id.
		$jobId = (int) $cid[0];

		// Attempt to move the row.
		$return = $model->reorder($jobId, $this->getTask() == 'orderup' ? -1 : 1);

		if ($return === false) {
			// Move failed, go back to the job and display a notice.
			$message = JText::sprintf('JError_Reorder_failed', $model->getError());
			$this->setRedirect('index.php?option=com_cron&view=jobs', $message, 'error');
			return false;
		}
		else {
			// Move succeeded, go back to the job and display a message.
			$message = JText::_('JSuccess_Item_reordered');
			$this->setRedirect('index.php?option=com_cron&view=jobs', $message);
			return true;
		}
	}


	/**
	 * Method to save the current ordering arrangement.
	 *
	 * @return	void
	 */
	public function saveorder()
	{
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));

		// Get the input
		$cid	= JRequest::getVar('cid',	null,	'post',	'array');
		$order	= JRequest::getVar('order',	null,	'post',	'array');

		// Sanitize the input
		JArrayHelper::toInteger($cid);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = &$this->getModel('Jobs');

		// Save the ordering
		$model->saveorder($cid, $order);

		$message = JText::_('JSuccess_Ordering_saved');
		$this->setRedirect('index.php?option=com_cron&view=jobs', $message);
	}




}