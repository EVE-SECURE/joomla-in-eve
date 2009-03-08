<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Plugin User - EVE
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
jimport('joomla.plugin.plugin');

/**
 * Joomla User plugin
 *
 * @author		Johan Janssens  <johan.janssens@joomla.org>
 * @package		Joomla
 * @subpackage	JFramework
 * @since 		1.5
 */
class plgUserEVE extends JPlugin
{
	
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param 	object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function plgUserEVE(& $subject, $config) {
		parent::__construct($subject, $config);
	}

	/**
	 * Unassign alts from deleted user
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param 	array	  	holds the user data
	 * @param	boolean		true if user was succesfully stored in the database
	 * @param	string		message
	 */
	function onAfterDeleteUser($user, $succes, $msg)
	{
		if(!$succes) {
			return false;
		}

		$db =& JFactory::getDBO();
		$db->setQuery('UPDATE #__eve_characters SET userID = 0 WHERE userID = '.$db->Quote($user['id'])); 
		$db->Query();
		return true;
	}
		
	/**
	 * Example store user method
	 *
	 * Method is called after user data is stored in the database
	 *
	 * @param 	array		holds the new user data
	 * @param 	boolean		true if a new user is stored
	 * @param	boolean		true if user was succesfully stored in the database
	 * @param	string		message
	 */
	function onAfterStoreUser($user, $isnew, $succes, $msg)
	{
		global $mainframe;
		
		if( $mainframe->isAdmin()) {
		 	return false;
		}
		
		if ($isnew) {
			$dbo = JFactory::getDBO();
			$sql = 'UPDATE #__users SET block=1 WHERE id='.intval($user['id']);
			$dbo->setQuery($sql);
			$dbo->query();
		}
		
		
	}

}
