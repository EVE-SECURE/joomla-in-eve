<?php
defined('ALE_BASE') or die('Restricted access');

require_once ALE_BASE.DIRECTORY_SEPARATOR.'interface'.DIRECTORY_SEPARATOR.'cache.php';

class AleCacheJoomla implements AleInterfaceCache {
	private $dbo;
	private $host = '';
	private $path = '';
	private $params = array();
	private $table = null;
	
	public function __construct(array $config) {
		if (isset($config['db'])) {
			$this->dbo = $config['db'];
		} else {
			$this->dbo = JFactory::getDBO();
		}
	}
	
	/**
	 * Set host URL
	 *
	 * @param string $host
	 */
	public function setHost($host) {
		$this->table = null;
		$this->host = $host;
	}
	
	/**
	 * Set call parameters
	 *
	 * @param string $path
	 * @param array $params
	 */
	public function setCall($path, array $params = array()) {
		$this->path = $path;
		$this->params = $params;
		$param_str = '';
		foreach ($params  as $key => $value) {
			$param_str .= '&'.urlencode($key).'='.urlencode($value);
		}
		$param_str = sha1($param_str);
		
		$dbo = $this->dbo;
		$query = sprintf("SELECT * FROM #__ale_cache WHERE host='%s' AND path='%s' AND params='%s'", 
			$dbo->Quote($this->host), $dbo->Quote($this->path), $dbo->Quote($param_str));
		$dbo->Execute($query);
		$result = $dbo->loadResult();
		$this->table = new TableAleCache($dbo);
		
		if ($result) {
			$this->table->bind($result);
		} else {
			$this->table->host = $this->host;
			$this->table->path = $this->path;
			$this->table->params = $param_str;
		}
	}
	
	/**
	 * Store content
	 *
	 * @param string $content
	 * @return null
	 */
	public function store($content) {
		$this->table->content = $content;
		$this->table->store();
	}
	
	/**
	 * Update cachedUntil value of recent call
	 *
	 * @param string $time
	 */
	public function updateCachedUntil($time) {
		throw new BadMethodCallException('Not implemented');
	}
	
	/**
	 * Retrieve content as string
	 *
	 * @return string
	 */
	public function retrieve() {
		return $this->table->content;
	}
	
	/**
	 * Check if target is stored  
	 *
	 * @return int|null
	 */
	public function isCached() {
		if (!$this->table->id) {
			return ALE_CACHE_MISSING;
		}
		return ALE_CACHE_CACHED;
	}
	
}

class TableAleCache extends JTable {
	var $id = null;
	var $host = null;
	var $path = null;
	var $params = null;
	var $content = null;
	var $currentTime = null;
	var $cachedUntil = null;
	
	function __construct($dbo) {
		parent::__construct('#__ale_cache', 'id', $dbo);
	}
}

/*
 CREATE TABLE `jos_ale_cache` (
`id` INT NOT NULL AUTO_INCREMENT ,
`host` VARCHAR( 64 ) NOT NULL ,
`path` VARCHAR( 64 ) NOT NULL ,
`params` VARCHAR( 41 ) NOT NULL ,
`content` TEXT NULL ,
`currentTime` DATETIME NOT NULL ,
`cachedUntil` DATETIME NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM 


