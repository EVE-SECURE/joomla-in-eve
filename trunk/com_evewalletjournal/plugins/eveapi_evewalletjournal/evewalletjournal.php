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

/**
 * Joomla! in EVE Api core plugin
 *
 * @author		Pavol Kovalik  <kovalikp@gmail.com>
 * @package		Joomla! in EVE
 * @subpackage	Core
 */
class plgEveapiEveWalletJournal extends EveApiPlugin {
	private $_fields;
	function __construct($subject, $config = array()) {
		parent::__construct($subject, $config);
		$this->_fields = array(
		  'entityID',
		  'accountKey',
		  'date',
		  'refID',
		  'refTypeID',
		  'ownerName1',
		  'ownerID1',
		  'ownerName2',
		  'ownerID2',
		  'argName1',
		  'argID1',
		  'amount',
		  'balance',
		  'reason'
		  );
	}

	private function _storeWalletJournal($xml, $entityID, $accounKey = 1000)
	{
		$dbo = JFactory::getDBO();
		$sql = 'INSERT IGNORE INTO #__eve_walletjournal (';
		$sql .= implode(',', array_map(array($dbo, 'nameQuote'), $this->_fields));
		$sql .= ") VALUES ";
		$values = array();
		foreach ($xml->result->transactions->toArray() as $entry) {
			$entry['accountKey'] = $accounKey;
			$entry['entityID'] = $entityID;
			$value = array();
			foreach ($this->_fields as $field) {
				$value[] = $dbo->quote($entry[$field]);
			}
			$values[] = '('.implode(',', $value).')';
		}
		if ($values) {
			$sql .= implode(",", $values);
			$dbo->setQuery($sql);
			$dbo->query();
		}
	}


	public function charWalletJournal($xml, $fromCache, $options = array()) 
	{
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_evewalletjournal'.DS.'tables');
		$this->_storeWalletJournal($xml, $options['characterID']);
	}

	public function corpWalletJournal($xml, $fromCache, $options = array()) 
	{
		if (!isset($options['corporationID'])) {
			$characterID = JArrayHelper::getValue($options, 'characterID');
			$character = EveFactory::getInstance('Character', $characterID);
			$entityID = $character->corporationID;
		} else {
			$entityID = $options['corporationID'];
		}
		if (!$entityID) {
			//TODO: some reasonable error?
			return;
		}
		$this->_storeWalletJournal($xml, $entityID, $options['accountKey']);
	}

	public function eveRefTypes($xml, $fromCache, $options = array())
	{
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_evewalletjournal'.DS.'tables');
		foreach ($xml->result->refTypes->toArray() as $refType) {
			//FIXME: duplicate entries
			$table = JTable::getInstance('Reftype', 'EvewalletjournalTable');
			$table->bind($refType);
			$table->store();
		}
	}

}
