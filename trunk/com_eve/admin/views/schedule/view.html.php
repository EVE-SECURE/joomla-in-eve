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

class EveViewSchedule extends JView {
	public $state;
	public $items;
	public $pagination;
	public $apiCalls;

	
	function display($tpl = null) {
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$apiCalls	= $this->get('ApiCalls');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->assignRef('state',			$state);
		$this->assignRef('items',			$items);
		$this->assignRef('pagination',		$pagination);
		$this->assignRef('apiCalls',		$apiCalls);
		
		parent::display($tpl);
		$this->_setToolbar();
	}

	
	/**
	 * Setup the Toolbar
	 */
	protected function _setToolbar()
	{
		$title = JText::_('API SCHEDULE');
		JToolBarHelper::title($title, 'schedule');
		
		JToolBarHelper::custom('schedule.run', 'run', 'run', 'Run now');
		
		JToolBarHelper::publishList('schedule.publish', 'Enable');
		JToolBarHelper::unpublishList('schedule.unpublish', 'Disable');
		
		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_eve', 480, 640);
	}
	
	
	function _display($tpl = null) {
		global $mainframe;
		
		require_once JPATH_COMPONENT.DS.'html'.DS.'filter.php';
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/com_eve/assets/common.css');
		
		
		$context = 'com_eve.schedule.index.';
		$filter_order		= $mainframe->getUserStateFromRequest( $context.'filter_order',		'filter_order',     'typeCall',	'cmd' );
		$filter_order_dir	= $mainframe->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',					'word' );
		$filter_state		= $mainframe->getUserStateFromRequest( $context.'filter_state',		'filter_state',		'',			'word' );
		$filter_apicall		= $mainframe->getUserStateFromRequest( $context.'filter_apicall',	'filter_apicall',	'',			'int' );
		
		$search 	= $mainframe->getUserStateFromRequest( $context.'filter_search', 'filter_search', '', 'string' );
		$search 	= JString::strtolower( $search );
		
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
		
		$model = $this->getModel();
		$q = $model->getQuery();
		$q->addTable('#__eve_schedule', 'sc');
		$q->addJoin('#__eve_apicalls', 'ap', 'ap.id=sc.apicall');
		$q->addQuery('COUNT(*)');
		if ($search) {
			$q->addJoin('#__eve_accounts', 'a', 'sc.userID=a.userID');
			$q->addJoin('#__users', 'u', 'a.owner=u.id');
			$q->addJoin('#__eve_characters', 'c', 'sc.characterID=c.characterID');
			$searchString = sprintf('u.name LIKE %1$s OR c.name LIKE %1%s', 
				$q->Quote( '%'.$q->getEscaped( $search, true ).'%', false ));
			$q->addWhere($searchString);
		}
		if ($filter_apicall) {
			$q->addWhere('sc.apicall='.$filter_apicall);
		}
		if ($filter_state == 'P') {
			$q->addWhere('sc.published');
		}
		if ($filter_state == 'U') {
			$q->addWhere('sc.published = 0');
		}
		
		$total = $q->loadResult();
		
		$q = $model->getQuery();
		$q->addTable('#__eve_apicalls');
		$q->addOrder('`type`');
		$q->addOrder('`call`');
		$q->addQuery('`id`', 'CONCAT(`type`, \'/\', `call`) AS typeCall');
		$apicalls = $q->loadObjectList();
		$empty = array('id'=>'0', 'typeCall'=>JText::_('Select API call'));
		//$empty = ;
		array_unshift($apicalls, JArrayHelper::toObject($empty));
		
		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );
		
		$q = $model->getQuery();
		$q->addTable('#__eve_schedule', 'sc');
		$q->addJoin('#__eve_apicalls', 'ap', 'ap.id=sc.apicall');
		$q->addJoin('#__eve_accounts', 'a', 'sc.userID=a.userID');
		$q->addJoin('#__users', 'u', 'a.owner=u.id');
		$q->addJoin('#__eve_characters', 'c', 'sc.characterID=c.characterID');
		$q->addQuery('ap.*', 'sc.*');
		$q->addQuery('CONCAT(`type`, \'/\',`call`) AS typeCall');
		$q->addQuery('u.name AS userName');
		$q->addQuery('c.name AS characterName');
		$q->addOrder($filter_order, $filter_order_dir);
		$q->setLimit($limit, $limitstart);
		if ($search) {
			$q->addWhere($searchString);
		}
		if ($filter_apicall) {
			$q->addWhere('sc.apicall='.$filter_apicall);
		}
		if ($filter_state == 'P') {
			$q->addWhere('sc.published');
		}
		if ($filter_state == 'U') {
			$q->addWhere('sc.published = 0');
		}
		$items = $q->loadObjectList();
		
		$this->assign('filter_search', $search);
		$this->assign('filter_state', $filter_state);
		$this->assign('filter_apicall', $filter_apicall);
		$this->assign('filter_order', $filter_order);
		$this->assign('filter_order_dir', $filter_order_dir);
		$this->assignRef('apicalls', $apicalls);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		parent::display();
	}

	function loadItem($index=0)
	{
		$item =& $this->items[$index];
		$item->index	= $index;
		
		$item->url		= JRoute::_('index.php?option=com_eve&control=schedule&task=edit&cid[]='.$item->id);

		$this->assignRef('item', $item);
	}
}

?>