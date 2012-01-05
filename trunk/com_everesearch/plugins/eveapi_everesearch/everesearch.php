<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Research
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

/**
 * Joomla! in EVE Api core plugin
 *
 * @author		Pavol Kovalik  <kovalikp@gmail.com>
 * @package		Joomla! in EVE
 * @subpackage	Core
 */
class plgEveapiEveresearch extends EveApiPlugin {
	private $_fields;
	function __construct($subject, $config = array()) {
		parent::__construct($subject, $config);
		$this->_fields = array(
		  'characterID',
		  'agentID',
		  'skillTypeID',
		  'researchStartDate',
		  'pointsPerDay',
		  'remainderPoints',
		);
	}

	private function _storeResearch($xml, $characterID)
	{
		$dbo = JFactory::getDBO();
		$sql = 'INSERT IGNORE INTO #__eve_research (';
		$sql .= implode(',', array_map(array($dbo, 'nameQuote'), $this->_fields));
		$sql .= ") VALUES ";
		$values = array();
		foreach ($xml->result->research->toArray() as $entry) {
			$entry['characterID'] = $characterID;
			$value = array();
			foreach ($this->_fields as $field) {
				$value[] = $dbo->quote($entry[$field]);
			}
			$values[] = '('.implode(',', $value).')';
		}
		$dbo->setQuery('DELETE FROM #__eve_research WHERE characterID='.(int) $characterID);
		$dbo->query();
		if ($values) {
			$sql .= implode(",", $values);
			$dbo->setQuery($sql);
			$dbo->query();
		}
	}


	public function charResearch($xml, $fromCache, $options = array()) {
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_everesearch'.DS.'tables');
		$this->_storeresearch($xml, $options['characterID']);
	}

}
