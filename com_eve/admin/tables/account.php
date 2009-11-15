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
	var $_encryptor = null;
	
	
	/**
	* @param database A database connector object
	*/
	function __construct( &$dbo )
	{
		parent::__construct( '#__eve_accounts', 'userID', $dbo );
	}
	
	function store($updateNulls = false) {
		$tmp = $this->apiKey;
		if (!is_null($this->_encryptor) && !is_null($this->apiKey)) {
			$this->apiKey = $this->_encryptor->encrypt($this->apiKey);
		}
		$result = parent::store($updateNulls);
		$this->apiKey = $tmp;
		return $result;
	}
	
	function load($oid = null) {
		$result = parent::load($oid);
		if ($result && !is_null($this->_encryptor)) {
			$this->apiKey = $this->_encryptor->decrypt($this->apiKey);
		}
		return $result;
	}
	
	public function setEncryptor($encryptor) {
		$this->_encryptor = $encryptor;
	}
}
