<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Wallet Journal
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

require_once JPATH_COMPONENT_SITE.DS.'view.php';

class EvewalletjournalViewCorporation extends EvewalletjournalView 
{
	public $corporation;

	protected function _setEntity($corporation, $params) 
	{
		$document = JFactory::getDocument();
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		if (is_object($menu)
				&& JArrayHelper::getValue($menu->query, 'option') == 'com_evewalletjournal'
				&& JArrayHelper::getValue($menu->query, 'view') == 'corporation'  
				&& JArrayHelper::getValue($menu->query, 'corporationID') == $corporation->corporationID) {
			$menu_params = new JParameter($menu->params);
			if (!$menu_params->get('page_title')) {
				$params->set('page_title',	$corporation->corporationName.' - '.JText::_('Wallet Journal'));
			}
		} else {
			$params->set('page_title',	$corporation->corporationName.' - '.JText::_('Wallet Journal'));
		}
		$document->setTitle($params->get('page_title'));
		
		$this->assignRef('corporation', $corporation);
		
	}
	
	protected function _setPathway()
	{
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		if (!$menu || $menu->component == 'com_evewalletjournal') {
			return;
		}
		
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		
		$view = JArrayHelper::getValue($menu->query, 'view');
		switch ($view) {
			case null:
				$pathway->addItem($this->corporation->allianceName, 
					EveRoute::_('alliance', $this->corporation));
			case 'alliance':
				$pathway->addItem($this->corporation->corporationName, 
					EveRoute::_('corporation', $this->corporation, $this->corporation));
			case 'corporation':
				$pathway->addItem(JText::_('Wallet Journal'), 
					EveRoute::_('corpwalletjournal', $this->corporation, $this->corporation));
		}
	}
}
