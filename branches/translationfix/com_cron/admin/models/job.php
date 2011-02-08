<?php
/**
 * @version		$Id$
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.modelitem');

class CronModelJob extends JModelItem {
	protected $_item = array();

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	function get($property, $default=null) {
		if ($item = $this->getItem()) {
			if(isset($item->$property)) {
				return $item->$property;
			}
		}
		return $default;

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
		$app		= &JFactory::getApplication();
		$params		= &JComponentHelper::getParams('com_cron');

		// Load the User state.
		if (JRequest::getWord('layout') === 'edit') {
			$jobID = (int) $app->getUserState('com_cron.edit.job.id');
			$this->setState('job.id', $jobID);
		} else {
			$jobID = (int) JRequest::getInt('id');
			$this->setState('job.id', $jobID);
		}


		// Load the parameters.
		$this->setState('params', $params);
	}

	function validate($data)
	{
		return $data;
	}

	/**
	 * Method to checkin a row.
	 *
	 * @param	integer	$id		The numeric id of a row
	 * @return	boolean	True on success/false on failure
	 * @since	1.6
	 */
	public function checkin($jobID = null)
	{
		// Initialize variables.
		$user		= &JFactory::getUser();
		$userId		= (int) $user->get('id');
		$jobID	= (int) $jobID;

		if ($jobID === 0) {
			$jobID = $this->getState('job.id');
		}

		if (empty($jobID)) {
			return true;
		}

		// Get a adsTablead instance.
		$table = &$this->getTable();

		// Attempt to check-in the row.
		$return = $table->checkin($jobID);

		// Check for a database error.
		if ($return === false) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to check-out a ad for editing.
	 *
	 * @param	int		$jobID	The numeric id of the ad to check-out.
	 * @return	bool	False on failure or error, success otherwise.
	 * @since	1.6
	 */
	public function checkout($jobID)
	{
		// Initialize variables.
		$user		= &JFactory::getUser();
		$userId		= (int) $user->get('id');
		$jobID	= (int) $jobID;

		// Check for a new ad id.
		if ($jobID === -1) {
			return true;
		}

		$table = &$this->getTable();

		// Attempt to check-out the row
		$return = $table->checkout($userI, $jobID);

		// Check for a database error.
		if ($return === false) {
			$this->setError($table->getError());
			return false;
		}

		// Check if the row is checked-out by someone else.
		if ($return === null) {
			$this->setError(JText::_('JCommon_Item_is_checked_out'));
			return false;
		}

		return true;
	}

	/**
	 * Method to get a member item.
	 *
	 * @access	public
	 * @param	integer	The id of the member to get.
	 * @return	mixed	User data object on success, false on failure.
	 * @since	1.0
	 */
	public function &getItem($jobID = null)
	{
		// Initialize variables.
		$jobID	= (!empty($jobID)) ? $jobID : (int) $this->getState('job.id');
		$false		= false;

		if (!isset($this->_item[$jobID])) {
			$table = $this->getTable();
			$table->load($jobID);

			if ($table instanceof JObject ) {
				$value = JArrayHelper::toObject($table->getProperties(1), 'JObject');
			} else {
				$value = JArrayHelper::toObject(get_object_vars($table), 'JObject');
			}
			$this->_item[$jobID] = $value;
		}
		return $this->_item[$jobID];
	}

	

	public function save($data)
	{
		$jobID	= (int) $this->getState('job.id');
		$isNew		= true;

		$dispatcher = &JDispatcher::getInstance();
		JPluginHelper::importPlugin('content');

		// Get a ad row instance.
		$table = &$this->getTable();

		// Load the row if saving an existing item.
		if ($jobID > 0) {
			$table->load($jobID);
			$isNew = false;
		}
		
		$params = JArrayHelper::getValue($data, 'params', null, 'array');
		if (is_array($params)) {
			$txt = array ();
			foreach ($params as $k => $v) {
				$txt[] = "$k=$v";
			}
			$data['params'] = implode("\n", $txt);
		}

		// Bind the data
		if (!$table->bind($data)) {
			$this->setError(JText::sprintf('JTable_Error_Bind_failed', $table->getError()));
			return false;
		}

		// Prepare the row for saving
		$this->_prepareTable($table);
		
		$this->_setCrontabBits($table);

		// Check the data
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}
		

		// Store the data
		if (!$table->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Trigger the onAfterContentSave event.
		//$dispatcher->trigger('onAfterContentSave', array(&$table, $isNew));
		
		return $table->id;
	}

	protected function _prepareTable(&$table)
	{
		jimport('joomla.filter.output');
		$date = &JFactory::getDate();
		$user = &JFactory::getUser();

		if (empty($table->id)) {
			if (empty($table->ordering)) {
				$db = &JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__cron_jobs');
				$max = $db->loadResult();

				$table->ordering = $max+1;
			}
		} else {
			
		}
	}

	/**
	 * Tests if ad is checked out
	 *
	 * @access	public
	 * @param	int	A user id
	 * @return	boolean	True if checked out
	 * @since	1.5
	 */
	public function isCheckedOut($userId = 0)
	{
		if ($userId === 0) {
			$user		= &JFactory::getUser();
			$userId		= (int) $user->get('id');
		}

		$jobID = (int) $this->getState('job.id');

		if (empty($jobID)) {
			return true;
		}

		$table = &$this->getTable();

		$return = $table->load($jobID);

		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return false;
		}

		return $table->isCheckedOut($userId);
	}

	/**
	 * Method to delete ads from the database.
	 *
	 * @param	integer	$cid	An array of	numeric ids of the rows.
	 * @return	boolean	True on success/false on failure.
	 */
	public function delete($cid)
	{
		// Get a ad row instance
		$table = $this->getTable();

		for ($i = 0, $c = count($cid); $i < $c; $i++) {
			// Load the row.
			$return = $table->load($cid[$i]);

			// Check for an error.
			if ($return === false) {
				$this->setError($table->getError());
				return false;
			}

			// Delete the row.
			$return = $table->delete();

			// Check for an error.
			if ($return === false) {
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to adjust the ordering of a row.
	 *
	 * @param	int		$jobID	The numeric id of the ad to move.
	 * @param	int		$direction	The direction to move the row (-1/1).
	 * @return	bool	True on success/false on failure
	 */
	public function reorder($jobID, $direction)
	{
		// Get a adsTablead instance.
		$table = &$this->getTable();

		$jobID	= (int) $jobID;

		if ($jobID === 0) {
			$jobID = $this->getState('job.id');
		}

		// Attempt to check-out and move the row.
		if (!$this->checkout($jobID)) {
			return false;
		}

		// Load the row.
		if (!$table->load($jobID)) {
			$this->setError($table->getError());
			return false;
		}

		// Move the row.
		$table->move($direction);

		// Check-in the row.
		if (!$this->checkin($jobID)) {
			return false;
		}

		return true;
	}

	public function getParams() {
		return $this->getState('params');
	}
	
	/**
	 * Sets bits to table;
	 *
	 * @param CronTableJob $table
	 */
	protected function _setCrontabBits($table) {
		require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'parser.php';
		try {
			$parser = new CronHelperParser();
			$parser->parse($table->pattern);
			foreach (array('minutes', 'hours', 'days', 'months', 'weekdays') as $field) {
				$method = 'get'.ucfirst($field);
				$table->$field = '.'.implode('.', $parser->$method()).'.';
			}
			$result = true;
		}
		catch (Exception $e) {
			$this->setError($e->getMessage());
			$result = false;
		}
		return $result;
		
	}

		
}