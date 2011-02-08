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
jimport('joomla.plugin.plugin');

/**
 * Joomla! in EVE Api core plugin
 *
 * @author		Pavol Kovalik  <kovalikp@gmail.com>
 * @package		Joomla! in EVE		
 * @subpackage	Core
 */
class plgCronEve extends JPlugin {
	function __construct($subject, $config = array()) {
		parent::__construct($subject, $config);
	}
	
	function onCronTick() {
		//
		$now = JFactory::getDate();
		
		$dbo = JFactory::getDBO();
		$q = EveFactory::getQuery($dbo);
		$q->addTable('#__eve_schedule', 'sc');
		$q->addJoin('#__eve_apicalls', 'ap', 'ap.id=sc.apicall');
		$q->addWhere('next <= '.$q->quote($now->toMySQL()));
		$q->addWhere('published');
		$q->addQuery('ap.*', 'sc.*');
		$list = $q->loadObjectList();
		
		JPluginHelper::importPlugin('eveapi');
		$dispatcher =& JDispatcher::getInstance();
		//$ale = new AleEVEOnline();
		$ale = EveFactory::getAleEVEOnline($dbo);
		
		$utc_tz = new DateTimeZone('UTC');
		//JDate::toMySQL()
		
		foreach ($list as $row) {
			try {
				if ($row->authentication != 'None') {
					$account = EveFactory::getInstance('account', $row->userID);
					if ($row->authorization == 'Limited' && ($account->apiStatus != 'Limited' && $account->apiStatus != 'Full')) {
						continue;
					}
					if ($row->authorization == 'Full' && $account->apiStatus != 'Full') {
						continue;
					}
					$ale->setCredentials($account->userID, $account->apiKey);
				}
				if ($row->authentication == 'Character') {
					$ale->setCharacterID($row->characterID);
				} else {
					$ale->setCharacterID(null);
				}
				$next = null;
				$type = $row->type;
				$call = $row->call;
				if ($row->params) {
					$params = json_decode($row->params, true);
				} else {
					$params = array();
				}
				while (true) {
					$xml = $ale->$type->$call($params);
					$params['userID'] = $row->userID;
					$params['characterID'] = $row->characterID;
					$dispatcher->trigger($type.$call,  
						array($xml, $ale->isFromCache(), $params));
					$next = new DateTime((string) $xml->cachedUntil, $utc_tz);
					if (!$row->paginationAttrib) {
						break;
					}
					$rowsetName = $row->paginationRowsetName;
					$count = count($xml->result->$rowsetName);
					if ((0 <= $count) && ($count < $row->paginationPerPage)) {
						break;
					}
					$xpathStr = '/eveapi/result/rowset/row[last()]/@'.$row->paginationAttrib;
					$beforeParam = (string)reset($xml->xpath($xpathStr));
					$params[$row->paginationParam] = $beforeParam;
				}
				
			}
			//TODO: update cacheUntil on error
			catch (AleExceptionEVEAuthentication $e) {
				//FIXME: use helper when available
				$next = new DateTime($e->getCachedUntil(), $utc_tz);
				EveHelper::updateApiStatus($account, $e->getCode());
				switch ($account->apiStatus) {
					case 'Limited':
						JError::raiseNotice(0, JText::_('API key offers limited access'));
						break;
					case 'Invalid':
						JError::raiseWarning(0, JText::_('API key is invalid'));
						break;
					case 'Inactive':
						JError::raiseWarning(0, JText::_('Account is inactive'));
						break;
				}
			}
			catch (AleExceptionEVE $e) {
				$next = new DateTime($e->getCachedUntil(), $utc_tz);
			}
			catch (RuntimeException $e) {
				JError::raiseWarning($e->getCode(), $e->getMessage());
			}
			catch (Exception $e) {
				JError::raiseError($e->getCode(), $e->getMessage());
			}
			if (!is_null($next)) {
				$next->modify('+'.$row->delay.' minutes');
				$schedule = EveFactory::getInstance('schedule', $row->id);
				$schedule->next = $next->format('Y-m-d H:i:s');
				$schedule->store();
			}
		}
	}
}
