<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Character Sheet
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

class EvecharsheetModelEvecharsheet extends JModel {
	protected $dbdump;

	public function __construct($config = array())
	{
		parent::__construct($config);
		$eveparams = JComponentHelper::getParams('com_eve');
		$dbdump_database = $eveparams->get('dbdump_database');
		$this->dbdump = $dbdump_database ? $dbdump_database :'';
	}

	function getTableCheck() {
		$tables = array('invTypes', 'invGroups', 'chrAttributes', 'crtCertificates', 'crtClasses',
			'crtCategories', 'dgmAttributeTypes', 'dgmTypeAttributes', 'staStations');

		$result = array();
		$db = $this->getDBO();

		if ($this->dbdump) {
			$sql = "SHOW TABLES IN ".$this->dbdump." LIKE '%s'";
		} else {
			$sql = "SHOW TABLES LIKE '%s'";
		}
		foreach ($tables as $table) {
			$_sql = sprintf($sql, $table);
			$db->Execute($_sql);
			$result[$table] = $db->loadObject();
		}

		return $result;
	}

}