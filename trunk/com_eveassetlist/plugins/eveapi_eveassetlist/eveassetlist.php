<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Asset List
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
class plgEveapiEveAssetList extends EveApiPlugin {
	private $_fields;
	function __construct($subject, $config = array()) {
		parent::__construct($subject, $config);
		$this->_fields = array(
		  'entityID',
		  'containerID',
		  'itemID',
		  'locationID',
		  'typeID',
		  'quantity',
		  'flag',
		  'singleton',
		);
	}

	private function _storeAssetList($xml, $entityID)
	{
		$dbo = JFactory::getDBO();
		$sql = 'DELETE FROM #__eve_assets WHERE entityID='.$dbo->quote($entityID);
		$dbo->setQuery($sql);
		$dbo->query();

		$sql = 'INSERT IGNORE INTO #__eve_assets (';
		$sql .= implode(',', array_map(array($dbo, 'nameQuote'), $this->_fields));
		$sql .= ") VALUES ";
		$values = array();
		foreach ($xml->result->assets as $asset) {
			$asset->entityID = $entityID;
			$value = array();
			foreach ($this->_fields as $field) {
				if (isset($asset->$field)) {
					$value[] = $dbo->quote($asset->$field);
				} else {
					$value[] = $dbo->quote(0);
				}
			}
			$values[] = '('.implode(',', $value).')';
			if (isset($asset->contents)) {
				foreach ($asset->contents as $content) {
					$content->entityID = $entityID;
					$content->containerID = $asset->itemID;
					$content->locationID = $asset->locationID;
					$value = array();
					foreach ($this->_fields as $field) {
						if (isset($content->$field)) {
							$value[] = $dbo->quote($content->$field);
						} else {
							$value[] = $dbo->quote(0);
						}
					}
					$values[] = '('.implode(',', $value).')';
				}
			}
			if (count($values) > 1000) {
				$_sql = $sql . implode(",", $values);
				$dbo->setQuery($_sql);
				$dbo->query();
			}
		}

		if ($values) {
			$_sql = $sql . implode(",", $values);
			$dbo->setQuery($_sql);
			$dbo->query();
		}
	}


	public function onSetOwnerCorporation($userID, $characterID, $owner)
	{
		$this->_setOwnerCorporation('corp', 'AssetList', $owner, $userID, $characterID);
	}

	public function charAssetList($xml, $fromCache, $options = array()) {
		$this->_storeAssetList($xml, $options['characterID']);
	}

	public function corpAssetList($xml, $fromCache, $options = array())
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
		$this->_storeAssetList($xml, $entityID);
	}

}
