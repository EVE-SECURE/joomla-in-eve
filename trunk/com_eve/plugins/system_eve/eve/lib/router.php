<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Core
 * @copyright	Copyright (C) 2010 Pavol Kovalik. All rights reserved.
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

class EveRouter {
	private static $instance;
	private static $_sections;
	
	private $user = array();
	private $character = array();
	private $corporation = array();
	private $alliance = array();
	
	private $ownedChars = array();
	
	public function __construct()
	{
		$menu = JSite::getMenu();
		$items = $menu->getItems('component', 'com_eve');
		if (is_array($items)) {
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
		}
		$acl = EveFactory::getACL();
		$this->ownedChars = $acl->getOwnedCharacterIDs();
	}
	
	public function getSection($name)
	{
		if (!isset(self::$_sections)) {
			$q = EveFactory::getQuery();
			$q->addTable('#__eve_sections');
			self::$_sections = $q->loadObjectList('name');
		}
		return JArrayHelper::getValue(self::$_sections, $name, null); 
	}
	
	
	public function getItem(&$query)
	{
		//for now, we can only find menu items of core component
		$query['option'] = 'com_eve';
		if (isset($query['characterID']) && isset($this->ownedChars[intval($query['characterID'])])) {
			if (!empty($this->user)) {
				$query['Itemid'] = reset($this->user)->id;
				return reset($this->user);
			}
		}
		
		$entities = array('character', 'corporation', 'alliance');
		
		foreach ($entities as $entity) {
			$entityID = $entity.'ID';
			if (isset($query[$entityID])) {
				$id = intval($query[$entityID]);
				if (isset($this->{$entity}[$id])) {
					$query['Itemid'] = $this->{$entity}[$id]->id;
					return $this->{$entity}[$id];
				}
			}
		}
		if (isset($query['Itemid'])) {
			unset($query['Itemid']);
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
			$entities = array('character');
		} elseif (!isset($query['allianceID'])) {
			//corporation does not have to be in alliance
			$entities = array('corporation', 'character'); 
		} else {
			$entities = array('alliance', 'corporation', 'character');
		}
		foreach ($entities as $entity) {
			$entityID = $entity.'ID';
			if (!isset($query[$entityID])) {
				break;
			}
			if (!empty($item) && ($item->query['view'] == $entity)) {
				$segments = array();
				unset($query[$entityID]);
				continue;
			}
			$segments[] = $entity[0];
			$segments[] = $query[$entityID];
			unset($query[$entityID]);
		}
		
		//handle section...
		$sectionName = JArrayHelper::getValue($query, 'section');
		$section = $this->getSection($sectionName);
		if ($section && $section->alias) {
			$segments[] = $section->alias;
		}
		if (isset($query['section'])) {
			unset($query['section']);
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
