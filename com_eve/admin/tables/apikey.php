<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class EveTableApikey extends EveTable {
	/** @var int */
	var $keyID 	= null;
	/** @var string */
	var $vCode 	= null;
	/** @var int */
	var $user_id 		= null;
	/** @var string */
	var $type = null;
	/** @var int */
	var $mask = null;
	/** @var string */
	var $status 	= null;
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
		parent::__construct( '#__eve_apikeys', 'keyID', $dbo );
	}

	function store($updateNulls = false) {
		$tmp = $this->vCode;
		if (!empty($this->vCode)) {
			$this->vCode = $this->encrypt($this->vCode);
		}
		$result = parent::store($updateNulls);
		$this->vCode = $tmp;
		return $result;
	}

	function load($oid = null) {
		$result = parent::load($oid);
		if ($result) {
			$this->vCode = $this->decrypt($this->vCode);
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

	public function delete($oid)
	{
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval( $oid );
		}

		$query = 'DELETE FROM '.$this->_db->nameQuote('#__eve_schedule').
				' WHERE '.$this->_tbl_key.' = '. $this->_db->Quote($this->$k);
		$this->_db->setQuery( $query );

		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$query = 'DELETE FROM '.$this->_db->nameQuote('#__eve_apikey_entities').
				' WHERE '.$this->_tbl_key.' = '. $this->_db->Quote($this->$k);
		$this->_db->setQuery( $query );

		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return parent::delete();
	}

}
