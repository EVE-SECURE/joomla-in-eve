<?php

if (!defined('ALE_BASE')) {
	define('ALE_BASE', dirname(__FILE__));
}

if (!defined('ALE_CONFIG_DIR')) {
	define('ALE_CONFIG_DIR', ALE_BASE);
}

class AleFactory {
	/**
	 * Instances of Ale classes
	 *
	 * @var array
	 */
	private static $instances = array();
	
	/**
	 * Look for class within Ale directory
	 *
	 * @param string $name
	 * @param string $type
	 * @return string
	 */
	private static function _class($name, $type = '') {
		$class = 'Ale'.ucfirst($type).$name;
		if (class_exists($class)) {
			return $class;
		}
		$path = ALE_BASE.DIRECTORY_SEPARATOR;
		if ($type) {
			$path .= strtolower($type).DIRECTORY_SEPARATOR; 
		}
		$path .= strtolower($name).'.php';
		if (!file_exists($path)) {
			throw new Exception('could not find');
		}
		require_once $path;
		if (!class_exists($class)) {
			throw new Exception('class definition missing: '.$class);
		}
		return $class;
	}
	
	/**
	 * Get value from array if exists, or return default
	 *
	 * @param array $array
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	private static function _default(&$array, $key, $default) {
		return isset($array[$key]) ? $array[$key] : $default;
	}
	
	/**
	 * Initialise new instance of Ale class
	 *
	 * @param string $name
	 * @param array $config
	 */
	private static function init($name, $config) {
		$_name = strtolower($name);
		//echo ALE_CONFIG_DIR.DIRECTORY_SEPARATOR.$_name.'.ini';
		if (file_exists(ALE_CONFIG_DIR.DIRECTORY_SEPARATOR.$_name.'.ini')) {
			$tmp = parse_ini_file(ALE_CONFIG_DIR.DIRECTORY_SEPARATOR.$_name.'.ini', true);
		} else {
			throw new Exception('config not found');
		}
		if ($tmp === false) {
			throw new Exception('bad config');
		}
		
		$mainConfig 	= self::_default($tmp, 'main', array());
		$cacheConfig 	= self::_default($tmp, 'cache', array());
		$requestConfig 	= self::_default($tmp, 'request', array());
		
		if (is_array($config)) {
			foreach($config as $key => $value) {
				$split = explode('.', $key, 2);
				if (count($split) == 2) {
					if ($split[0] == 'main' || $split[0] == 'cache' || $split[0] == 'request') {
						$key = $split[0];
						$value = array($split[1] => $value);
					}
					if ($key == 'main' && is_array($value)) {
						foreach ($value as $k => $v) {
							$mainConfig[$k] = $v;
						}
					} elseif ($key == 'cache' && is_array($value)) {
						foreach ($value as $k => $v) {
							$cacheConfig[$k] = $v;
						}
					} elseif ($key == 'request' && is_array($value)) {
						foreach ($value as $k => $v) {
							$requestConfig[$k] = $v;
						}
					} else {
						$mainConfig[$key] = $value;
					}
				} else {
					$mainConfig[$key] = $value;
				}
			}
		}
		
		$mainName 		= self::_default($mainConfig, 'class', $name);
		$cacheName 		= self::_default($cacheConfig, 'class', 'Dummy');
		$requestName 	= self::_default($requestConfig, 'class', 'Curl');
		
		$mainClass 		= self::_class($mainName);
		$cacheClass 	= self::_class($cacheName, 'cache');
		$requestClass 	= self::_class($requestName, 'request');
		
		$request 		= new $requestClass($requestConfig);
		$cache 			= new $cacheClass($cacheConfig); 
		$main 			= new $mainClass($request, $cache, $mainConfig);

		self::$instances[$_name] = $main;
		
	}
	
	/**
	 * Loads configuration file and returns instance of Ale class
	 * If object already exists and no new config is provided,
	 * method returns old instance
	 *
	 * @param string $name file name
	 * @param array $config
	 * @return AleBase AleBase object or its descendant
	 */
	public function get($name, $config = false) {
		$_name = strtolower($name);
		if ($config !== false || !isset(self::$instances[$_name])) {
			self::init($name, $config);
		}
		return self::$instances[$_name];
	}
	
	/**
	 * Loads configuration file and returns instance of AleEVEOnline class
	 *
	 * @param array $config
	 * @return AleEVEOnline
	 */
	public function getEVEOnline($config = false) {
		return self::get('EVEOnline', $config);
	}
	
}
