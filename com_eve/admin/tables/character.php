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

class TableCharacter extends EveTable {
	/** @var int */
	var $userID			= null;
	
	//character sheet
	/** @var int */
	var $characterID	= null;
	/** @var string */
	var $name			= null;
	/** @var string */
	var $race 			= null;
	/** @var string */
	var $bloodLine 		= null;
	/** @var string */
	var $gender	 		= null;
	/** @var int */
	var $corporationID	= null;
	/** @var float */
	var $balance		= null;
	/** @var  int */
	
	//member tracking
	/** @var string */
	var $title			= null;
	/** @var datetime */
	var $startDateTime 	= null;
	/** @var  int */
	var $baseID			= null;
	/** @var datetime */
	var $logonDateTime	= null;
	/** @var datetime */
	var $logoffDateTime	= null;
	/** @var  int */
	var $locationID		= null;
	/** @var  int */
	var $shipTypeID		= null;
	/** @var  int */
	var $roles			= null;
	/** @var  int */
	var $grantableRoles	= null;
	

	/**
	* @param database A database connector object
	*/
	function __construct( &$dbo )
	{
		parent::__construct( '#__eve_characters', 'characterID', $dbo );
	}

}
