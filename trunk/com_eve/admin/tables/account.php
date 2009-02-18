<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class TableAccount extends EveTable {
	/** @var int */
	var $userID 	= null;
	/** @var string */
	var $apiKey 	= null;
	/** @var string */
	var $apiStatus 	= null;
	/** @var int */
	var $owner 		= null;
	
	
	/**
	* @param database A database connector object
	*/
	function __construct( &$dbo )
	{
		parent::__construct( '#__eve_accounts', 'userID', $dbo );
	}
		
}
?>