<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.application.component.model');
jimport('joomla.database.query');

/**
 * Prototype list model.
 *
 * @package		Joomla.Framework
 * @subpackage	Application
 * @since		1.6
 */
class JModelList extends JModel
{
	//missing from 1.6 JModel
	protected $__state_set	= null;
	
	/**
	 * Internal memory based cache array of data.
	 *
	 * @var		array
	 */
	protected $_cache = array();

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context		= null;

	public function __construct($config = array())
	{
		parent::__construct($config);
		if (!empty($config['ignore_request'])) {
			$this->__state_set = true;
		}
	}

	/**
	 * Method to get a list of items.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 */
	public function &getItems()
	{
		// Get a storage key.
		$store = $this->_getStoreId();

		// Try to load the data from internal storage.
		if (!empty($this->_cache[$store])) {
			return $this->_cache[$store];
		}

		// Load the list items.
		$query	= $this->_getListQuery();
		$items	= $this->_getList((string) $query, $this->getState('list.start'), $this->getState('list.limit'));

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Add the items to the internal cache.
		$this->_cache[$store] = $items;

		return $this->_cache[$store];
	}

	/**
	 * Method to get a list pagination object.
	 *
	 * @return	object	A JPagination object.
	 */
	public function &getPagination()
	{
		jimport('joomla.html.pagination');
		// Get a storage key.
		$store = $this->_getStoreId('getPagination');

		// Try to load the data from internal storage.
		if (!empty($this->_cache[$store])) {
			return $this->_cache[$store];
		}

		// Create the pagination object.
		jimport('joomla.html.pagination');
		$page = new JPagination($this->getTotal(), (int) $this->getState('list.start'), (int) $this->getState('list.limit'));

		// Add the object to the internal cache.
		$this->_cache[$store] = $page;

		return $this->_cache[$store];
	}

	/**
	 * Method to get the total number of published items.
	 *
	 * @return	int		The number of published items.
	 */
	public function getTotal()
	{
		// Get a storage key.
		$store = $this->_getStoreId('getTotal');

		// Try to load the data from internal storage.
		if (!empty($this->_cache[$store])) {
			return $this->_cache[$store];
		}

		// Load the total.
		$query = $this->_getListQuery();
		$total = (int) $this->_getListCount((string) $query);

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Add the total to the internal cache.
		$this->_cache[$store] = $total;

		return $this->_cache[$store];
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string		An SQL query
	 */
	protected function _getListQuery()
	{
		$query = new JQuery();

		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$context	A prefix for the store id.
	 * @return	string		A store id.
	 */
	protected function _getStoreId($id = '')
	{
		// Add the list state to the store id.
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.ordering');
		$id	.= ':'.$this->getState('list.direction');

		return md5($this->_context.':'.$id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return	void
	 */
	protected function _populateState($ordering = null, $direction = 'ASC')
	{
		// If the context is set, assume that stateful lists are used.
		if ($this->_context)
		{
			$app = JFactory::getApplication();

			$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
			$this->setState('list.limit', $limit);

			$limitstart = $app->getUserStateFromRequest($this->_context.'.limitstart', 'limitstart', 0);
			$this->setState('list.start', $limitstart);

			$orderCol = $app->getUserStateFromRequest($this->_context.'.ordercol', 'filter_order', $ordering);
			$this->setState('list.ordering', $orderCol);

			$orderDirn = $app->getUserStateFromRequest($this->_context.'.orderdirn', 'filter_order_Dir', $direction);
			$this->setState('list.direction', $orderDirn);
		}
		else
		{
			$this->setState('list.start', 0);
			$this->setState('list.limit', 0);
		}
	}

	
	/**
	 * Method to get model state variables
	 * 1.6 JModel override
	 *
	 * @param	string	Optional parameter name
	 * @param   mixed	Optional default value
	 * @return	object	The property where specified, the state object where omitted
	 */
	public function getState($property = null, $default = null)
	{
		if (!$this->__state_set) {
			// Private method to auto-populate the model state.
			$this->_populateState();

			// Set the model state set flat to true.
			$this->__state_set = true;
		}

		return $property === null ? $this->_state : $this->_state->get($property, $default);
	}
	

}
