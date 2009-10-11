<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Character Sheet
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

jimport('joomla.application.component.view');

class EvecharsheetViewCharacter extends JView {
	function display($tmpl = null) {
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		
		$params 	= $this->get('Params');
		$character 	= $this->get('Character');
		$groups 	= $this->get('SkillGroups');
		$queue 		= $this->get('Queue');
		$categories = $this->get('CertificateCategories');
		$attributes = $this->get('Attributes');
		$roles 		= $this->get('Roles');
		$roleLocations 	= $this->get('RoleLocations');
		$titles 	= $this->get('Titles');
		
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		if (is_object($menu)
				&& JArrayHelper::getValue($menu->query, 'option') == 'com_evecharsheet'
				&& JArrayHelper::getValue($menu->query, 'view') == 'character'  
				&& JArrayHelper::getValue($menu->query, 'characterID') == $character->characterID) {
			$menu_params = new JParameter($menu->params);
			if (!$menu_params->get('page_title')) {
				$params->set('page_title',	$character->name.' - '.JText::_('Character Sheet'));
			}
		} else {
			$params->set('page_title',	$character->name.' - '.JText::_('Character Sheet'));
		}
		$document->setTitle($params->get('page_title'));
		
		$this->assignRef('params', $params);
		$this->assignRef('character', $character);
		$this->assignRef('groups', $groups);
		$this->assignRef('queue', $queue);
		$this->assignRef('categories', $categories);
		$this->assignRef('attributes', $attributes);
		$this->assignRef('roles', $roles);
		$this->assignRef('roleLocations', $roleLocations);
		$this->assignRef('titles', $titles);
		
		parent::display($tmpl);
		$this->_setPathway();
	}
	
	public function show($section)
	{
		return true;
		return intval($this->params->get('show'.$section, 0));
	}

	protected function _setPathway()
	{
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		if ($menu->component == 'com_evecharsheet') {
			return;
		}
		
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		
		$view = JArrayHelper::getValue($menu->query, 'view');
		switch ($view) {
			case null:
				$pathway->addItem($this->character->allianceName, 
					EveRoute::_('alliance', $this->character));
			case 'alliance':
				$pathway->addItem($this->character->corporationName, 
					EveRoute::_('corporation', $this->character, $this->character));
			case 'corporation':
				$pathway->addItem($this->character->name, 
					EveRoute::_('character', $this->character, $this->character, $this->character));
			case 'character':
				$pathway->addItem(JText::_('Character Sheet'), 
					EveRoute::_('charsheet', $this->character, $this->character, $this->character));
		}
	}
}
