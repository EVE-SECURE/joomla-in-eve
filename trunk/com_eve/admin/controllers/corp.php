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

		$post = JRequest::get('post');
		
		$model = & $this->getModel('Corp');
		$table = $model->getTable('Corporation');
		
		$table->load(JRequest::getInt('corporationID')); 
				
		if (!$table->bind( $post )) {
			return JError::raiseWarning( 500, $table->getError() );
		}
		
		if ($table->standings === '') {
			$table->standings = null;
		}
		
		if (!$table->check()) {
			return JError::raiseWarning( 500, $table->getError() );
		}
		
		if (!$table->store(true)) {
			return JError::raiseWarning( 500, $table->getError() );
		}
				
		$task = $this->getTask();
		
		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option=com_eve&control=corp&task=edit&cid[]='. $table->corporationID ;
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_eve&control=corp';
				break;
		}

		$this->setRedirect( JRoute::_($link, false), JText::_( 'CORPORATION SAVED' ) );
				
		
	}
	
	function getCorporationSheet() {
		$model = & $this->getModel('Corp', 'EveModel');
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		
		$msg = null;
		if ($model->apiGetCorporationSheet($cid)) {
			$msg = JText::_('CORPORATIONS SUCCESSFULLY IMPORTED');
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_eve&control=corp', false), $msg);
	}
	
	function getMemberTracking() {
		$model = & $this->getModel('Corp', 'EveModel');
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		
		$msg = null;
		if ($model->apiGetMemberTracking($cid)) {
			$msg = JText::_('CHARACTERS SUCCESSFULLY IMPORTED');
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_eve&control=corp', false), $msg);
	}
	
}
