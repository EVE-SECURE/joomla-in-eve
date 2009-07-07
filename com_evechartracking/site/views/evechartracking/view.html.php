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

class EvechartrackingViewEvechartracking extends JView {
	function display() {
		global $mainframe;
		
		$breadcrumbs = $mainframe->getPathWay();
		$params = &$mainframe->getParams();
		
		$user = JFactory::getUser();
		
		$layout = $this->getLayout();
		
		$model = $this->getModel();
		
		$context = 'com_evechartracking.columns.';
		
		$ID = 0;
		
		$defaultColums = $model->getColumns(true);
		if ($layout == 'corp') {
			$corporationID = $mainframe->getUserStateFromRequest( $context.'corporationID', 'corporationID', 0, 'int' );
			
			if (JRequest::getInt('reset') && is_null(JRequest::getVar('selectedColumns'))) {
				$mainframe->setUserState($context.'selectedColumns', array());
			}
			$selectedColumns = $mainframe->getUserStateFromRequest( $context.'selectedColumns', 'selectedColumns', $defaultColums, 'array' );
			
			$corps = $model->getCorps();
			if (!isset($corps[$corporationID])) {
				$corporationID = 0;
			} else {
				$breadcrumbs->addItem($corps[$corporationID]->corporationName, JRoute::_('index.php?view=evechartracking&layout=corp&corporationID='.$corporationID));
			}
			
			if (empty($corps)) {
				parent::display('none');
				return;
			}
			
			$select = array('corporationID' => '0', 'corporationName'=>JText::_('SELECT CORPORATION'));
			$select = array('0' => JArrayHelper::toObject($select));
			$corps = array_merge($select, $corps);
			
			$html_corps = JHTML::_('select.genericlist', $corps, 'corporationID', array('onchange'=>'this.form.submit()'), 'corporationID', 'corporationName', $corporationID);
			
			$this->assignRef('html_corps', $html_corps);
			
			$ID = $corporationID;
			
		} else {
				
			$owner = $mainframe->getUserStateFromRequest( $context.'owner', 'owner', 0, 'int' );
			if (JRequest::getInt('reset') && is_null(JRequest::getVar('selectedColumns'))) {
				$mainframe->setUserState($context.'selectedColumns', array());
			}
			$selectedColumns = $mainframe->getUserStateFromRequest( $context.'selectedColumns', 'selectedColumns', $defaultColums, 'array' );
			
			$users = $model->getUsers();
			if (!isset($users[$owner])) {
				$owner = 0;
			} else {
				$breadcrumbs->addItem($users[$owner]->name, JRoute::_('index.php?view=evechartracking&layout=user&owner='.$owner));
			}
			
			if (empty($users)) {
				parent::display('none');
				return;
			}
			
			$select = array('id' => '0', 'name'=>JText::_('SELECT USER'));
			$select = array('0' => JArrayHelper::toObject($select));
			$users = array_merge($select, $users);
			
	
			$html_users = JHTML::_('select.genericlist', $users, 'owner', array('onchange'=>'this.form.submit()'), 'id', 'name', $owner);
			
			$this->assignRef('html_users', $html_users);
			
			$ID = $owner;
		}
			
		parent::display('select');
		if ($ID == 0) {
			return;
		}
		
		$params = &JComponentHelper::getParams( 'com_evechartracking' );
		if (EveHelperIgb::isIgb()) {
			$this->namePattern = $params->get('name_link_igb', '%s');
		} else {
			$this->namePattern = $params->get('name_link', '%s');
		}
		
		$columns = $model->getColumns();
		$total = $model->getMemberCount($ID, $layout);
		
		$limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
		$filter_order		= $mainframe->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'c.name',	'cmd' );
		$filter_order_dir	= $mainframe->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		
		$limit				= JRequest::getVar('limit',				$mainframe->getCfg('list_limit'),	'', 'int');
		$limitstart			= JRequest::getVar('limitstart',		0,				'', 'int');
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$filter_order		= JRequest::getVar('filter_order',		'cd.ordering',	'', 'cmd');
		$filter_order_Dir	= JRequest::getVar('filter_order_Dir',	'ASC',			'', 'word');
		
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
		$this->assignRef('pagination',	$pagination);
		
		$members = $model->getMembers($ID, $layout, $limitstart, $limit);
		
		$this->assignRef('columns', $columns);
		$this->assignRef('selectedColumns', $selectedColumns);
		$this->assignRef('members', $members);
		parent::display();
	}
	
	function getMemberColumn(&$member, $column) {
		switch($column) {
			case 'name':
				return sprintf($this->namePattern, $member->name, $member->characterID);
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
			case 'corporationID':
				if ($member->corporationID == 0) {
					return '';
				}
				return sprintf('<a href="%s">%s</a>', JRoute::_('index.php?option=com_evechartracking&view=evechartracking&layout=corp&corporationID='.$member->corporationID), $member->corporationName);				
			case 'owner':
				if ($member->owner == 0) {
					return '';
				}
				return sprintf('<a href="%s">%s</a>', JRoute::_('index.php?option=com_evechartracking&view=evechartracking&layout=user&owner='.$member->owner), $member->userName);				
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

?>