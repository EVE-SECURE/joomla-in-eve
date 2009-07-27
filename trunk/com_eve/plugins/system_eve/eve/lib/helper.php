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
	
	static function getOwnerCoroprationIDs($dbo = null) {
		if (is_null(self::$ownerCorporationIDs)) {
			if (empty($dbo)) {
				$dbo = JFactory::getDBO();
			}
			$q = self::getQuery($dbo);
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
	 * @param TableAccount $account
	 * @param int $errorCode
	 * @param bool $store
	 */
	function updateApiStatus($account, $errorCode, $store = false) {
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
	
	
}
