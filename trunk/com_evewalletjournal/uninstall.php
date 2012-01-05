<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Wallet Journal
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

$plugins = array(
array('eveapi', 'evewalletjournal'),
);

$where = array();
foreach ($plugins as $plugin) {
	$where[] = vsprintf("(folder='%s' AND element='%s')", $plugin);
}

$query = 'SELECT id FROM #__plugins WHERE '.implode(' OR ', $where);

$dbo = JFactory::getDBO();
$dbo->setQuery($query);
$tmp = $dbo->loadResultArray();
$plugins = array();
foreach ($tmp as $plugin) {
	$plugins[$plugin] = 0;
}

$model = JModel::getInstance('Plugins', 'InstallerModel');
$model->remove($plugins);

function com_uninstall() {
	EveHelper::clearApiCalls('char', 'WalletJournal');
	EveHelper::clearApiCalls('corp', 'WalletJournal');

	return true;
}