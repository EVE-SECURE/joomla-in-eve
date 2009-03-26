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

class EvecharsheetViewSheet extends JView {
	function display($tmpl = null) {
		global $mainframe;
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet(JURI::base(). 'components/com_evecharsheet/assets/sheet.css');
		//$document->addScript('components/com_consulting/assets/product.js');
		
		$params = &$mainframe->getParams();
		$this->assignRef('params', $params);
		
		$model = $this->getModel();
		
		$characterID = JRequest::getInt('characterID');
		$paramCharacterID = $params->get('characterID');
		if (!$characterID) {
			$characterID = $paramCharacterID;
		}
		$model->set('characterID', $characterID);
		$character = $model->getInstance('character', $characterID);
		//$this->assignRef('character', $character);
		$title = $characterID == $paramCharacterID ? $this->params->get('page_title') : $character->name;
						
		if (!$character) {
			$this->display('none');
			return;
		}
		$corporation = $model->getCorporation($character->corporationID);
		
		$groups = $model->getGroups($characterID);
		$show_owner = $params->get('show_owner');
		$show_all = $params->get('show_all');
		$this->assign('show_owner', $show_owner);
		if ($show_owner) {
			$owner = $model->getOwner($characterID);
			$owners_chars = $model->getOwnersCharacters($characterID, $show_all);
			$this->assignRef('owner', $owner);
			$this->assignRef('owners_chars', $owners_chars);
		}
		$this->assign('title', $title);
		$this->assignRef('character', $character);
		$this->assignRef('corporation', $corporation);
		$this->assignRef('groups', $groups);
		
		parent::display($tmpl);
	}
	
	function displaySkill() {
		
	}
}