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
	private $_ownedCharacter = false;
	
	public function display($tmpl = null) {
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		
		$params 		= $this->get('Params');
		$character 		= $this->get('Item');
		$clone	 		= $this->get('Clone');
		$groups 		= $this->get('SkillGroups');
		$queue 			= $this->get('Queue');
		$categories 	= $this->get('CertificateCategories');
		$attributes 	= $this->get('Attributes');
		$roles 			= $this->get('Roles');
		$roleLocations 	= $this->get('RoleLocations');
		$titles 		= $this->get('Titles');

		$this->_ownedCharacter = false;
		$acl = EveFactory::getACL();
		$ids = $acl->getOwnedCharacterIDs();
		if (isset($ids[$character->characterID])) {
			$this->_ownedCharacter = true;
		}
		
		
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
		$this->assignRef('clone', $clone);
		$this->assignRef('queue', $queue);
		$this->assignRef('categories', $categories);
		$this->assignRef('attributes', $attributes);
		$this->assignRef('roles', $roles);
		$this->assignRef('roleLocations', $roleLocations);
		$this->assignRef('titles', $titles);
		
		parent::display();
		$this->_setPathway();
	}
	
	public function show($section)
	{
		$user = JFactory::getUser();
		$show = intval($this->params->get('show_'.$section, 1));
		$access = intval($this->params->get('access_'.$section, 0)) <= $user->get('aid');
		return $show && ($access || $this->_ownedCharacter);
	}

	protected function _setPathway()
	{
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		if ($menu) {
			if ($menu->component == 'com_evecharsheet') {
				return;
			}
			$view = JArrayHelper::getValue($menu->query, 'view');
		} else {
			$view = null;
		}
		
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		switch ($view) {
			case null:
				if ($this->character->allianceID) {
					$pathway->addItem($this->character->allianceName, 
						EveRoute::_('alliance', $this->character));
				}
			case 'alliance':
				$pathway->addItem($this->character->corporationName, 
					EveRoute::_('corporation', $this->character, $this->character));
			case 'corporation':
			case 'user':
				$pathway->addItem($this->character->name, 
					EveRoute::_('character', $this->character, $this->character, $this->character));
			case 'character':
				$pathway->addItem(JText::_('Character Sheet'), 
					EveRoute::_('charsheet', $this->character, $this->character, $this->character));
		}
	}
}
