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
		$eveparams = JComponentHelper::getParams('com_eve');
		$dbdump_database = $eveparams->get('dbdump_database');
		$this->dbdump = $dbdump_database ? $dbdump_database.'.' :'';
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
		$q->addTable('#__eve_assets', 'al');
		$q->addJoin('#__eve_assets', 'con', 'con.itemID=al.containerID AND con.entityID=al.entityID');
		$q->addJoin($this->dbdump.'invTypes', 'inv', 'inv.typeID=al.typeID');
		$q->addJoin($this->dbdump.'invTypes', 'cinv', 'cinv.typeID=con.typeID');
		$q->addJoin($this->dbdump.'invFlags', 'fla', 'fla.flagID=al.flag');
		$q->addQuery('al.*');
		$q->addQuery('inv.typeName');
		$q->addQuery('cinv.typeID AS containerTypeID, cinv.typeName AS containerTypeName');
		$q->addQuery('fla.flagName, fla.flagText');
		if (false) {
			$locationQuery = "CASE
		  WHEN al.locationID BETWEEN 66000000 AND 66999999 THEN
		    (SELECT s.stationName FROM {$this->dbdump}staStations AS s
		      WHERE s.stationID=al.locationID-6000001)
		  WHEN al.locationID BETWEEN 67000000 AND 67999999 THEN
		    (SELECT c.stationName FROM api.ConqStations AS c
		      WHERE c.stationID=al.locationID-6000000)
		  WHEN al.locationID BETWEEN 60014861 AND 60014928 THEN
		    (SELECT c.stationName FROM api.ConqStations AS c
		      WHERE c.stationID=al.locationID)
		  WHEN al.locationID BETWEEN 60000000 AND 61000000 THEN
		    (SELECT s.stationName FROM {$this->dbdump}staStations AS s
		      WHERE s.stationID=al.locationID)
		  WHEN al.locationID>=61000000 THEN
		    (SELECT c.stationName FROM api.ConqStations AS c
		      WHERE c.stationID=al.locationID)
		  else (SELECT m.itemName FROM {$this->dbdump}mapDenormalize AS m
		    WHERE m.itemID=al.locationID) END locationName";
		} else {
			$locationQuery = "CASE
		  WHEN al.locationID BETWEEN 66000000 AND 66999999 THEN
		    (SELECT s.stationName FROM {$this->dbdump}staStations AS s
		      WHERE s.stationID=al.locationID-6000001)
		  WHEN al.locationID BETWEEN 67000000 AND 67999999 THEN
		  	".$dbo->quote(JText::_('Com_Eveassetlist_Unknown_Player_Outpost'))."
		  WHEN al.locationID BETWEEN 60014861 AND 60014928 THEN
		  	".$dbo->quote(JText::_('Com_Eveassetlist_Unknown_Player_Outpost'))."
		  WHEN al.locationID BETWEEN 60000000 AND 61000000 THEN
		    (SELECT s.stationName FROM {$this->dbdump}staStations AS s
		      WHERE s.stationID=al.locationID)
		  WHEN al.locationID>=61000000 THEN
		  	".$dbo->quote(JText::_('Com_Eveassetlist_Unknown_Player_Outpost'))."
		  else (SELECT m.itemName FROM {$this->dbdump}mapDenormalize AS m
		    WHERE m.itemID=al.locationID) END AS locationName";
		}

		$q->addQuery($locationQuery);

		$orderings = array('al.itemid', 'inv.typename', 'al.quantity', 'al.singleton',
			 'fla.flagtext', 'locationname', 'containertypename');
		if ($this->_entity == 'user') {
			$orderings[] = 'charactername';
			$q->addJoin('#__eve_characters', 'ch', 'ch.characterID=al.entityID');
			$q->addQuery('ch.name AS characterName');
			$acl = EveFactory::getACL();
			$chacracterIDs = $acl->getUserCharacterIDs();
			if ($chacracterIDs) {
				$q->addWhere('al.entityID IN ('.implode(', ', $chacracterIDs).')');
			} else {
				$q->addWhere('0 = 1');
			}
		} else {
			$q->addWhere('al.entityID = %1$s', $entityID);
		}



		if ($search) {
			$q->addWhere(sprintf('(inv.typeName LIKE %1$s OR cinv.typeName LIKE %1$s)',
			$q->Quote( '%'.$q->getEscaped( $search, true ).'%', false )));
		}
		$ordering = $q->getEscaped($this->getState('list.ordering', 'al.itemID'));
		$direction = $q->getEscaped($this->getState('list.direction', 'desc'));
		if (!in_array(strtolower($ordering), $orderings)) {
			$ordering = 'al.itemID';
		}
		if (strtolower($direction) != 'asc' && strtolower($direction) != 'desc') {
			$direction = 'desc';
		}

		if (strtolower($ordering) == 'al.quantity') {
			$q->addOrder('al.singleton', $direction == strtolower('desc') ? 'asc' : 'desc');
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

		if ($this->_entity == 'user') {
			$user = JFactory::getUser();
			$entityID = $user->id;
		} else {
			$entityID = JRequest::getInt($this->_entity.'ID');
		}
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
