<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Character Tracking
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

jimport('joomla.application.component.model');

class EvechartrackingModelDbcheck  extends JModel {
	
	//check for required tables
	function getTableCheck() {
		$tables = array('mapDenormalize', 'staStations', 'invTypes');
		
		$result = array();
		$db = $this->getDBO();
		
		$sql = "SHOW TABLES LIKE '%s'";
		foreach ($tables as $table) {
			$_sql = sprintf($sql, $table);
			$db->Execute($_sql);
			$result[$table] = $db->loadObject();
		}
		
		return $result;
	}
	
}

?>