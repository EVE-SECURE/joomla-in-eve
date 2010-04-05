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

class EveHelper {
	static $ownerCorporationIDs = null;
	
	public static function getOwnerCoroprationIDs($dbo = null) 
	{
		if (is_null(self::$ownerCorporationIDs)) {
			if (empty($dbo)) {
				$dbo = JFactory::getDBO();
			}
			$q = EveFactory::getQuery($dbo);
			$q->addTable('#__eve_corporations', 'co');
			$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
			$q->addWhere('(co.owner OR al.owner)');
			$q->addQuery('co.corporationID');
			self::$ownerCorporationIDs = $q->loadResultArray();
		}
		return self::$ownerCorporationIDs;
	}

	/**
	 * Update apiStatus based on error code
	 *
	 * @param EveTableAccount $account
	 * @param int $errorCode
	 * @param bool $store
	 */
	public static function updateApiStatus($account, $errorCode, $store = false) 
	{
		switch ($errorCode) {
			case 200:
				$account->apiStatus = 'Limited';
				if ($store) {
					$account->store();
				}
				break;
			case 202:
			case 203:
			case 204:
			case 205:
			case 210:
			case 212:
				$account->apiStatus = 'Invalid';
				if ($store) {
					$account->store();
				}
				break;
			case 211:
				$account->apiStatus = 'Inactive';
				if ($store) {
					$account->store();
				}
				break;
		}
	}
	
	/**
	 * Schedule corporatin related API calls.
	 * You should use this when installing new component.
	 *
	 * @param string $plugin Plugin name
	 * @param bool $create Create plugin manually. Set to true if the plugin was just installed
	 */
	public static function scheduleApiCalls($name = null, $create = false)
	{
		$type = 'eveapi';
		$dbo = JFactory::getDBO();
		$q = new JQuery($dbo);
		$q->addTable('#__eve_corporations', 'co');
		$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
		$q->addJoin('#__eve_characters', 'ceo', 'ceo.characterID=co.ceoID');
		$q->addQuery('ceo.characterID', 'ceo.userID');
		$q->addWhere('(co.owner = 1 OR al.owner = 1)');
		$ceos = $q->loadObjectList();
		
		
		JPluginHelper::importPlugin('eveapi', $name);
		$dispatcher =& JDispatcher::getInstance();
		if ($create && $name) {
			$path	= JPATH_PLUGINS.DS.$type.DS.$name.'.php';
			require_once $path;
			$className = 'plg'.$type.$name;
			if(class_exists($className)) {
				$instance = new $className($dispatcher, array('type' => $type, 'name' => $name));
			}
		}
		
		foreach ($ceos as $ceo) {
			if ($ceo->userID && $ceo->characterID) {
				$dispatcher->trigger('onSetOwnerCorporation', array($ceo->userID, $ceo->characterID, 1));
			}
		}
	}
	
	public static function clearApiCalls($type, $call)
	{
		$dbo = JFactory::getDBO();
		
		$sql = sprintf('DELETE FROM #__eve_schedule WHERE apicall IN (SELECT id FROM #__eve_apicalls WHERE `type`=%s AND `call`=%s)', 
			$dbo->quote($type), $dbo->quote($call));
		$dbo->setQuery($sql);
		$dbo->query($sql);

		$sql = sprintf('DELETE FROM  #__eve_apicalls WHERE `type`=%s AND `call`=%s', 
			$dbo->quote($type), $dbo->quote($call));
		$dbo->setQuery($sql);
		$dbo->query($sql);
	}
	
	
}
