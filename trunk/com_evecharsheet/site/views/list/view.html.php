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

class EvecharsheetViewList extends JView {
	function display($tmpl = null) {
		global $mainframe;
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet(JURI::base(). 'components/com_evecharsheet/assets/sheet.css');
		//$document->addScript('components/com_consulting/assets/product.js');
		
		$params = &$mainframe->getParams();
		$this->assignRef('params', $params);

		$context = 'com_evecharsheet.list.';
		
		$model = $this->getModel();
		
		$model->set('show_all', $params->get('show_all'));
		
		$layout = $this->getLayout();
		if ($layout == 'corporation') {
			$corporationID = JRequest::getInt('corporationID');
			$paramCorporationID = $this->params->get('corporationID');
			if (!$corporationID) {
				$corporationID = $paramCorporationID;
			}
			$model->set('corporationID', $corporationID);
			$corporation = $model->getInstance('corporation', $corporationID);
			//$this->assignRef('corporation', $corporation);
			$title = $corporationID == $paramCorporationID ? $this->params->get('page_title') : $corporation->corporationName.' ['.$corporation->ticker.']';
		} else {	
			$ownerID = JRequest::getInt('owner');
			$paramOwnerID = $this->params->get('owner');
			if (!$ownerID) {
				$ownerID = $paramOwnerID;
			}
			if (!$ownerID) {
				$user = JFactory::getUser();
				$ownerID = $user->id;
			}
			$model->set('owner', $ownerID);
			$owner = JFactory::getUser($ownerID);
			//$this->assignRef('owner', $owner);
			$title = $ownerID == $paramOwnerID ? $this->params->get('page_title') : $owner->name;
		}
		
		$displayFilter = $params->get('displayFilter');
		if ($displayFilter) {
			//parent::display('filter');
		}
		
		$total = $model->getCharacterCount();
		$limitstart			= JRequest::getVar('limitstart',		0,				'', 'int');
		$limit				= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$filter_order		= JRequest::getVar('filter_order',		'cd.ordering',	'', 'cmd');
		$filter_order_Dir	= JRequest::getVar('filter_order_Dir',	'ASC',			'', 'word');
		
		$characters = $model->getCharacters($limitstart, $limit);
		
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
		$this->assign('title', $title);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('characters', $characters);

		parent::display();
	}
	
}
