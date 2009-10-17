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
	
	
	/**
	* @param database A database connector object
	*/
	function __construct( &$dbo )
	{
		parent::__construct( '#__eve_accounts', 'userID', $dbo );
	}
		
}
