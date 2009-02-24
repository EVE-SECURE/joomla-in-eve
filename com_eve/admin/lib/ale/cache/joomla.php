<?php
defined('ALE_BASE') or die('Restricted access');

require_once ALE_BASE.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'abstractdb.php';

class AleCacheJoomla extends AleCacheAbstractDB {
	
	function __construct(array $config = array()) {
		parent::__construct($config);
		if (isset($config['db']) && is_resource($config['db'])) {
			$this->db = $config['db'];
		} else {
			$this->db = JFactory::getDBO();
		}
	}
	
	protected function escape($string) {
		return $this->db->getEscaped($string);
	}
	
	protected function &execute($query) {
		$result = $this->db->Execute($query);
		if ($result === false) {
			throw new AleExceptionCache($this->db->getErrorMsg(), $this->db-getErrorNum());
		}
		$result = $this->db->loadAssoc();
		return $result;
	}
	
	protected function &fetchRow(&$result) {
		return $result;
	}
	
	protected function freeResult(&$result) {
	}
			
}
