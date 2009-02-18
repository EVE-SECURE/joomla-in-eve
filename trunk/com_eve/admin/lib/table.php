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