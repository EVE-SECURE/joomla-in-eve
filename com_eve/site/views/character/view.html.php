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

jimport('joomla.application.component.view');

class EveViewCharacter extends JView
{
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();

		$params = $this->get('Params');
		$character = $this->get('Item');
		$components = $this->get('Components');
		$apischedule = $this->get('CharacterList', 'Apischedule');
		$sectionaccess = $this->get('CharacterList', 'Sectionaccess');
		$groups = $this->get('CharacterGroups', 'Sectionaccess');
		if (is_array($sectionaccess)) {
			foreach ($sectionaccess as $item) {
				if (is_null($item->access)) {
					$item->access = 'NULL';
				}
			}
		}

		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		if (is_object($menu)
		&& JArrayHelper::getValue($menu->query, 'option') == 'com_eve'
		&& JArrayHelper::getValue($menu->query, 'view') == 'character'
		&& JArrayHelper::getValue($menu->query, 'characterID') == $character->characterID) {
			$menu_params = new JParameter($menu->params);
			if (!$menu_params->get('page_title')) {
				$params->set('page_title',	$character->name);
			}
		} else {
			$params->set('page_title',	$character->name);
		}
		$document->setTitle($params->get('page_title'));

		$this->assignRef('character', $character);
		$this->assignRef('components', $components);
		$this->assignRef('apischedule', $apischedule);
		$this->assignRef('sectionaccess', $sectionaccess);
		$this->assignRef('groups', $groups);
		$this->assignRef('params', $params);

		parent::display();
		$this->_setPathway();
	}

	protected function _setPathway()
	{
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();

		$view = JArrayHelper::getValue($menu->query, 'view');
		switch ($view) {
			case null:
				if ($this->character->allianceID) {
					$pathway->addItem($this->character->alliancenName,
					EveRoute::_('alliance', $this->character));
				}
			case 'alliance':
				$pathway->addItem($this->character->corporationName,
				EveRoute::_('corporation', $this->character, $this->character));
			case 'corporation':
			case 'user':
				$pathway->addItem($this->character->name,
				EveRoute::_('character', $this->character, $this->character, $this->character));
		}
	}
}
