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
	$app = JFactory::getApplication();
	$menu = $app->getMenu();
	$router = EveRouter::getInstance();
	$item = $router->getItem($query);
	if (!$item && isset($query['Itemid']) && isset($query['view'])) {
		unset($query['Itemid']);
	}
	$segments = $router->getSegments($query, $item);
	if (isset($query['view']) && (isset($query['Itemid']) || !empty($segments))) {
		unset($query['view']);
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
	if ($s1) {
		$vars['option'] = 'com_eve'.$s1;
	}
	
	return $vars;
}

class EveRouter {
	private static $instance;
	
	private $character = array();
	private $corporation = array();
	private $alliance = array();
	
	private function __construct()
	{
		$menu = JSite::getMenu();
		$items = $menu->getItems('component', 'com_eve');
		foreach ($items as $item) {
			$view = $item->query['view'];
			$itemID = $item->query['view'].'ID';
			if (isset($this->$view)) {
				$this->{$view}[$item->query[$itemID]] = $item;
			}
		}
	}
	
	public function getItem(&$query)
	{
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
		$entities = array('alliance', 'corporation', 'character');
		foreach ($entities as $entity) {
			$entityID = $entity.'ID';
			if (!isset($query[$entityID])) {
				return $segments;
			}
			if (!empty($item) && ($item->query['view'] == $entity)) {
				$segments = array();
				$query['Itemid'] = $item->id;
				unset($query[$entityID]);
				unset($query['view']);
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