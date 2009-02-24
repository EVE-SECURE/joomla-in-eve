<?php
defined('ALE_BASE') or define('ALE_BASE', dirname(__FILE__));

require_once ALE_BASE.DIRECTORY_SEPARATOR.'util'.DIRECTORY_SEPARATOR.'context.php';


class AleBase {
	
	/** 
	 * @var AleInterfaceRequest 
	 */
	protected $request;
	/** 
	 * @var AleInterfaceCache
	 */
	protected $cache;
	/** 
	 * @var array
	 */
	protected $default = array(
		'host' => '',
		'suffix' => '',
		'parserClass' => 'SimpleXMLElement' 
		);
	/** 
	 * @var array
	 */
	protected $config;
	
	protected $fromCache;
	
	function __construct(AleInterfaceRequest $request, AleInterfaceCache $cache, array $config = array()) {
		$this->request = $request;
		$this->cache = $cache;
		$this->config = array();
		
		foreach ($this->default as $key => $value) {
			$this->config[$key] = isset($config[$key]) ? $config[$key] : $value;
		}
		
		if ($this->config['parserClass'] != 'string' && !class_exists($this->config['parserClass'])) {
			//let's try to load internal class
			$file = preg_replace('/^AleParser/', '', $this->config['parserClass']);
			$path = ALE_BASE.DIRECTORY_SEPARATOR.'parser'.DIRECTORY_SEPARATOR.strtolower($file).'.php';
			if (!file_exists($path)) {
				throw new LogicException(sprintf('Cannot find Parser class [%s] in file \'%s\'', $this->config['parserClass'], $path));
			}
			require_once $path;
			if (!class_exists($this->config['parserClass'])) {
				throw new LogicException(sprintf('Cannot find Parser class [%s] in file \'%s\'', $this->config['parserClass'], $path));
			}
		}
		$this->cache->setHost($this->config['host']);
	}
	
	/**
	 * Extract cached until time
	 *
	 * @param string $content
	 * @return string
	 */
	protected function getCachedUntil($content) {
		return null;
	}
	
	/**
	 * Return string or parsed XML as object, based on configuration
	 *
	 * @param string $content
	 * @param bool $useCache
	 * @return mixed 
	 */
	protected function handleContent($content, &$useCache = true) {
		if ($this->config['parserClass'] == 'string') {
			return $content;
		}
		
		$parserClass = $this->config['parserClass'];
		$content = new $parserClass($content);
		return $content; 
	}
	
	/**
	 * Available only for this class or AleUtilContext object
	 *
	 * @param array $context segments of URI path
	 * @param array $arguments variable retrieved by __call method
	 * @return unknown
	 */
	protected function _retrieveXml(array $context, array $arguments) {
		$path = implode('/', $context);
		$params = isset($arguments[0]) && is_array($arguments[0]) ? $arguments[0] : array();
		return $this->retrieveXml($path, $params);
	}
	
	/**
	 * Retrieves XML document
	 *
	 * @param string $path
	 * @param array $params
	 * @param int $auth Credentials level. <b>EVEAPI_AUTH_DEFAULT is INVALID!</b>
	 * @return string|object Returns string or instance of parserClass
	 */
	public function retrieveXml($path, array $params) {
		//params should always have the same order
		ksort($params);
		
		$host = $this->config['host'];
		$suffix = $this->config['suffix'];
		$this->cache->setCall($path, $params);
		$this->fromCache = $this->cache->isCached();
		
		if ($this->fromCache == ALE_CACHE_CACHED) {
			$content = $this->cache->retrieve();
		} else {
			$content = $this->request->query($host.$path.$suffix, $params);
		}
		
		$useCache = true;
		$result = $this->handleContent($content, $useCache);
		
		if (($this->fromCache != ALE_CACHE_CACHED) && $useCache) {
			$cachedUntil = $this->getCachedUntil($content);
			$this->cache->store($content, $cachedUntil);
		}
		
		return $result;
		
	}
	
	/**
	 * Getter method
	 *
	 * @param string $name
	 * @return AleUtilContext
	 */
	function __get($name) {
		return new AleUtilContext($this, $name);
	}
	
	/**
	 * Overload method
	 *
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 */
	public function  __call($name, array $arguments) {
		return $this->_retrieveXml(array($name), $arguments);
	}
	
	/**
	 * Set configuration value
	 *
	 * @param string $key Key
	 * @param mixed $value Value
	 * @return mixed Previous value
	 */
	public function setConfig($key, $value = null) {
		if (!isset($this->default[$key])) {
			throw new InvalidArgumentException('setConfig: key is not valid');  
		}
		$result = $this->config[$key]; 
		$this->config[$key] = isset($vale) ? $this->default[$key] : $value;
		return $result;
	}
	
	/**
	 * Check if last result was fetched from cache;
	 *
	 * @return bool
	 */
	function isFromCache() {
		return (bool) $this->fromCache;
	}
	
}
