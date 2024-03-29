<?php

/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Research
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

function EveresearchBuildRoute(&$query)
{
	if (!isset($query['section'])) {
		return array();
	}

	$app = JFactory::getApplication();
	$menu = $app->getMenu();
	$router = EveFactory::getRouter();
	$item = $router->getItem($query);

	$segments = $router->getSegments($query, $item);

	if (isset($query['view'])) {
		unset($query['view']);
	}
	return $segments;
}

function EveresearchParseRoute($segments)
{
	return array();
}
