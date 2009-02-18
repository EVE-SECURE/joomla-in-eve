<?php
defined('ALE_BASE') or die('Restricted access');

require_once ALE_BASE.DIRECTORY_SEPARATOR.'interface'.DIRECTORY_SEPARATOR.'cache.php';

class AleCacheDummy implements AleInterfaceCache {
	private $host = '';
	private $path = '';
	private $params = array();
	
	/**
	 * Constructor
	 *
	 * @param array $config
	 */
	public function __construct(array $config = array()) {
		//pass
	}
	
	/**
	 * Set host URL
	 *
	 * @param string $host
	 */
	public function setHost($host) {
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
	}
	
	/**
	 * Store content
	 *
	 * @param string $content
	 * @param string $cachedUntil
	 * @return null
	 */
	public function store($content, $cachedUntil) {
		//pass
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
		throw new BadMethodCallException('Not implemented');
	}
	
	/**
	 * Check if target is stored  
	 *
	 * @return int|null
	 */
	public function isCached() {
		return ALE_CACHE_MISSING;
	}
	
}
