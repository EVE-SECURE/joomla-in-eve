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

class EveControllerChar extends EveController {
	
	function __construct( $config = array() )
	{
		//$config['name'] = 'char';
		parent::__construct( $config );
		
		//$this->setRedirect('?option=com_eve&control=char');
		
		$this->registerTask('remove', 'remove');
		$this->registerTask('add', 'addedit');
		$this->registerTask('edit', 'addedit');
		$this->registerTask('apply', 'applysave');
		$this->registerTask('save', 'applysave');
		$this->registerTask('get_character_sheet', 'getCharacterSheet');
		$this->registerTask('get_corporation_sheet', 'getCorporationSheet');
	}
	
	function addedit() {
		JRequest::setVar('view', 'char');
		$this->display();

	}
	
	function remove() {
		JRequest::checkToken() or die( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_eve&control=char' );
		
		$db 			=& JFactory::getDBO();
		$cid 			= JRequest::getVar( 'cid', array(), '', 'array' );
		$model 			= & $this->getModel('Char');
		$table 			= $model->getTable('Character');

		JArrayHelper::toInteger( $cid );
		
		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select a Character to delete', true ) );
		}
		
		foreach ($cid as $id) {
			$table->delete($id);
		}
		
		$url = JRoute::_('index.php?option=com_eve&control=char', false);
		$this->setRedirect($url, JText::_('CHARACTER DELETED'));
	}
	
	function applysave() {
		JRequest::checkToken() or die( 'Invalid Token' );
		
		$this->setRedirect( 'index.php?option=com_eve' );
		$model = & $this->getModel('Char');
		$model->store();
		$task = $this->getTask();
		
		switch ($task)
		{
			case 'apply':
				$url = 'index.php?option=com_eve&control=char&task=edit&cid[]='. $table->characterID ;
				break;

			case 'save':
			default:
				$url = 'index.php?option=com_eve&control=char';
				break;
		}

		$this->setRedirect(JRoute::_($url, false));
	}
	
	function getCharacterSheet() {
		$model = & $this->getModel('Char', 'EveModel');
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		
		$msg = null;
		if ($model->apiGetCharacterSheet($cid)) {
			$msg = JText::_('CHARACTERS SUCCESSFULLY IMPORTED');
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_eve&control=char', false), $msg);
	}
	
	function getCorporationSheet() {
		$model = & $this->getModel('Char', 'EveModel');
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		
		if ($model->apiGetCorporationSheet($cid)) {
			$msg = JText::_('CORPORATIONS SUCCESSFULLY IMPORTED');
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_eve&control=char', false), $msg);
		
	}
	
}
?>