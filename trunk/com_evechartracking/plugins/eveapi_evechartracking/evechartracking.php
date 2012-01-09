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

/**
 * Joomla! in EVE Api core plugin
 *
 * @author		Pavol Kovalik  <kovalikp@gmail.com>
 * @package		Joomla! in EVE
 * @subpackage	Core
 */
class plgEveapiEvechartracking extends EveApiPlugin 
{
	function __construct($subject, $config = array()) 
	{
		parent::__construct($subject, $config);
	}

	public function corpMemberTracking($xml, $fromCache, $options = array()) 
	{
		if (!isset($options['corporationID'])) {
			$characterID = JArrayHelper::getValue($options, 'characterID');
			$character = EveFactory::getInstance('Character', $characterID);
			$corporationID = $character->corporationID;
		} else {
			$corporationID = $options['corporationID'];
		}
		if (!$corporationID) {
			//TODO: some reasonable error?
			return;
		}
		$memberIDs = array();
		foreach ($xml->result->members as $characterID => $member) {
			$sheet = $member->toArray();
			$sheet['corporationID'] = $corporationID;
			$character = EveFactory::getInstance('Character', $characterID);
			$character->save($sheet);
			$memberIDs[] = $characterID;
		}
		if (!empty($memberIDs)) {
			$sql = sprintf('UPDATE #__eve_characters SET corporationID=0 WHERE corporationID=%s AND characterID NOT IN(%s)',
			intval($corporationID), implode(', ', $memberIDs));
			$dbo = JFactory::getDBO();
			$dbo->Execute($sql);
		}
		//TODO: block user that are not in owner corps
	}

}
