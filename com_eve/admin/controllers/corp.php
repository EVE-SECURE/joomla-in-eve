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

class EveControllerCorp extends EveController {
	
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		
		//$this->setRedirect('?option=com_eve&control=corp');
		
		$this->registerTask('remove', 'remove');
		$this->registerTask('add', 'addedit');
		$this->registerTask('edit', 'addedit');
		$this->registerTask('apply', 'applysave');
		$this->registerTask('save', 'applysave');
		$this->registerTask('getCorporationSheet', 'getCorporationSheet');
		$this->registerTask('getMemberTracking', 'getMemberTracking');
	}
	
	function addedit() {
		JRequest::setVar('view', 'corp');
		$this->display();
	}
	
	function remove() {
		JRequest::checkToken() or die( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_eve&control=corp' );
		
		$db 			=& JFactory::getDBO();
		$cid 			= JRequest::getVar( 'cid', array(), '', 'array' );
		$model 			= & $this->getModel('Corp', 'EveModel');
		$table 			= $model->getTable('Corporation');

		JArrayHelper::toInteger( $cid );
		
		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select a Corporation to delete', true ) );
		}
		
		foreach ($cid as $id) {
			$table->delete($id);
		}
		
		$url = JRoute::_('index.php?option=com_eve&control=corp', false);
		$this->setRedirect($url, JText::_('CORPORATION DELETED'));
	}
	
	function applysave() {
		JRequest::checkToken() or die( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_eve' );

		$model = & $this->getModel('Corp');
		$model->store();
		
		$task = $this->getTask();
		
		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option=com_eve&control=corp&task=edit&cid[]='. JRequest::getInt('corporationID');
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_eve&control=corp';
				break;
		}

		$this->setRedirect( JRoute::_($link, false));
				
		
	}
	
	function getCorporationSheet() {
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		$model = & $this->getModel('Corp', 'EveModel');
		$model->apiGetCorporationSheet($cid);
		$this->setRedirect(JRoute::_('index.php?option=com_eve&control=corp', false));
	}
	
	function getMemberTracking() {
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		$model = & $this->getModel('Corp', 'EveModel');
		$model->apiGetMemberTracking($cid);
		$this->setRedirect(JRoute::_('index.php?option=com_eve&control=corp', false));
	}
	
}
