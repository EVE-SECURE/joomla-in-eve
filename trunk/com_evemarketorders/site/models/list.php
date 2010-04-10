<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Market Orders
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

class EvemarketordersModelList extends JModelList {
	protected $dbdump;
		
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_context = 'com_evemarketorders.list';
	
	protected $_entity = null;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$entity = JArrayHelper::getValue($config, 'entity', JRequest::getCmd('view'));
		$this->_entity = $entity;
		$eveparams = JComponentHelper::getParams('com_eve');
		$dbdump_database = $eveparams->get('dbdump_database');
		$this->dbdump = $dbdump_database ? $dbdump_database.'.' :''; 
	}
	
	protected function _getListQuery()
	{
		$search = $this->getState('filter.search');
		$entityID = intval($this->getState('list.entityID'));
		$accountKey = intval($this->getState('filter.accountKey', -1));
		$columns = array('mo.issued', 'mo.orderid', 'inv.typename', 
			'mo.volentered', 'mo.volremaining', 'mo.minvolume', 'mo.price', 'mo.escrow', 'mo.bid', 
			'sta.stationname', 'mo.range', 'mo.duration', 'mo.orderstate', );
		// Create a new query object.
		$dbo = $this->getDBO();
		$q = new JQuery($dbo);
		$q->addTable('#__eve_marketorders', 'mo');
		$q->addJoin($this->dbdump.'staStations', 'sta', 'sta.stationID=mo.stationID');
		$q->addJoin($this->dbdump.'invTypes', 'inv', 'inv.typeID=mo.typeID');
		$q->addQuery('mo.*');
		$q->addQuery('sta.stationTypeID, sta.stationName');
		$q->addQuery('inv.typeName');
		if ($this->_entity == 'corporation') {
			if ($accountKey > 0) {
				$q->addWhere('accountKey = %1$s', $accountKey);
			}
			array_push($columns, 'mo.accountkey', 'ch.name');
			$q->addJoin('#__eve_characters', 'ch', 'ch.characterID=mo.charID');
			$q->addJoin('#__eve_corporations', 'co', 'co.corporationID=ch.corporationID');
			$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
			$q->addQuery('ch.characterID', 'ch.name AS characterName');
			$q->addQuery('co.corporationID', 'co.corporationName', 'co.ticker AS corporationTicker');
			$q->addQuery('al.allianceID', 'al.name AS allianceName', 'al.shortName AS allianceShortName');
		}
		$q->addWhere('entityID = %1$s', $entityID);
		
		if ($search) {
			//TODO: search
			$q->addWhere(sprintf('(inv.typeName LIKE %1$s OR sta.stationName LIKE %1$s)', 
				$q->Quote( '%'.$q->getEscaped( $search, true ).'%', false )));
		}
		$ordering = $q->getEscaped($this->getState('list.ordering', 'mo.issued'));
		$direction = $q->getEscaped($this->getState('list.direction', 'desc'));
		if (!in_array(strtolower($ordering), $columns)) {
			$ordering = 'mo.issued';
		}
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
		$id	.= ':'.$this->getState('filter.accountKey');
		
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

		$accountKey = -1;
		if ($this->_entity == 'corporation') {
			$accountKey = $app->getUserStateFromRequest($context.'filter.accountKey', 'accountKey', $accountKey);
		}
		$this->setState('filter.accountKey', $accountKey);
		
		parent::_populateState('mo.issued', 'desc');

		$limitstart = JRequest::getInt('limitstart'); 
		$this->setState('list.start', $limitstart);
		
		// Load the parameters.
		$this->setState('params', $params);
	}

	public function getAccountKeys()
	{
		$options = array();
		for ($i = 1000; $i <= 1006; $i += 1) {
			$option = JHTML::_('select.option', $i);
			$options[] = $option;
		}
		return $options;
	}
	
}
