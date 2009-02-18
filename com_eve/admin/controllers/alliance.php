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

class EveControllerAlliance extends EveController {
	
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		
		//$this->setRedirect('?option=com_eve&control=alliance');
		
		$this->registerTask('remove', 'remove');
		$this->registerTask('add', 'addedit');
		$this->registerTask('edit', 'addedit');
		$this->registerTask('apply', 'applysave');
		$this->registerTask('save', 'applysave');
		$this->registerTask('get_alliance_list', 'getAllianceList');
		$this->registerTask('get_alliance_members', 'getAllianceMembers');
	}
	
	function addedit() {
		JRequest::setVar('view', 'alliance');
		$this->display();
	}
	
	function remove() {
		JRequest::checkToken() or die( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_eve&control=alliance' );
		
		$db 			=& JFactory::getDBO();
		$cid 			= JRequest::getVar( 'cid', array(), '', 'array' );
		$model 			= & $this->getModel('Alliance', 'EveModel');
		$table 			= $model->getTable('Alliances');

		JArrayHelper::toInteger( $cid );
		
		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an Alliance to delete', true ) );
		}
		
		foreach ($cid as $id) {
			$table->delete($id);
		}
		
		$url = JRoute::_('index.php?option=com_eve&control=alliance', false);
		$this->setRedirect($url, JText::_('ALLIANCE DELETED'));
			}
	
	function applysave() {
		JRequest::checkToken() or die( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_eve' );

		$post = JRequest::get('post');
		
		$model = & $this->getModel('Alliance');
		$table = $model->getTable('Alliances');
		
		$table->load(JRequest::getInt('allianceID')); 
		
		if (!$table->bind( $post )) {
			return JError::raiseWarning( 500, $table->getError() );
		}
		
		if (!$table->check()) {
			return JError::raiseWarning( 500, $table->getError() );
		}
		
		if (!$table->store()) {
			return JError::raiseWarning( 500, $table->getError() );
		}
				
		$task = $this->getTask();
		
		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option=com_eve&control=alliance&task=edit&cid[]='. $table->allianceID ;
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_eve&control=alliance';
				break;
		}

		$this->setRedirect( JRoute::_($link, false), JText::_( 'ALLIANCE SAVED' ) );	
	}
	
	function getAllianceList() {
		$model = & $this->getModel('Alliance', 'EveModel');
		
		$msg = null;
		if ($model->apiGetAllianceList()) {
			$msg = JText::_('ALLIANCES SUCCESSFULLY IMPORTED');
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_eve&control=alliance', false), $msg);
	}
	
	function getAllianceMembers() {
		$model = & $this->getModel('Alliance', 'EveModel');
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		
		$msg = null;
		if ($model->apiGetAllianceMembers($cid)) {
			$msg = JText::_('ALLIANCES SUCCESSFULLY IMPORTED');
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_eve&control=alliance', false), $msg);
	}
	
	
}
?>