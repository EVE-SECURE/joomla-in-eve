<?php
defined('ALE_BASE') or die('Restricted access');

define('ALE_CACHE_CACHED',  1);
define('ALE_CACHE_MISSING', null);
define('ALE_CACHE_EXPIRED', 0);

interface AleInterfaceCache {
	
	/**
	 * Constructor
	 *
	 * @param array $config
	 */
	public function __construct(array $config = array());
	
	/**
	 * Set host URL
	 *
	 * @param string $host
	 */
	public function setHost($host);
	
	/**
	 * Set call parameters
	 *
	 * @param string $path
	 * @param array $params
	 */
	public function setCall($path, array $params = array());
	
	/**
	 * Store content
	 *
	 * @param string $content
	 * @param string $cachedUntil
	 * @return null
	 */
	public function store($content, $cachedUntil);
	
	/**
	 * Update cachedUntil value of recent call
	 *
	 * @param string $time
	 */
	public function updateCachedUntil($time);
	
	/**
	 * Retrieve content as string
	 *
	 */
	public function retrieve();
	
	/**
	 * Check if target is stored  
	 *
	 * @return int|null
	 */
	public function isCached();
	
}
