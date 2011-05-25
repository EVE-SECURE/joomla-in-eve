<?php
defined('_JEXEC') or die();

class EveTable extends JTable {
	private $_loaded;
	
	function __construct($table, $key, &$db) {
		parent::__construct($table, $key, $db);
		$this->_loaded = false;
	}
	
	function load($oid = null) {
		$this->_loaded = parent::load($oid);
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = $oid;
		}
		return $this->_loaded;
	}
	
	function isLoaded() {
		return $this->_loaded;
	}
	
	function store($updateNulls = false) {
		$k = $this->_tbl_key;

		if ($this->_loaded) {
			$ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
		}
		else {
			$ret = $this->_db->insertObject( $this->_tbl, $this );
		}
		if( !$ret ) {
			$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
			return false;
		}
		else {
			$this->_loaded = true;
			return true;
		}
	}
	
}

/*
TODO: rework table for multiple field primary keys
class EveTableRework extends JTable {
	private $_loaded;
	
	function __construct($table, $key, &$db) {
		if (!is_array($key)) {
			$key = array($key);
		}
		parent::__construct($table, $key, $db);
		$this->_loaded = false;
	}
	
	function _buildWhere() {
		$db =& $this->getDBO();
		
		$result = '';
		foreach ($this->_tbl_key as $key) {
			if ($result) {
				$result .= ' AND ';
			}
			$result .= $key.' = '.$db->Quote($this->$key);
		}
		return $result;
	}
	
	function _updateObject() {
		$db =& $this->getDBO();
		
		$where .= '';
		$fmtsql = 'UPDATE '.$db->nameQuote($table).' SET %s WHERE %s';
		$tmp = array();
		foreach (get_object_vars( $this ) as $k => $v) {
			if( is_array($v) or is_object($v) or $k[0] == '_' ) { // internal or NA field
				continue;
			}
			if( in_array($k, $this->_tbl_key) ) { // PK not to be updated
				if ($where) {
					$where .= ' AND ';
				}
				$where .= $keyName . '=' . $db->Quote( $v );
			} else {
				if ($v === null) {
					if ($updateNulls) {
						$val = 'NULL';
					} else {
						continue;
					}
				} else {
					$val = $db->isQuoted( $k ) ? $db->Quote( $v ) : (int) $v;
				}
				$tmp[] = $db->nameQuote( $k ) . '=' . $val;
			}
		}
		$this->setQuery( sprintf( $this->_tbl, implode( ",", $tmp ) , $where ) );
		return $this->query();		
	}
	
	function reset() {
		$k = $this->_tbl_key;
		foreach ($this->getProperties() as $name => $value) {
			if (!in_array($name, $k)) {
				$this->$name = $value;
			}
		}
		
	}
	
	function load($oid = null) {
		$this->_loaded = false;

		$keys = $this->_tbl_key;
		
		$args = func_get_args();
		
		if ($oid !== null && count($k) == count($args)) {	
			foreach ($keys as $i => $key) {
				$this->$key = $args[$i];
			}
		}
		foreach ($keys as $i => $key) {
			if ($this->$key === null) {
				return false;
			}
		}
		
		$this->reset();

		$db =& $this->getDBO();

		$query = 'SELECT *'
		. ' FROM '.$this->_tbl
		. ' WHERE '.$this->_buildWhere();
		$db->setQuery( $query );

		if ($result = $db->loadAssoc( )) {
			$this->_loaded = $this->bind($result); 
			return $this->_loaded;
		} else {
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}
	
	function isLoaded() {
		return $this->_loaded;
	}
	
	function store($updateNulls = false) {
		$k = $this->_tbl_key;

		if ($this->_loaded) {
			$ret = $this->_updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
		}
		else {
			$ret = $this->_db->insertObject( $this->_tbl, $this );
		}
		if( !$ret ) {
			$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
			return false;
		}
		else {
			$this->_loaded = true;
			return true;
		}
	}
	
}
*/