<?php
defined('_JEXEC') or die();

//add include path for character, corporation, alliance, account tables
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'tables');

class EveFactory {
	static $instances = array();
	static $namedInstances = array();
	static $aleconfig = array(
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

	/**
	 * Return instance of JQuery class, creating new if not exists
	 *
	 * @param JDatabase $dbo
	 * @return JQuery
	 */
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
			jimport( 'joomla.application.component.helper');
			$params = &JComponentHelper::getParams('com_eve');
			
			require_once JPATH_PLUGINS.DS.'system'.DS.'eve'.DS.'lib'.DS.'ale'.DS.'factory.php';
			self::$aleconfig['request.class'] = $params->get('ale_requestclass', 'Curl');
			self::$aleconfig['cache.maxDataSize'] = intval($params->get('ale_maxdatasize', 0));
			$instance = AleFactory::getEVEOnline(self::$aleconfig);
		}
		return $instance;
	}
	
	static function getInstance($table, $id = null, $config = array()) {
		if (!array_key_exists('dbo', $config))  {
			$config['dbo'] =& JFactory::getDBO();
		}
		
		if (!$id) {
			$instance =& JTable::getInstance($table, 'EveTable', $config);
			return $instance;
		}
		
		$_table = strtolower($table);
		 
		if (!isset(self::$instances[$_table])) {
			self::$instances[$_table] = array();
		}
		
		if (!isset(self::$instances[$_table][$id])) {
			$instance =& JTable::getInstance($table, 'EveTable', $config);
			$instance->load($id);
			self::$instances[$_table][$id] = $instance;
		}
		
		return self::$instances[$_table][$id];
	}
	
	static function getInstanceByName($table, $key, $name, $config) {
		if (!array_key_exists('dbo', $config))  {
			$dbo = $config['dbo'] =& JFactory::getDBO();
		}
		$_table = strtolower($table);
		 
		if (!isset(self::$namedInstances[$_table])) {
			self::$namedInstances[$_table] = array();
		}
		
		if (!isset(self::$namedInstances[$_table][$name])) {
			$instance =& JTable::getInstance($table, 'EveTable', $config);
			$k = $this->_tbl_key;
			
			$instance->reset();
	
			$db =& $this->getDBO();
	
			$query = 'SELECT *'
			. ' FROM '.$instance->_tbl
			. ' WHERE '.$instance->$key.' = '.$db->Quote($name);
			$db->setQuery( $query );
	
			if (!($result = $db->loadAssoc( ))) {
				//$this->setError($db->getErrorMsg());
				return false;
			}
			$this->bind($result);

			self::$namedInstances[$_table][$name] = $instance->$k; 
			self::$instances[$_table][$instance->$k] = $instance;
		}
		$id = self::$namedInstances[$_table][$name];
		if ($id == null) {
			return false;
		}
		return self::getInstance($table, $id, $config); 
		
	}
	
	function getACL() {
		static $instance;
		
		if (!isset($instance)) {
			require_once JPATH_PLUGINS.DS.'system'.DS.'eve'.DS.'lib'.DS.'acl.php';
			$instance = new EveACL();
		}
		return $instance;
	}
	
	public function getConfig()
	{
		static $instance;
		
		if (!isset($instance)) {
			jimport('joomla.registry.registry');
			$instance = new JRegistry('eve');

			$names = array('encryption');
			foreach ($names as $name) {
				$className = 'EveConfig'.ucfirst($name);
				if (!class_exists($className)) {
					$fname = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'configs'.DS.$name.'.php';
					if (file_exists($fname)) {
						require_once $fname;
					}
				}
				if (class_exists($className)) {
					$config = new $className();
					$instance->loadObject($config, $name);
				}
			}
		}
		return $instance;
	}
	
}
