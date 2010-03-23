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

jimport( 'joomla.application.component.view');

abstract class EvewalletjournalView extends JView 
{
	public $params;
	public $state;
	public $items;
	public $pagination;

	function display($tpl = null) {
		$app = JFactory::getApplication();
		
		$state		= $this->get('State');
		$params		= $this->get('Params');
		$item		= $this->get('Item');
		$items		= $this->get('Items', 'list');
		$pagination	= $this->get('Pagination', 'list');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		if (count($errors = $this->get('Errors', 'list'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->_setEntity($item, $params);
		
		$this->assignRef('params', 		$params);
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);
		
		parent::display();
		$this->_setPathway();
	}
	
	protected function _setEntity($item, $params) 
	{
		
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
				$pathway->addItem($this->character->allianceName, 
					EveRoute::_('alliance', $this->character));
			case 'alliance':
				$pathway->addItem($this->character->corporationName, 
					EveRoute::_('corporation', $this->character, $this->character));
			case 'corporation':
			case 'user':
				$pathway->addItem($this->character->name, 
					EveRoute::_('character', $this->character, $this->character, $this->character));
			case 'character':
				$pathway->addItem(JText::_('Wallet Journal'), 
					EveRoute::_('charwalletjournal', $this->character, $this->character, $this->character));
		}
	}
	
	public function getArgument($item)
	{
		switch ($item->refTypeID) {
			case 1:
				//station
				return JHTML::_('evelink.station', array($item, 'arg', '1'));
				break;
			case 2:
				//transactionID
				break;
			case 19:
				//ship typeID
				return JHTML::_('evelink.ship', array($item, 'arg', '1'));
				break;
			case 35:
				//CSPA characterID
				break;
			case 85:
				//Bonties solarSystemID
				return JHTML::_('evelink.solarSystem', array($item, 'arg', '1'));
				break;
			default:
				return $item->argName1;
		}
	}
	
	public function getReason($item)
	{
		switch ($item->refTypeID) {
			case 85:
				//For each type of NPC killed, its typeID is followed by a colon and the quantity killed. 
				//These pairs are seperated by commas, and if there are too many (more than about 60 characters' worth) 
				//the list is ended with a literal ",..." to indicate that more have been left off the list. 
				return $item->reason;
				break;
			default:
				return $item->reason;
		}
	}
}
