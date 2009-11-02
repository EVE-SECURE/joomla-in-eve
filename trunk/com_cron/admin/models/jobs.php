<?php
/**
 * @version		$Id$
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

class CronModelJobs extends JModelList 
{
	protected $_context = 'com_cron.entities';
	
	function __construct($config = array()) {
		parent::__construct($config);
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 * @since	1.6
	 */
	protected function _getListQuery()
	{
		$search = $this->getState('filter.search');
		$created_by = intval($this->getState('filter.created_by'));
		$state = $this->getState('filter.state');
		$q = new JQuery();
		$q->addTable('#__cron_jobs', 'jobs');
		$q->addJoin('#__users', 'editor', 'editor.id=jobs.checked_out');
		//$q->addJoin('#__users', 'user', 'user.id=jobs.created_by');
		
		$q->addOrder($q->getEscaped($this->getState('list.filter_order', 'jobs.ordering')), 
			$q->getEscaped($this->getState('list.filter_order_Dir', 'ASC')));
		/*
		if (!empty($search)) {
			$search = $q->Quote('%'.$q->getEscaped($search, true).'%', false);
			$q->addWhere('(jobs.title LIKE '.$search.')');
		}
		if ($created_by > 0) {
			$q->addWhere('jobs.created_by = %s', $created_by);
		}
		if (is_numeric($state)) {
			$q->addWhere('jobs.state = %s',(int) $state);
		}*/
		
		$q->addQuery('jobs.*');
		$q->addQuery('editor.name AS editor');
		//$q->addQuery('user.name AS userName');
		return $q;
	}
	

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function _getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.filter_order');
		$id	.= ':'.$this->getState('list.filter_order_Dir');
		$id	.= ':'.$this->getState('check.state');
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.created_by');
		
		return md5($id);
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function _populateState()
	{
		// Initialize variables.
		$app		= &JFactory::getApplication('administrator');
		$params		= JComponentHelper::getParams('com_cron');
		$context	= $this->_context.'.';

		// Load the filter state.
		$this->setState('filter.search', $app->getUserStateFromRequest($context.'filter.search', 'filter_search', ''));
		$this->setState('filter.catid', $app->getUserStateFromRequest($context.'filter.catid', 'filter_catid', 0, 'int'));
		$this->setState('filter.created_by', $app->getUserStateFromRequest($context.'filter.created_by', 'filter_created_by', 0, 'int'));
		$this->setState('filter.state', $app->getUserStateFromRequest($context.'filter.state', 'filter_state', '*'));
		
		// Load the list state.
		$this->setState('list.start', $app->getUserStateFromRequest($context.'list.start', 'limitstart', 0, 'int'));
		$this->setState('list.limit', $app->getUserStateFromRequest($context.'list.limit', 'limit', $app->getCfg('list_limit', 25), 'int'));
		$this->setState('list.filter_order', $app->getUserStateFromRequest($context.'list.filter_order', 'filter_order', 'jobs.ordering', 'cmd'));
		$this->setState('list.filter_order_Dir', $app->getUserStateFromRequest($context.'list.filter_order_Dir', 'filter_order_Dir', 'ASC', 'word'));

		// Load the check parameters.
		if ($this->_state->get('filter.state') === '*') {
			$this->setState('check.state', false);
		} else {
			$this->setState('check.state', true);
		}

		// Load the parameters.
		$this->setState('params', $params);	
	}

	
	public function setPublished($cid, $state = 0)
	{
		$user = &JFactory::getUser();

		// Get a weblinks row instance.
		$table = $this->getTable('Job', 'CronTable'); 

		// Update the state for each row
		foreach ($cid as $id) {
			// Load the row.
			$table->load($id);

			// Make sure the weblink isn't checked out by someone else.
			if ($table->checked_out != 0 && $table->checked_out != $user->id) {
				$this->setError(JText::sprintf('Cron_Job_Checked_Out', $id));
				return false;
			}

			// Check the current ordering.
			if ($table->state != $state) {
				// Set the new ordering.
				$table->state = $state;

				// Save the row.
				if (!$table->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}
		return true;
	}
	
	public function saveorder($cid, $order)
	{
		// Get a adsTablead instance.
		$table = $this->getTable('Job', 'CronTable');
		
		foreach ($cid as $i => $id) {
			// Load the row.
			$table->load($id);

			// Make sure the weblink isn't checked out by someone else.
			if ($table->checked_out != 0 && $table->checked_out != $user->id) {
				$this->setError(JText::sprintf('Cron_Job_Checked_Out', $id));
				return false;
			}
			
			if (!isset($order[$i])) {
				continue;
			}

			// Check the current ordering.
			if ($table->ordering != $order[$i]) {
				// Set the new ordering.
				$table->ordering = $order[$i];

				// Save the row.
				if (!$table->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}
		return true;

	}
}