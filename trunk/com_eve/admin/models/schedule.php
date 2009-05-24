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

jimport('joomla.application.component.model');

class EveModelSchedule extends EveModel {
	
	function __construct($config = array()) {
		$config['table_path'] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'tables';
		parent::__construct($config);
	}
	
	function setEnabled($cid, $enabled) {
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initialize variables
		$dbo = $this->getDBO();

		if (empty($cid)) {
			JError::raiseWarning( 500, JText::_( 'No items selected' ) );
			return false;
		}

		JArrayHelper::toInteger( $cid );
		$cids = implode( ',', $cid );

		$query = 'UPDATE #__eve_schedule'
		. ' SET published = ' . (int) $enabled
		. ' WHERE id IN ( '. $cids.'  )';
		$dbo->setQuery( $query );
		if (!$dbo->query()) {
			JError::raiseWarning( 500, $dbo->getError() );
			return false;
		}
		return true;
	}
	
		
}
