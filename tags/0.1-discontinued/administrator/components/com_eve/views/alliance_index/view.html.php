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

class EveViewAlliance_Index extends JView {
	
	function display($tpl = null) {
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/com_eve/assets/common.css');
		
		global $mainframe;
		
		require_once JPATH_COMPONENT.DS.'html'.DS.'filter.php';
	
		$title = JText::_('EVE ALLIANCE MANAGER');
		JToolBarHelper::title($title, 'alliance');
		JToolBarHelper::custom('get_alliance_list', 'alliance', 'alliance', 'Get Alliance List', false);
		JToolBarHelper::custom('get_alliance_members', 'corp', 'corp', 'Get Alliance Members', true);
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();
		
		$context = 'com_eve.alliances.index.';
		$filter_order		= $mainframe->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'name',	'cmd' );
		$filter_order_dir	= $mainframe->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$filter_standings	= $mainframe->getUserStateFromRequest( $context.'filter_standings',	'filter_standings',	'',				'word' );

		$search 	= $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search 	= JString::strtolower( $search );
		
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
		
		$q = new JQuery();
		$q->addTable('#__eve_alliances', 'al');
		$q->addQuery('COUNT(*)');
		if ($search) {
			$q->addWhere('al.name LIKE '.$q->Quote( '%'.$q->getEscaped( $search, true ).'%', false ));
		}
		switch ($filter_standings) {
			case 'owner':
				$q->addWhere('al.owner');
				break;
			case 'friendly':
				$q->addWhere('al.standings > 0');
				break;
			case 'hostile':
				$q->addWhere('al.standings < 0');
				break;
		}
		$total = $q->loadResult();
		
		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );
		
		$html_sort_name = JHTML::_('grid.sort', JText::_( 'ALLIANCE NAME' ), 'al.name', $filter_order_dir, $filter_order);
		$html_sort_alliance_tag  = JHTML::_('grid.sort', JText::_( 'ALLIANCE TAG' ), 'al.alliance_tag', $filter_order_dir, $filter_order);
		$html_sort_standings = JHTML::_('grid.sort', JText::_( 'STANDINGS' ), 'al.standings', $filter_order_dir, $filter_order);
		$html_filter_search = JHTML::_('filter.search', $search);
		$html_filter_standings = JHTML::_('filter.select', 'filter_standings', $filter_standings);
		
		$q = new JQuery();
		$q->addTable('#__eve_alliances', 'al');
		$q->addQuery('al.*');
		$q->addOrder($filter_order, $filter_order_dir);
		$q->setLimit($limit, $limitstart);
		if ($search) {
			$q->addWhere('al.name LIKE '.$q->Quote( '%'.$q->getEscaped( $search, true ).'%', false ));
		}
		switch ($filter_standings) {
			case 'owner':
				$q->addWhere('al.owner');
				break;
			case 'friendly':
				$q->addWhere('al.standings > 0');
				break;
			case 'hostile':
				$q->addWhere('al.standings < 0');
				break;
		}
		$this->items = $q->loadObjectList();
		
		$this->assign('html_sort_name', $html_sort_name);
		$this->assign('html_sort_alliance_tag',  $html_sort_alliance_tag);
		$this->assign('html_sort_standings', $html_sort_standings);
		$this->assign('html_filter_search', $html_filter_search);
		$this->assign('html_filter_standings', $html_filter_standings);
		
		
		$this->assign('filter_order', $filter_order);
		$this->assign('filter_order_dir', $filter_order_dir);
		$this->assign('search', $search);
		$this->assignRef('items', $this->items);
		$this->assignRef('pagination', $pagination);
		parent::display($tpl);
	}

	function loadItem($index=0)
	{
		$item =& $this->items[$index];
		$item->index	= $index;

		$item->url		= JRoute::_('index.php?option=com_eve&control=alliance&task=edit&cid[]='.$item->id);

		$this->assignRef('item', $item);
	}
}

?>