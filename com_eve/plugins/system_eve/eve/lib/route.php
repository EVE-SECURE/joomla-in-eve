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
	private static $_components;
	
	protected function _getComponent($id)
	{
		if (!isset(self::$_components)) {
			$q = EveFactory::getQuery();
			$q->addTable('#__eve_components');
			self::$_components = $q->loadObjectList('id');
		}
		return self::$_components[$id]; 
	}
	
	static public function _($id, $alliance = null, $corporation = null, $character = null, $xhtml = true)
	{
		$app = JFactory::getApplication();
		$component = self::_getComponent($id);
		$sef = $app->getCfg('sef');
		if ($sef) {
			$url = 'index.php?option=com_eve&entity='.$component->entity;
			if ($component->alias) {
				$url .= '&component='.$component->alias;
			}
			$entities = array('alliance', 'corporation', 'character');
		} else {
			$url = 'index.php?option=com_eve'.$component->component;
			if ($component->view) {
				$url .= '&view='.$component->view; 
			}
			if ($component->layout) {
				$url .= '&layout='.$component->layout; 
			}
			$entities = array($component->entity);
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
				if ($entity == 'alliance' && $obj->$id == 0) {
					//corporation does not have to be in alliance
					continue;
				}
				if ($sef) {
					$url .= '&'.$entity.'ID='.$obj->$id.':'.$obj->$name;
				} else {
					$url .= '&'.$entity.'ID='.$obj->$id;
				}
			}
			if ($entity == $component->entity) {
				break;
			}
		}
		return JRoute::_($url, $xhtml);
	}
	
	static public function link($id, $attribs = null, $alliance = null, $corporation = null, $character = null)
	{
		$component = EveFactory::getInstance('Component', $id);
		$href = self::_($component, $alliance, $corporation, $character);
		return JHTML::_('link', $url, $component->title, $attribs);
	}
}