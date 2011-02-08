<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Character Tracking
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

class EvechartrackingViewCorporation extends JView {

	public function display($tpl = null)
	{
		JHTML::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'helpers'.DS.'html');
		
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		
		$params = $this->get('Params');
		$corporation = $this->get('Item');
		$members = $this->get('Members');
		$columns = $this->get('Columns');
		$selectedColumns = $this->get('SelectedColumns');

		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		if (is_object($menu)
				&& JArrayHelper::getValue($menu->query, 'option') == 'com_eve'
				&& JArrayHelper::getValue($menu->query, 'view') == 'corporation'  
				&& JArrayHelper::getValue($menu->query, 'corporationID') == $corporation->corporationID) {
			$menu_params = new JParameter($menu->params);
			if (!$menu_params->get('page_title')) {
				$params->set('page_title', $corporation->corporationName.' - '.JText::_('Com_Evechartracking_Member_Tracking'));
			}
		} else {
			$params->set('page_title', $corporation->corporationName.' - '.JText::_('Com_Evechartracking_Member_Tracking'));
		}
		$document->setTitle($params->get('page_title'));
		
		$this->assignRef('corporation', $corporation);
		$this->assignRef('columns', $columns);
		$this->assignRef('selectedColumns', $selectedColumns);
		$this->assignRef('params', $params);
		$this->assignRef('members', $members);
		
		parent::display();
		$this->_setPathway();
	}
	
	protected function _setPathway()
	{
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		if ($menu) {
			if ($menu->component == 'com_evechartracking') {
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
				if ($this->corporation->allianceID) {
					$pathway->addItem($this->corporation->allianceName, 
						EveRoute::_('alliance', $this->corporation));
				}
			case 'alliance':
				$pathway->addItem($this->corporation->corporationName, 
					EveRoute::_('corporation', $this->corporation, $this->corporation));
			case 'corporation':
				$pathway->addItem(JText::_('Com_Evechartracking_Member_Tracking'), 
					EveRoute::_('chartracking', $this->corporation, $this->corporation));
		}
	}
	
	function getMemberColumn(&$member, $column) {
		switch($column) {
			case 'name':
				return JHTML::_('evelink.character', $member, $this->corporation); 
				break;
			case 'balance':
				return number_format($member->$column, 2); 
				break;
			case 'baseName':
				if ($member->baseID == 0) {
					return '';
				}
				if (EveHelperIgb::isIgb()) {
					return sprintf('<a href="showinfo:%s//%s">%s</a>', $member->baseTypeID, $member->baseID, $member->baseName);
				} else {
					return $member->baseName;
				}
			case 'locationName':
				if ($member->locationID == 0) {
					return '';
				}
				if (EveHelperIgb::isIgb()) {
					return sprintf('<a href="showinfo:%s//%s">%s</a>', $member->locationTypeID, $member->locationID, $member->locationName);
				} else {
					return $member->locationName;
				}
			case 'shipTypeName':
				if ($member->shipTypeID == 0) {
					return '';
				}
				if (EveHelperIgb::isIgb()) {
					return sprintf('<a href="showinfo:%s">%s</a>', $member->shipTypeID, $member->shipTypeName);

				} else {
					return $member->shipTypeName;
				}
			case 'owner':
				if ($member->owner == 0) {
					return '';
				}
				return $member->ownerName;
			case 'startDateTime':
			case 'startDateTime':
			case 'logonDateTime':
			case 'logoffDateTime':
				return JHTML::_('date', $member->$column, JText::_('DATE_FORMAT_LC2'));
				break;
			default:
				if (isset($member->$column)) {
					return $member->$column;
				}
				return '';
			}
	}
}
