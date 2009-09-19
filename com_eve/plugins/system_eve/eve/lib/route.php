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
	static public function _($component, $entity, $alliance = null, $corporation = null, $character = null, $xhtml = true)
	{
		$app = JFactory::getApplication();
		$sef = $app->getCfg('sef');
		if ($sef) {
			$url = 'index.php?option=com_eve&view='.$entity;
			if ($component) {
				$url .= '&component='.$component;
			}
			$entities = array('alliance', 'corporation', 'character');
		} else {
			$url = 'index.php?option=com_eve'.$component.'&view='.$entity;
			$entities = array($entity);
		}
		foreach ($entities as $ent) {
			if ($$ent) {
				if (is_array($$ent)) {
					$array = $$ent;
					$obj = $array[0];
					$id = $array[1].'ID';
					$name = $array[1].'Name';
				} else {
					$obj = $$ent; 
					$id = $ent.'ID';
					$name = $ent.'Name';
					$name = isset($obj->$name) ? $name : 'name';
				}
				if ($sef) {
					$url .= '&'.$ent.'ID='.$obj->$id.':'.$obj->$name;
				} else {
					$url .= '&'.$ent.'ID='.$obj->$id;
				}
			}
			if ($ent == $entity) {
				break;
			}
		}
		return JRoute::_($url, $xhtml);
	}
}