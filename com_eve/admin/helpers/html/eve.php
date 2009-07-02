<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Core
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


abstract class JHTMLEve {
	
	static function alliancelist($name, $attribs = null, $selected = null, $idtag = false) {
		$query = 'SELECT allianceID, name FROM #__eve_alliances ORDER BY name;';
		$dbo = JFactory::getDBO();
		$dbo->setQuery($query);
		$alliances = $dbo->loadObjectList();
		
		$noalliance = array('allianceID' => '0', 'name'=>JText::_('NOT MEMBER OF ALLIANCE'));
		$noalliance = array('0' => JArrayHelper::toObject($noalliance));
		$alliances = array_merge($noalliance, $alliances);
		
		return JHTML::_('select.genericlist', $alliances, $name, $attribs, 'allianceID', 'name', $selected, $idtag);
	}
	
	static function accountlist($name, $attribs = null, $selected = null, $idtag = false) {
		$dbo = JFactory::getDBO();
		$q = new JQuery($dbo);
		$q->addTable('#__eve_accounts', 'u');
		$q->addJoin('#__users', 'owner', 'u.owner=owner.id');
		$q->addQuery('userID');
		$q->addQuery('COALESCE(CONCAT(owner.name, \'(\', u.userID, \')\'), u.userID) as name');
		$q->addOrder('name');
		$users = $q->loadObjectList();
		//FIXME: in case owner.name is NULL
		
		$nouser = array('userID' => '0', 'name'=>JText::_('CHARACTER NOT ASSIGNED'));
		$nouser = array('0' => JArrayHelper::toObject($nouser));
		$users = array_merge($nouser, $users);
		
		return JHTML::_('select.genericlist',$users, $name, $attribs, 'userID', 'name', $selected, $idtag);
		
	}
	
	static function corporationlist($name, $attr, $active = null, $idtag = false) {
		$query = 'SELECT corporationID, corporationName FROM #__eve_corporations ORDER BY corporationName;';
		$dbo = JFactory::getDBO();
		$dbo->setQuery($query);
		$alliances = $dbo->loadObjectList();
		
		$noalliance = array('corporationID' => '0', 'corporationName'=>JText::_('NOT MEMBER OF CORPORATION'));
		$noalliance = array('0' => JArrayHelper::toObject($noalliance));
		$alliances = array_merge($noalliance, $alliances);
		
		return JHTML::_('select.genericlist', $alliances, $name, $attribs, 'corporationID', 'corporationName', $selected, $idtag);
		
	}
	
}