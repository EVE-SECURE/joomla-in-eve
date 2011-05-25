<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.application.component.model');

/**
 * Prototype item model.
 *
 * @package		Joomla.Framework
 * @subpackage	Application
 * @version		1.6
 */
abstract class JModelItem extends JModel
{
	/**
	 * An item.
	 *
	 * @var		array
	 */
	protected $_item = null;

	/**
	 * Model option string.
	 *
	 * @var		string
	 */
	protected $_option;
	
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context;
	
	protected $__state_set = false;

	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JModel
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		if (!empty($config['ignore_request'])) {
			$this->__state_set = true;
		}
		
		// Check for context in config
		if (isset($config['context'])) {
			$this->_context = $config['context'];
		}
		
		if (empty($this->_option) || empty($this->_context)) {
			$r = null;
			if (!preg_match('/(.*)Model(.*)/i', get_class($this), $r)) {
				return JError::raiseError(500, 'JModel_Error_Cannot_parse_name');
			}
		}
		
		// Guess the option as the prefix, eg: OptionControllerContext
		if (empty($this->_option)) {
			$this->_option = 'com_'.strtolower($r[1]);
		}
		
		// Guess the context as the suffix, eg: OptionControllerContext
		if (empty($this->_context)) {
			$this->_context = strtolower($r[2]);
		}
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
		// Compile the store id.

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
	 */
	protected function _populateState()
	{
		$app = &JFactory::getApplication();

		// Load state from the request.
		if (!($pk = (int) $app->getUserState($this->_option.'.'.$this->_context.'.id'))) {
			$pk = (int) JRequest::getInt('id');
		}
		$this->setState($this->_context.'.id', $pk);

		// Load the parameters.
		if ($app->isSite()) {
			$params	= $app->getParams();
		} else {
			$params = JComponentHelper::getParams($this->_option);
		}
		$this->setState('params', $params);
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
		
	protected function _loadItem($pk = null)
	{
		// Get a row instance.
		$data = false;
		$table = &$this->getTable();

		if ($table->load($pk)) {
			$data = JArrayHelper::toObject($table->getProperties(1), 'JObject');
		} else if ($error = $table->getError()) {
			$this->setError($error);
		}
		
		return $data;		
	}
		
	public function getTable($name = null, $prefix = null, $options = array())
	{
		if (is_null($name)) {
			$name = ucfirst($this->_context);
		}
		if (is_null($prefix)) {
			$prefix = ucfirst(substr($this->_option, 4)).'Table'; 
		}
		return parent::getTable($name, $prefix, $options);
	}
	
	
	/**
	 * Method to get processed item data.
	 *
	 * @param	integer	The id of the item.
	 *
	 * @return	mixed	Content item data object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		// Initialize variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->_context.'.id');
		$storeId = $this->_getStoreId($pk);
		
		if ($this->_item === null) {
			$this->_item = array();
		}
		
		if (!isset($this->_item[$storeId])) {
			$this->_item[$storeId] = $this->_loadItem($pk);
		}
		
		return $this->_item[$storeId];
	}

}
