<?php
/**
 * @version		$Id: modelitem.php 12189 2009-06-20 00:34:32Z eddieajau $
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @copyright	Copyright (C) 2008 - 2009 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.application.component.model');
jimport('joomla.database.query');

/**
 * Prototype item model.
 *
 * @package		Joomla.Framework
 * @subpackage	Application
 * @version		1.6
 */
abstract class JModelItem extends JModel
{
	//missing from 1.6 JModel
	protected $__state_set	= null;
	
	/**
	 * An item.
	 *
	 * @var		array
	 */
	protected $_item = null;

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	 protected $_context = 'group.type';

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
	
	protected function _populateState()
	{
		
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