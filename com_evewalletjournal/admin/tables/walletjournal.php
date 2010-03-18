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

class EvewalletjournalTableWalletjournal extends JTable {
	var $id= null;
	var $date= null;
	var $refID= null;
	var $refTypeID= null;
	var $ownerName1= null;
	var $ownerID1= null;
	var $ownerName2= null;
	var $ownerID2= null;
	var $argName1= null;
	var $argID1= null;
	var $amount= null;
	var $balance= null;
	var $reason= null;
		
	/**
	* @param database A database connector object
	*/
	function __construct( &$dbo )
	{
		//FIXME: duplicate entries, use refID instead of id?
		parent::__construct( '#__eve_walletjournal', 'id', $dbo );
	}
	
}