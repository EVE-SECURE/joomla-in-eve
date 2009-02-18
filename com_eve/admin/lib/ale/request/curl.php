<?php
defined('ALE_BASE') or die('Restricted access');

require_once ALE_BASE.DIRECTORY_SEPARATOR.'interface'.DIRECTORY_SEPARATOR.'request.php';

class AleRequestCurl implements AleInterfaceRequest  {
	
	/**
	 * Constructor
	 *
	 * @param array $config
	 */
	public function __construct(array $config = array()) {
		
	}
	
	/**
	 * Enter description here...
	 *
	 * @param resource $ch
	 * @param string $header
	 * @return int
	 */
	protected function readHeader($ch, $header) {
		//echo '-'.$header.'-<br>';
		$matches = array();
		if (!preg_match('#^HTTP/[0-9]\\.[0-9] +([0-9]+) +(.*)$#', $header, $matches)) {
			return strlen($header);
		}
		if ($matches[1] >= 400) {
			curl_close($ch);
			throw new Exception($matches[2], $matches[1]);
		}
		return strlen($header);
	}
	
	/**
	 * Fetch respone from target URL
	 *
	 * @param string $url
	 * @param array $params
	 */
	public function query($url, array $params = null) {
		//curl magic
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		if ($params) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'readHeader'));
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		$contents = trim(@curl_exec($ch));
		
		//chceck for connection errors
		$errno = curl_errno($ch);
		if ($errno > 0) {
			$errstr = curl_error($ch);
			//TODO: API exception
			curl_close ($ch);
			throw new Exception($errstr, $errno);
		}
		
		curl_close ($ch);
		
		return $contents;
	}

}
