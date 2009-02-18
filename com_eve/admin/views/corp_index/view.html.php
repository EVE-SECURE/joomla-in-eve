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

class EveViewCorp_Index extends JView {
	
	function display($tpl = null) {
		global $mainframe;
		
		require_once JPATH_COMPONENT.DS.'html'.DS.'filter.php';
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/com_eve/assets/common.css');
		
		$title = JText::_('EVE CORPORATION MANAGER');
		JToolBarHelper::title($title, 'corp');
		JToolBarHelper::custom('getCorporationSheet', 'corp', 'corp', 'Corporation Sheet', true);
		JToolBarHelper::custom('getMemberTracking', 'char', 'char', 'Member Tracking', true);
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();
		
		$context = 'com_eve.corps.index.';
		$filter_order		= $mainframe->getUserStateFromRequest( $context.'filter_order',		'filter_order',     'corporationName',	'cmd' );
		$filter_order_dir	= $mainframe->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',					'word' );
		$filter_standings	= $mainframe->getUserStateFromRequest( $context.'filter_standings',	'filter_standings',	'',					'word' );
		
		$search 	= $mainframe->getUserStateFromRequest( $context.'filter_search', 'filter_search', '', 'string' );
		$search 	= JString::strtolower( $search );
		
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
		
		$q = new JQuery();
		$q->addTable('#__eve_corporations', 'co');
		$q->addQuery('COUNT(*)');
		if ($search) {
			$q->addWhere('co.corporationName LIKE '.$q->Quote( '%'.$q->getEscaped( $search, true ).'%', false ));
		}
		$total = $q->loadResult();
		
		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );
		
		$q = new JQuery();
		$q->addTable('#__eve_corporations', 'co');
		$q->addJoin('#__eve_alliances', 'al', 'al.allianceID=co.allianceID');
		$q->addQuery('co.*');
		$q->addQuery('al.name');
		$q->addQuery('al.owner AS derived_owner');
		$q->addQuery('COALESCE(co.standings, al.standings, 0) AS derived_standings');
		$q->addOrder($filter_order, $filter_order_dir);
		$q->setLimit($limit, $limitstart);
		if ($search) {
			$q->addWhere('co.corporationName LIKE '.$q->Quote( '%'.$q->getEscaped( $search, true ).'%', false ));
		}
		switch ($filter_standings) {
			case 'owner':
				$q->addWhere('(co.owner OR al.owner)');
				break;
			case 'friendly':
				$q->addWhere('COALESCE(co.standings, al.standings, 0) > 0');
				break;
			case 'hostile':
				$q->addWhere('COALESCE(co.standings, al.standings, 0) < 0');
				break;
		}
		$this->items = $q->loadObjectList();
		
		$this->assign('filter_search', $search);
		$this->assign('filter_standings', $filter_standings);
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
		
		$item->url		= JRoute::_('index.php?option=com_eve&control=corp&task=edit&cid[]='.$item->corporationID);

		$this->assignRef('item', $item);
	}
}

?>