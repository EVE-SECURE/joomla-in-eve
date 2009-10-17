<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Character Tracking
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
 * Joomla! in EVE Api core plugin
 *
 * @author		Pavol Kovalik  <kovalikp@gmail.com>
 * @package		Joomla! in EVE		
 * @subpackage	Core
 */
class plgEveapiEveWalletJournal extends JPlugin {
	function __construct($subject, $config = array()) {
		parent::__construct($subject, $config);
	}
	
	
	public function onSetOwnerCorporation($userID, $characterID, $owner) {
		//TODO: superclass EveapiPlugin
		$schedule = JTable::getInstance('Schedule', 'EveTable');
		$schedule->loadExtra('corp', 'WalletJournal', $userID, $characterID);
		if ($owner && !$schedule->id && $schedule->apicall) {
			$next = new DateTime();
			$schedule->next = $next->format('Y-m-d H:i:s');
			$schedule->store();
		}
		if (!$owner && $schedule->id) {
			$schedule->delete();
		}
	}
	
	public function charWalletJournal($xml, $fromCache, $options = array()) {
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_evewalletjournal'.DS.'tables');
		foreach ($xml->result->entries->toArray() as $entry) {
			$table = JTable::getInstance('Walletjournal', 'EvewalletjournalTable');
			$table->bind($entry);
			$table->store();
			
		}
	}
	
}
