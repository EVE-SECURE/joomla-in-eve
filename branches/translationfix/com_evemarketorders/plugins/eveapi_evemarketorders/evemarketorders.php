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
class plgEveapiEveMarketOrders extends EveApiPlugin {
	private $_fields;
	function __construct($subject, $config = array()) {
		parent::__construct($subject, $config);
		$this->_fields = array(
		  'entityID',
		  'orderID',
		  'charID',
		  'stationID',
		  'volEntered',
		  'volRemaining',
		  'minVolume',
		  'orderState',
		  'typeID',
		  'range',
		  'accountKey',
		  'duration',
		  'escrow',
		  'price',
		  'bid',
		  'issued',
		);
	}
	
	private function _onDuplicateUpdate($field)
	{
		$dbo = JFactory::getDBO();
		return $dbo->nameQuote($field).'=VALUES('.$dbo->nameQuote($field).')';
	}
	
	private function _storeMarketOrders($xml, $entityID)
	{
		$dbo = JFactory::getDBO();
		$sql = 'INSERT INTO #__eve_marketorders (';
		$sql .= implode(',', array_map(array($dbo, 'nameQuote'), $this->_fields));
		$sql .= ") VALUES ";
		$values = array();
		foreach ($xml->result->orders->toArray() as $entry) {
			$entry['entityID'] = $entityID;
			$value = array();
			foreach ($this->_fields as $field) {
				$value[] = $dbo->quote($entry[$field]); 
			}
			$values[] = '('.implode(',', $value).')';
		}
		$sql .= implode(",", $values);
		$sql .= ' ON DUPLICATE KEY UPDATE ';
		$sql .= implode(',', array_map(array($this, '_onDuplicateUpdate'), array_slice($this->_fields, 2, -1)));
		if ($values) {
			$dbo->setQuery($sql);
			$dbo->query();
		}
	}
	
	
	public function onSetOwnerCorporation($userID, $characterID, $owner) 
	{
		$this->_setOwnerCorporation('corp', 'MarketOrders', $owner, $userID, $characterID);
	}
	
	public function charMarketOrders($xml, $fromCache, $options = array()) {
		$this->_storeMarketOrders($xml, $options['characterID']);
	}
	
	public function corpMarketOrders($xml, $fromCache, $options = array()) 
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
		$this->_storeMarketOrders($xml, $entityID);
	}
	
}
