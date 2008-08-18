<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Core
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

class EveViewChar_Index extends JView {
	
	function display($tpl = null) {
		global $mainframe;
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/com_eve/assets/common.css');
		
		$title = JText::_('EVE CHARACTER MANAGER');
		JToolBarHelper::title($title, 'char');
		JToolBarHelper::custom('get_corporation_sheet', 'corp', 'corp', 'Corporation Sheet', true);
		JToolBarHelper::custom('get_character_sheet', 'char', 'char', 'Character Sheet', true);
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();
		
		$context = 'com_eve.chars.index.';
		$filter_order		= $mainframe->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'c.name',	'cmd' );
		$filter_order_dir	= $mainframe->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		$search 	= $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search 	= JString::strtolower( $search );
		
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
		
		$q = new JQuery();
		$q->addTable('#__eve_characters', 'c');
		$q->addQuery('COUNT(*)');
		if ($search) {
			$q->addWhere('c.name LIKE '.$q->Quote( '%'.$q->getEscaped( $search, true ).'%', false ));
		}
		$total = $q->loadResult();
		
		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );
		
		$html_sort_name 	= JHTML::_('grid.sort', JText::_( 'CHARACTER NAME' ), 'c.name', $filter_order_dir, $filter_order);
		$html_sort_userName 		= JHTML::_('grid.sort', JText::_( 'USER NAME' ), 'userName', $filter_order_dir, $filter_order);
		$html_sort_corporationName	= JHTML::_('grid.sort', JText::_( 'CORPORATION' ), 'co.corporationName', $filter_order_dir, $filter_order);
		$html_sort_allianceName 	= JHTML::_('grid.sort', JText::_( 'ALLIANCE' ), 'allianceName', $filter_order_dir, $filter_order);
				
		$q = new JQuery();
		$q->addTable('#__eve_characters', 'c');
		$q->addJoin('#__eve_corporations', 'co', 'c.corporationID=co.corporationID');
		$q->addJoin('#__eve_alliances', 'al', 'al.allianceID=co.allianceID');
		$q->addJoin('#__users', 'u', 'c.userID=u.id');
		$q->addOrder($filter_order, $filter_order_dir);
		$q->addQuery('c.*');
		$q->addQuery('co.corporationName, co.ticker');
		$q->addQuery('al.name AS allianceName, al.shortName');
		$q->addQuery('u.name AS userName');
		$q->setLimit($limit, $limitstart);
		if ($search) {
			$q->addWhere('c.name LIKE '.$q->Quote( '%'.$q->getEscaped( $search, true ).'%', false ));
		}
		$this->items = $q->loadObjectList();
		
		$this->assign('html_sort_name', $html_sort_name);
		$this->assign('html_sort_userName', $html_sort_userName);
		$this->assign('html_sort_corporationName', $html_sort_corporationName);
		$this->assign('html_sort_allianceName', $html_sort_allianceName);
		
		
		$this->assign('search', $search);
		$this->assign('filter_order', $filter_order);
		$this->assign('filter_order_dir', $filter_order_dir);
		$this->assignRef('items', $this->items);
		$this->assignRef('pagination', $pagination);
		parent::display($tpl);
	}

	function loadItem($index=0)
	{
		$item =& $this->items[$index];
		$item->index	= $index;

		$item->url		= JRoute::_('index.php?option=com_eve&control=char&view=char&cid[]='.$item->id);
		
		$this->assignRef('item', $item);
	}
}

?>