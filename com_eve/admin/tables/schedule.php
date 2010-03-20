<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Core
 * @copyright	Copyright (C) 2008 Pavol Kovalik. All rights reserved.
 * @license		GNU/GPL, see http://www.gnu.org/licenses/gpl.html
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class EveTableSchedule extends JTable {
	var $id = null;
	var $apicall = null;
	var $userID = null;
	var $characterID = null;
	var $next = null;
	var $published = null;
	
	/**
	* @param database A database connector object
	*/
	function __construct( &$dbo )
	{
		parent::__construct( '#__eve_schedule', 'id', $dbo );
	}
	
	function setApicall($type, $call, $params = null) {
		$q = EveFactory::getQuery();
		$q->addTable('#__eve_apicalls', 'ap', 'sc.apicall=ap.id');
		$q->addQuery('ap.id');
		$q->addWhere("ap.type='%s' AND ap.call='%s'", $type, $call);
		if ($params) {
			$q->addWhere("ap.params='%s'", $params);
		}
		$this->apicall = $q->loadResult();
	}
	
	function loadExtra($type, $call, $userID = null, $characterID = null, $params = null) {
		if (is_array($params)) {
			$params = json_encode($params);
		}
		$q = EveFactory::getQuery();
		$q->addTable('#__eve_schedule', 'sc');
		$q->addJoin('#__eve_apicalls', 'ap', 'sc.apicall=ap.id');
		$q->addQuery('sc.*', 'ap.id AS apicall');
		$q->addWhere("ap.type='%s' AND ap.call='%s'", $type, $call);
		if ($userID) {
			$q->addWhere("sc.userID='%s'", $userID);
		}
		if ($characterID) {
			$q->addWhere("sc.characterID='%s'", $characterID);
		}
		if ($params) {
			$q->addWhere("ap.params='%s'", $params);
		}
		$result = $q->loadAssoc();
		if ($result) {
			return $this->bind($result);
		}
		$this->userID = $userID;
		$this->characterID = $characterID;
		$this->setApicall($type, $call, $params);
		return false;
	}
}