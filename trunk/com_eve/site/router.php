<?php

/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Community Builder - Character Sheet
 * @copyright	Copyright (C) 2009 Pavol Kovalik. All rights reserved.
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

function EveBuildRoute(&$query)
{
	if (!isset($query['entity']) && !isset($query['task'])) {
		return array();
	}
	$app = JFactory::getApplication();
	$menu = $app->getMenu();
	$router = EveRouter::getInstance();
	$item = $router->getItem($query);
	
	if (!$item && isset($query['Itemid']) && isset($query['entity'])) {
		unset($query['Itemid']);
	}
	
	$segments = $router->getSegments($query, $item);
	
	if (isset($query['entity'])) {
		unset($query['entity']);
	}
	
	if (isset($query['component'])) {
		$segments[] = $query['component'];
		unset($query['component']);
	}
	return $segments;
}

function EveParseRoute($segments)
{
	$app = JFactory::getApplication();
	$menu = $app->getMenu();
	$item = $menu->getActive();
	if (empty($item)) {
		$s1 = JArrayHelper::getValue($segments, 0);
		if (is_null($s1)) {
			return array();
		} elseif ($s1 == 'a') {
			$vars['view'] = $view = 'alliance';
			$vars['allianceID'] = JArrayHelper::getValue($segments, 1, null, 'int');
			$segments = array_slice($segments, 2);
		} elseif ($s1 == 'c') {
			$vars['view'] = $view = 'corporation';
			$vars['corporationID'] = JArrayHelper::getValue($segments, 1, null, 'int');
			$segments = array_slice($segments, 2);
		} else {
			
		}
	} else {
		$vars = $item->query;
		$view = JArrayHelper::getValue($item->query, 'view');
	}
	
	if ($view == 'user') {
		$s1 = JArrayHelper::getValue($segments, 0);
		if (is_null($s1)) {
			return $vars;;
		} elseif ($s1 == 'c') {
			$vars['view'] = $view = 'character';
			$vars['characterID'] = JArrayHelper::getValue($segments, 1, null, 'int');
			$segments = array_slice($segments, 2);
		} else {
			//TODO: route another component
		}
	}
	
	if ($view == 'alliance') {
		$s1 = JArrayHelper::getValue($segments, 0);
		if (is_null($s1)) {
			return $vars;
		} elseif ($s1 == 'c') {
			$vars['view'] = $view = 'corporation';
			$vars['corporationID'] = JArrayHelper::getValue($segments, 1, null, 'int');
			$segments = array_slice($segments, 2);
			
		} else {
			//TODO: route another component
		}
	}
	
	if ($view == 'corporation') {
		$s1 = JArrayHelper::getValue($segments, 0);
		if (is_null($s1)) {
			return $vars;;
		} elseif ($s1 == 'c') {
			$vars['view'] = $view = 'character';
			$vars['characterID'] = JArrayHelper::getValue($segments, 1, null, 'int');
			$segments = array_slice($segments, 2);
		} else {
			//TODO: route another component
		}
	}
	
	$s1 = JArrayHelper::getValue($segments, 0);
	$s1 = str_replace(':', '-', $s1);
	if ($s1) {
		$dbo = JFactory::getDBO();
		$query = 'SELECT * FROM #__eve_components WHERE alias='.$dbo->Quote($s1);
		$dbo->setQuery($query);
		$component = $dbo->loadObject();
		if (!$component) {
			JError::raiseError(404, JText::_("Resource Not Found"));
			return false;
		}
		$vars['option'] = 'com_eve'.$component->component;
		if ($component->view) {
			$vars['view'] = $component->view;
		}
		if ($component->layout) {
			$vars['layout'] = $component->layout;
		}
	}
	
	return $vars;
}

class EveRouter {
	private static $instance;
	
	private $user = array();
	private $character = array();
	private $corporation = array();
	private $alliance = array();
	
	private $ownedChars = array();
	
	private function __construct()
	{
		$menu = JSite::getMenu();
		$items = $menu->getItems('component', 'com_eve');
		foreach ($items as $item) {
			$view = $item->query['view'];
			if ($view == 'user') {
				$this->{$view}[$item->id] = $item;
			} else {
				$itemID = $item->query['view'].'ID';
				if (isset($this->$view)) {
					$this->{$view}[intval($item->query[$itemID])] = $item;
				}
			}
		}
		$dbo = JFactory::getDBO();
		$q = EveFactory::getQuery($dbo);
		
		$user = JFactory::getUser();
		$id = intval($user->id);
		$q->addTable('#__eve_characters', 'c');
		$q->addJoin('#__eve_accounts', 'a', 'c.userID=a.userID');
		$q->addWhere('a.owner=%s', $id);
		$q->addQuery('characterID');
		$tmp = $q->loadResultArray();
		foreach ($tmp as $characterID) {
			$this->ownedChars[$characterID] = $characterID;
		}
	}
	
	public function getItem(&$query)
	{
		if (isset($query['characterID']) && isset($this->ownedChars[intval($query['characterID'])])) {
			if (!empty($this->user)) {
				return reset($this->user);
			}
		}
		
		$entities = array('character', 'corporation', 'alliance');
		
		foreach ($entities as $entity) {
			$entityID = $entity.'ID';
			if (isset($query[$entityID])) {
				$id = intval($query[$entityID]);
				if (isset($this->{$entity}[$id])) {
					return $this->{$entity}[$id];
				}
			}
		}
		return null;
	}
	
	public function getSegments(&$query, $item = null)
	{
		$segments = array();
		if (JArrayHelper::getValue($item->query, 'view') == 'user') {
			//this is user view
			unset($query['corporationID']);
			unset($query['allianceID']);
			$query['Itemid'] = $item->id;
			$entities = array('character');
		} elseif (JArrayHelper::getValue($item->query, 'view') == 'corporation' && !isset($query['allianceID'])) {
			//corporation does not have to be in alliance
			$entities = array('corporation', 'character'); 
		} else {
			$entities = array('alliance', 'corporation', 'character');
		}
		foreach ($entities as $entity) {
			$entityID = $entity.'ID';
			if (!isset($query[$entityID])) {
				return $segments;
			}
			if (!empty($item) && ($item->query['view'] == $entity)) {
				$segments = array();
				$query['Itemid'] = $item->id;
				unset($query[$entityID]);
				unset($query['entity']);
				continue;
			}
			$segments[] = $entity[0];
			$segments[] = $query[$entityID];
			unset($query[$entityID]);
		}
		return $segments;
	}
	
	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new EveRouter();
		}
		return self::$instance;
	}
	
}