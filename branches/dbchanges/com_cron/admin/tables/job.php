<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.database.table');

class CronTableJob extends JTable {
	public $id = null;
	public $pattern = null;
	public $type = null;
	public $plugin = null;
	public $event = null;
	public $state = null;
	public $next = null;
	
	public $minutes = null;
	public $hours = null;
	public $days = null;
	public $moths = null;
	public $weekdays = null;
	
	public $params = null;
	public $ordering = null;
	public $checked_out = null;
	public $checked_out_time = null;
			
	function __construct(&$db) {
		parent::__construct('#__cron_jobs', 'id', $db);
	}
}
