<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Research
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

class EveresearchModelList extends JModelList {

	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_context = 'com_everesearch.list';

	protected $_entity = null;

	protected static $_datacore_table = false;

	public function __construct($config = array())
	{
		parent::__construct($config);
		$entity = JArrayHelper::getValue($config, 'entity', JRequest::getCmd('view'));
		$this->_entity = $entity;
		$eveparams = JComponentHelper::getParams('com_eve');
		$dbdump_database = $eveparams->get('dbdump_database');
		$this->dbdump = $dbdump_database ? $dbdump_database.'.' :'';
	}

	protected function _createDatacoreTable()
	{
		if (self::$_datacore_table) {
			return;
		}
		$dbo = $this->getDBO();
		$q = EveFactory::getQuery($dbo);
		$q->addTable($this->dbdump.'invTypes AS inv');
		$q->addJoin($this->dbdump.'dgmTypeAttributes', 'dgm1', 'dgm1.typeID = inv.typeID AND dgm1.attributeID = 182', 'INNER');
		$q->addJoin($this->dbdump.'dgmTypeAttributes', 'dgm2', 'dgm2.typeID = inv.typeID AND dgm2.attributeID = 1155', 'INNER');
		$q->addWhere('inv.groupID = 333');
		$q->addQuery('inv.typeID, inv.typeName, dgm1.valueInt AS datacoreSkillID, dgm2.valueInt AS datacoreCost');
		$query = $q->prepareSelect();

		$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS `tmp_eve_research_datacores` (
			`typeID` smallint(6) NOT NULL ,
			`typeName` varchar(100) default NULL ,
			`datacoreSkillID` int(11) default NULL ,
			`datacoreCost` int(11) default NULL ,
			PRIMARY KEY ( `typeID` )
		) ENGINE = MEMORY ".$query;
		$dbo->setQuery($sql);
		$dbo->query();
		self::$_datacore_table = true;
	}

	protected function _getListQuery()
	{
		$search = $this->getState('filter.search');
		$characterID = intval($this->getState('list.characterID'));
		// Create a new query object.
		$this->_createDatacoreTable();
		$dbo = $this->getDBO();
		$q = new JQuery($dbo);
		$q->addTable('#__eve_research', 're');
		$q->addJoin($this->dbdump.'eveNames', 'ev', 'ev.itemID=re.agentID');
		$q->addJoin($this->dbdump.'invTypes', 'inv', 'inv.typeID=re.skillTypeID');
		$q->addJoin($this->dbdump.'agtAgents', 'agt', 'agt.agentID=re.agentID');
		$q->addJoin($this->dbdump.'staStations', 'sta', 'sta.stationID=agt.locationID');
		$q->addJoin('tmp_eve_research_datacores', 'rd', 'rd.datacoreSkillID=re.skillTypeID');

		$q->addQuery('re.*');
		$q->addQuery('re.remainderPoints + (re.pointsPerDay * TIMESTAMPDIFF(SECOND, re.researchStartDate, NOW())/24/60/60) AS currentPoints');
		$q->addQuery('ev.itemName AS agentName');
		$q->addQuery('inv.typeName AS skillTypeName');
		$q->addQuery('agt.level');
		$q->addQuery('sta.stationName');
		$q->addQuery('re.pointsPerDay / rd.datacoreCost AS datacoresPerDay');
		$q->addQuery('(re.remainderPoints + (re.pointsPerDay * TIMESTAMPDIFF(SECOND, re.researchStartDate, NOW())/24/60/60)) / rd.datacoreCost AS currentDatacores');

		$orderings = array('agentname', 'skilltypename', 'currentpoints', 're.pointsperday', 'currentdatacores', 'datacoresperday',
			'agt.level', 'sta.stationname' );

		if ($this->_entity == 'user') {
			$orderings[] = 'charactername';
			$q->addJoin('#__eve_characters', 'ch', 'ch.characterID=re.characterID');
			$q->addQuery('ch.name AS characterName');
			$acl = EveFactory::getACL();
			$chacracterIDs = $acl->getUserCharacterIDs();
			if ($chacracterIDs) {
				$q->addWhere('re.characterID IN ('.implode(', ', $chacracterIDs).')');
			} else {
				$q->addWhere('0 = 1');
			}
		} else {
			$q->addWhere('re.characterID = %1$s', $characterID);
		}

		if ($search) {
			$q->addWhere(sprintf('(ev.itemName LIKE %1$s OR inv.typeName LIKE %1$s OR sta.stationName LIKE %1$s)',
			$q->Quote( '%'.$q->getEscaped( $search, true ).'%', false )));
		}
		$ordering = $q->getEscaped($this->getState('list.ordering', 'agentName'));
		$direction = $q->getEscaped($this->getState('list.direction', 'asc'));
		if (!in_array(strtolower($ordering), $orderings)) {
			$ordering = 're.agentID';
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
		$id	.= ':'.$this->getState('list.characterID');
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
			$characterID = $user->id;
		} else {
			$characterID = JRequest::getInt($this->_entity.'ID');
		}
		$this->setState('list.characterID', $characterID);

		// Load the filter state.
		$search = $app->getUserStateFromRequest($context.'filter.search', 'filter_search', '');
		$this->setState('filter.search', $search);

		parent::_populateState('agentName', 'desc');

		$limitstart = JRequest::getInt('limitstart');
		$this->setState('list.start', $limitstart);

		// Load the parameters.
		$this->setState('params', $params);
	}

	public function getSummary()
	{
		$this->_createDatacoreTable();
		$dbo = $this->getDBO();
		$q = new JQuery($dbo);
		$q->addTable('#__eve_research', 're');
		$q->addJoin($this->dbdump.'invTypes', 'inv', 'inv.typeID=re.skillTypeID');
		$q->addJoin('tmp_eve_research_datacores', 'rd', 'rd.datacoreSkillID=re.skillTypeID');

		$q->addQuery('inv.typeName AS skillTypeName');
		$q->addQuery('SUM(re.pointsPerDay) AS pointsPerDay');
		$q->addQuery('SUM(re.remainderPoints + (re.pointsPerDay * TIMESTAMPDIFF(SECOND, re.researchStartDate, NOW())/24/60/60)) AS currentPoints');
		$q->addQuery('SUM(re.pointsPerDay / rd.datacoreCost) AS datacoresPerDay');
		$q->addQuery('SUM((re.remainderPoints + (re.pointsPerDay * TIMESTAMPDIFF(SECOND, re.researchStartDate, NOW())/24/60/60)) / rd.datacoreCost) AS currentDatacores');
		$q->addGroup('re.skillTypeID');

		if ($this->_entity == 'user') {
			$acl = EveFactory::getACL();
			$chacracterIDs = $acl->getUserCharacterIDs();
			if ($chacracterIDs) {
				$q->addWhere('re.characterID IN ('.implode(', ', $chacracterIDs).')');
			} else {
				$q->addWhere('0 = 1');
			}
		} else {
			$characterID = intval($this->getState('list.characterID'));
			$q->addWhere('re.characterID = %1$s', $characterID);
		}
		return $q->loadObjectList();
	}

}
