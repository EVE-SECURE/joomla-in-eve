<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class EveTableAccount extends EveTable {
	/** @var int */
	var $userID 	= null;
	/** @var string */
	var $apiKey 	= null;
	/** @var string */
	var $apiStatus 	= null;
	/** @var int */
	var $owner 		= null;
	/* checkout values */
	var $checked_out = null;
	var $checked_out_time = null;

	/** @var EveEncryptor */
	var $_config = false;


	/**
	 * @param database A database connector object
	 */
	function __construct( &$dbo )
	{
		parent::__construct( '#__eve_accounts', 'userID', $dbo );
	}

	function store($updateNulls = false) {
		$tmp = $this->apiKey;
		if (!empty($this->apiKey)) {
			$this->apiKey = $this->encrypt($this->apiKey);
		}
		$result = parent::store($updateNulls);
		$this->apiKey = $tmp;
		return $result;
	}

	function load($oid = null) {
		$result = parent::load($oid);
		if ($result) {
			$this->apiKey = $this->decrypt($this->apiKey);
		}
		return $result;
	}

	public function encrypt($raw)
	{
		$config = EveFactory::getConfig();
		if ($config->getValue('encryption.cipher')) {
			$iv = base64_decode($config->getValue('encryption.iv'));
			return base64_encode(mcrypt_encrypt($config->getValue('encryption.cipher'), $config->getValue('encryption.key'),
			$raw, $config->getValue('encryption.mode'), $iv));
				
			//return base64_encode(mcrypt_encrypt($config->method, $config->key, $raw, $config->mode, $config->iv));
		} else {
			return $raw;
		}
	}

	public function decrypt($encrypted)
	{
		$config = EveFactory::getConfig();
		if ($config->getValue('encryption.cipher')) {
			$iv = base64_decode($config->getValue('encryption.iv'));
			return trim(mcrypt_decrypt($config->getValue('encryption.cipher'), $config->getValue('encryption.key'),
			base64_decode($encrypted), $config->getValue('encryption.mode'), $iv));
		} else {
			return $encrypted;
		}
	}

}
