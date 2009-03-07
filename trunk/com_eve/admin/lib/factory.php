<?php
defined('_JEXEC') or die();

class EveFactory {
	static $instances = array();
	static $config = array(
		'config'			=> false,
		
		'main.class' 		=> 'EVEOnline',
		'main.host' 		=> "http://api.eve-online.com/",
		'main.suffix' 		=> ".xml.aspx",
		'main.parserClass'	=> "AleParserXMLElement",
		'main.requestError' => "throwException",
		'main.serverError' 	=> "throwException",
	
		'cache.class' 		=> 'Joomla',
		'cache.table' 		=> '#__eve_alecache',
		
		'request.class' 	=> null,
		'request.timeout'	=> 30,
	);
	
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
			$params = &JComponentHelper::getParams('com_eve');
			
			require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'lib'.DS.'ale'.DS.'factory.php';
			self::$config['request.class'] = $params->get('ale_requestclass', 'Curl');
			$instance = AleFactory::getEVEOnline(self::$config);
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
