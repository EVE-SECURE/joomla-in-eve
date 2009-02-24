<?php
defined('_JEXEC') or die();

class EveFactory {
	static $instances = array();
	
	static function getQuery($dbo = null) {
		if (!isset($dbo)) {
			$dbo = JFactory::getDBO();
		}
		$q = new JQuery($dbo);
		return $q;
	}
	
	static function getAleEVEOnline($dbo = null) {
		static $instance;
		if (empty($dbo)) {
			$dbo = JFactory::getDBO();
		}
		if (!isset($instance)) {
			require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'lib'.DS.'ale'.DS.'factory.php';
			$instance = AleFactory::getEVEOnline(array('db' => $dbo));
		}
		return $instance;
	}
	
	static function getInstance($table, $id = null, $config = array()) {
		if (!array_key_exists('dbo', $config))  {
			$config['dbo'] =& JFactory::getDBO();
		}
		
		if (!$id) {
			$instance =& JTable::getInstance($name, 'Table', $config);
			return $instance;
		}
		
		$_table = strtolower($table);
		 
		if (!isset(self::$instances[$_table])) {
			self::$instances[$_table] = array();
		}
		
		if (!isset(self::$instances[$_table][$id])) {
			$instance =& JTable::getInstance($table, 'Table', $config);
			$instance->load((int) $id);
			self::$instances[$_table][$id] = $instance;
		}
		
		return self::$instances[$_table][$id];
		
	}
}
