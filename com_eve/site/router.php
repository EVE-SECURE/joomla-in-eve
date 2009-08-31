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
	$segments = array();
	$uri = JURI::getInstance();
	if (JArrayHelper::getValue($query, 'view') == 'corporation') {
		$segments = JRequest::getVar('segments');
		$segments[] = 'c';
		//TODO: nice url without ID
		//$segments[] = end(explode(':', $query['characterID'], 2));
		$segments[] = $query['corporationID'];
		unset($query['view']);
		unset($query['corporationID']);
	}
	if (JArrayHelper::getValue($query, 'view') == 'character') {
		$segments = JRequest::getVar('segments');
		$segments[] = 'c';
		//TODO: nice url without ID
		//$segments[] = end(explode(':', $query['characterID'], 2));
		$segments[] = $query['characterID'];
		unset($query['view']);
		unset($query['characterID']);
	}
	return $segments;
}

function EveParseRoute($segments)
{
	$vars = array();
	$app = JFactory::getApplication();
	//$app->
	$router = EveRouter::getInstance();
	$vars['segments'] = $segments;
	$app = JFactory::getApplication();
	$menu = $app->getMenu();
	$item = $menu->getActive();
	$view = JArrayHelper::getValue($item->query, 'view');
	if ($view == 'alliance') {
		$s1 = JArrayHelper::getValue($segments, 0);
		if (is_null($s1)) {
			return $vars;;
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
		} else {
			//TODO: route another component
		}
	}
	return $vars;
}

class EveRouter {
	static $views = array('alliance' => 0, 'corporation' => 2, 'character' => 4);
	
	function getInstance()
	{
		static $instance;
		if (!isset($instance)) {
			$instance = new EveRouter();
		}
		return $instance;
	}
	
	function setSegments($segments)
	{
		$this->segments = $segments;
	}
	
	function setMenuView($view)
	{
		$this->view = $view;
	}
	
	function getSegments($view)
	{
		
	}
}