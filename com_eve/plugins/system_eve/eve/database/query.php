<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Core
 * @copyright	Copyright (C) 2008 Pavol Kovalik. All rights reserved.
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

class JQuery extends JObject {
	protected $type = null;
	protected $dbo = null;
	protected $_limit = 0;
	protected $_offset = 0;
	protected $_query = '';
	
	
	public function __construct($dbo = null) {
		parent::__construct();
		if (is_null($dbo)) {
			$this->dbo =& JFactory::getDBO();
		} else {
			$this->dbo = $dbo;
		}
		$this->type = 'select';
	}
	
	protected function _checkMap($name) {
		return isset($this->$name) && is_array($this->$name) && !empty($this->$name);
	}
	
	protected function _addMap($name, $value, $id = null) {
		if (!isset($this->$name)) {
			$this->$name = array();
		}
		
		if (is_object($value)) {
			
		} elseif (is_array($value)) {
			$value = JArrayHelper::toObject($value);
		} else {
			$tmp = array('value'=>$value);
			$value = JArrayHelper::toObject($tmp);
		}
		
		if (is_null($id)) {
			$this->{$name}[] = $value;
		} else {
			$this->{$name}[$id] = $value;
		}
	}
	
	function quote($text, $escaped = true) {
		return $this->dbo->quote($text, $escaped);
	}
	
	function getEscaped($text, $extra=false) {
		return $this->dbo->getEscaped($text, $extra);
	}
	
	function addQuery($query) {
		$args = func_get_args();
		foreach($args as $query) {
			$this->_addMap('query', $query);
		}
	}
	
	function addTable($table, $alias = null) {
		$value = array('table'=>$table, 'alias'=>$alias);
		$this->_addMap('table', $value, $alias);
	}
	
	function addJoin($table, $alias, $join, $type = 'left') {
		$value = array('table'=>$table, 'alias'=>$alias, 'join'=>$join, 'type'=>strtoupper($type));
		$this->_addMap('join', $value, $alias);
	}
	
	function addWhere($query) {
		$args = func_get_args();
		if (count($args) > 1) {
			array_shift($args);
			foreach($args as $i => $arg) {
				$args[$i] = $this->getEscaped($arg);
			}
			$query = vsprintf($query, $args);
		}
		$this->_addMap('where', $query);
	}
		
	function addQuotedWhere($query, $value) {
		$query .= $this->dbo->quote($value);
		$this->_addMap('where', $query);
	}
	
	function addGroup($query) {
		$args = func_get_args();
		foreach($args as $query) {
			$this->_addMap('group', $query);
		}
	}
	
	function addHaving($query) {
		$args = func_get_args();
		if (count($args) > 1) {
			array_shift($args);
			foreach($args as $i => $arg) {
				$args[$i] = $this->getEscaped($arg);
			}
			$query = vsprintf($query, $args);
		}
		$this->_addMap('having', $query);
	}
	
	function addQuotedHaving($query, $value) {
		$query .= $this->dbo->quote($value);
		$this->_addMap('having', $query);
	}
	
	function addOrder($order, $direction = 'ASC') {
		$value = array('order'=>$order, 'direction'=>$direction);
		$this->_addMap('order', $value);
	}
	
	function setLimit($limit, $offset = 0) {
		$this->_limit = $limit;
		$this->_offset = $offset;
	}
	
	function clear() {
		
	}
	
	protected function _prepareQuery() {
		if ($this->_checkMap('query')) {
			$tmp = array();
			foreach ($this->query as $query) {
				$tmp[] = $query->value;
			}
			return ' '.implode(', ', $tmp);
		} else {
			return ' *';
		}
	}
	
	protected function _prepareTable() {
		$q = ' (';
		$tmp = array();
		foreach ($this->table as $table) {
			if ($table->alias) {
				$tmp[] = "$table->table AS $table->alias";
			} else {
				$tmp[] = "$table->table";
			}
		}
		$q .= implode(', ', $tmp);
		$q .= ')';
		return $q;
	}
	
	protected function _prepareJoin() {
		if (!$this->_checkMap('join')) {
			return '';
		}
		$tmp = array();
		foreach ($this->join as $table) {
			$tmp[] = " $table->type JOIN $table->table AS $table->alias ON $table->join";
		}
		return implode(' ', $tmp);
		
	}
	
	protected function _prepareWhere() {
		if (!$this->_checkMap('where')) {
			return '';
		}
		$tmp = array();
		foreach ($this->where as $query) {
			$tmp[] = $query->value;
		}
		return ' WHERE '.implode(' AND ', $tmp);		
	}
	
	protected function _prepareGroup() {
		if (!$this->_checkMap('group')) {
			return '';
		}
		$tmp = array();
		foreach ($this->group as $query) {
			$tmp[] = $query->value;
		}
		return ' GROUP BY '.implode(', ', $tmp);
		
	}
	
	protected function _prepareHaving() {
		if (!$this->_checkMap('having')) {
			return '';
		}
		$tmp = array();
		foreach ($this->having as $query) {
			$tmp[] = $query->value;
		}
		return ' HAVING '.implode(' AND ', $tmp);
		
	}
	
	protected function _prepareOrder() {
		if (!$this->_checkMap('order')) {
			return '';
		}
		$tmp = array();
		foreach ($this->order as $order) {
			$tmp[] = "$order->order $order->direction";
		}
		
		return ' ORDER BY '.implode(', ', $tmp);
	}
	
	protected function _prepareLimit() {
		if ($this->_limit > 0 || $this->_offset > 0) {
			return ' LIMIT '.$this->_offset.', '.$this->_limit;
		} else {
			return '';
		}
	}
	
	public function prepareSelect() {
		$q = 'SELECT';
		$q .= $this->_prepareQuery();
		$q .= ' FROM';
		$q .= $this->_prepareTable();
		$q .= $this->_prepareJoin();
		$q .= $this->_prepareWhere();
		$q .= $this->_prepareGroup();
		$q .= $this->_prepareHaving();
		$q .= $this->_prepareOrder();
		$q .= $this->_prepareLimit();
		
		return $q;
	}
	
	public function query() {
		//echo $this->toString(),'.', $this->_limit, '.', $this->_offset; exit;
		$this->dbo->setQuery($this->toString());
		return $this->dbo->query();
	}
	
	public function loadResult() {
		$this->dbo->setQuery($this->toString());
		return $this->dbo->loadResult();
	}
	
	public function loadResultArray($numinarray = 0) {
		$this->dbo->setQuery($this->toString());
		return $this->dbo->loadResultArray($numinarray);
	}
	
	/**
	* Fetch a result row as an associative array
	*
	* @access	public
	* @return array
	*/
	public function loadAssoc() {
		$this->dbo->setQuery($this->toString());
		return $this->dbo->loadAssoc();
	}

	/**
	* Load a assoc list of database rows
	*
	* @access	public
	* @param string The field name of a primary key
	* @return array If <var>key</var> is empty as sequential list of returned records.
	*/
	public function loadAssocList($key='') {
		$this->dbo->setQuery($this->toString());
		return $this->dbo->loadAssocList($key);
	}
	
	
	public function loadObject() {
		$this->dbo->setQuery($this->toString());
		return $this->dbo->loadObject();
	}
	
	public function loadObjectList($key = '') {
		$this->dbo->setQuery($this->toString());
		return $this->dbo->loadObjectList($key);
	}
	
	public function toString() {
		switch ($this->type) {
			case 'select':
			default:
				return $this->prepareSelect();
		}
	}
	
	public function __toString() {
		return $this->toString();
	}
	
}
