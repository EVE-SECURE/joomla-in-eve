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
	if (!isset($query['section']) && !isset($query['task'])) {
		return array();
	}
	$app = JFactory::getApplication();
	$menu = $app->getMenu();
	$router = EveFactory::getRouter();
	$item = $router->getItem($query);

	$segments = $router->getSegments($query, $item);

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
		$query = sprintf('SELECT * FROM #__eve_sections WHERE alias=%s AND entity=%s ', $dbo->Quote($s1), $dbo->Quote($view));
		$dbo->setQuery($query);
		$section = $dbo->loadObject();
		if (!$section) {
			JError::raiseError(404, JText::_("Resource Not Found"));
			return false;
		}
		$vars['option'] = 'com_eve'.$section->component;
		if ($section->view) {
			$vars['view'] = $section->view;
		}
		if ($section->layout) {
			$vars['layout'] = $section->layout;
		}
		$vars['section'] = $section->name;
	} else {
		$vars['section'] = $vars['view'];
	}

	return $vars;
}
