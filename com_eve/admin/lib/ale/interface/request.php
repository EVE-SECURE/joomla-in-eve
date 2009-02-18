<?php
defined('ALE_BASE') or die('Restricted access');

interface AleInterfaceRequest {
	
	/**
	 * Constructor
	 *
	 * @param array $config
	 */
	public function __construct(array $config = array());
	
	/**
	 * Fetch respone from target URL
	 *
	 * @param string $url
	 * @param array $params
	 */
	public function query($url, array $params = null);
	
}
