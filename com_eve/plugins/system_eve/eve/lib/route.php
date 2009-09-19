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

class EveRoute
{
	static public function _($url, $alliance = null, $corporation = null, $character = null, $xhtml = true)
	{
		$app = JFactory::getApplication();
		$names = array('character' => 'name', 'corporation' => 'corporationName','alliance' => 'name');
		if ($app->getCfg('sef')) {
			$entities = array('alliance', 'corporation', 'character');
		} else {
			$entities = array('alliance', 'corporation', 'character');
		}
		
		foreach ($entities as $entity) {
			if ($$entity) {
				if (is_array($$entity)) {
					$array = $$entity;
					$obj = $array[0];
					$id = $array[1].'ID';
					$name = $array[1].'Name';
				} else {
					$obj = $$entity; 
					$id = $entity.'ID';
					$name = $entity.'Name';
					$name = isset($obj->$name) ? $name : 'name';
				}
				$url .= '&'.$entity.'ID='.$obj->$id.':'.$obj->$name; 
			}
		}
		echo JRoute::_($url, $xhtml);
	}
}