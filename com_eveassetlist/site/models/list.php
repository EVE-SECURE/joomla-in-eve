<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Asset List
 * @copyright	Copyright (C) 2010 Pavol Kovalik. All rights reserved.
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

jimport('joomla.application.component.modellist');

class EveassetlistModelList extends JModelList {
	
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_context = 'com_eveassetlist.list';
	
	protected $_entity = null;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$entity = JArrayHelper::getValue($config, 'entity', JRequest::getCmd('view'));
		$this->_entity = $entity;
	}
	
	protected function _getListQuery()
	{
		$search = $this->getState('filter.search');
		$entityID = intval($this->getState('list.entityID'));
		$accountKey = intval($this->getState('filter.accountKey', 1000));
		$refTypeID = intval($this->getState('filter.refTypeID', -1));
		// Create a new query object.
		$dbo = $this->getDBO();
		$q = new JQuery($dbo);
		$q->addTable('#__eve_assetlist', 'al');
		$q->addWhere('entityID = %1$s', $entityID);
		
		
		/*if ($search) {
			$q->addWhere(sprintf('(wj.ownerName1 LIKE %1$s OR wj.ownerName2 LIKE %1$s OR wj.argName1 LIKE %1$s OR wj.reason LIKE %1$s)', 
				$q->Quote( '%'.$q->getEscaped( $search, true ).'%', false )));
		}*/
		$ordering = $q->getEscaped($this->getState('list.ordering', 'wj.refID'));
		$direction = $q->getEscaped($this->getState('list.direction', 'desc'));
		/*if (!in_array(strtolower($ordering), array('wj.refid', 'rt.reftypename', 'wj.date', 'wj.ownername1', 
				'wj.ownername2', 'wj.argname1', 'wj.amount', 'wj.balance', 'wj.reason' ))) {
			$ordering = 'wj.refID';
		}*/
		if (strtolower($direction) != 'asc' && strtolower($direction) != 'desc') {
			$direction = 'desc';
		}
		
		$q->addOrder($ordering, $direction);
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
		$id	.= ':'.$this->getState('list.entityID');
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.ordering');
		$id	.= ':'.$this->getState('list.direction');
		$id	.= ':'.$this->getState('filter.search');
		
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
		$params		= JComponentHelper::getParams('com_eve');
		$context	= $this->_context.'.';
		
		$entityID = JRequest::getInt($this->_entity.'ID');
		$this->setState('list.entityID', $entityID);
		
		// Load the filter state.
		$search = $app->getUserStateFromRequest($context.'filter.search', 'filter_search', '');
		$this->setState('filter.search', $search);

		parent::_populateState('al.itemID', 'desc');

		$limitstart = JRequest::getInt('limitstart'); 
		$this->setState('list.start', $limitstart);
		
		// Load the parameters.
		$this->setState('params', $params);
	}

}
