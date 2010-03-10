<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Core
 * @copyright	Copyright (C) 2009 Pavol Kovalik. All rights reserved.
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


abstract class JHTMLevelink {
	
	public static function character($character, $corporation = null, $alliance = null)
	{
		if (is_array($character)) {
			$characterName = $character[1].'Name';
			$characterObj = $character[0];
		} else {
			$characterName = isset($character->characterName) ? 'characterName' : 'name';
			$characterObj = $character;
		}
		if (is_null($corporation)) {
			$corporation = $characterObj;
			$corporationObj = $corporation; 
		} else if (is_array($corporation)) {
			$corporationObj = $corporation[0];
		} else {
			$corporationObj = $corporation;
		}
		if (is_null($alliance)) {
			$alliance = $corporationObj;
		}
		$html = '<a href="'.EveRoute::_('character', $alliance, $corporation, $character).'">'.
					$characterObj->$characterName.
				'</a>';
		return $html;
	}

	public static function corporation($corporation, $alliance = null)
	{
		if (is_array($corporation)) {
			$corporationName = $corporation[1].'Name';
			$corporationTicker = $corporation[1].'Ticker';
			$corporationObj = $corporation[0];
		} else {
			$corporationName = isset($corporation->corporationName) ? 'corporationName' : 'name';
			$corporationTicker = isset($corporation->corporationTicker) ? 'corporationTicker' : 'ticker';
			$corporationObj = $corporation;
		}
		if (is_null($alliance)) {
			$alliance = $corporationObj;
		}
		$html = '<a href="'.EveRoute::_('corporation', $alliance, $corporation).'">'.
					$corporationObj->$corporationName.' ['.$corporationObj->$corporationTicker.']'.
				'</a>';
		return $html;
		
	}

	public static function alliance($alliance)
	{
		if (is_array($alliance)) {
			$allianceName = $alliance[1].'Name';
			$allianceShortName = $alliance[1].'ShortName';
			$allianceObj = $alliance[0];
		} else {
			$allianceName = isset($alliance->allianceName) ? 'allianceName' : 'name';
			$allianceShortName = isset($alliance->allianceShortName) ? 'allianceShortName' : 'shortName';
			$allianceObj = $alliance;
		}
		$html = '<a href="'.EveRoute::_('alliance', $alliance).'">'.
					$allianceObj->$allianceName.' &lt;'.$allianceObj->$allianceShortName.'&gt;'.
				'</a>';
		return $html;
	}
	
	public static function item($item, $href = '#')
	{
		
	}
	
	public static function ship($item, $href = '#')
	{
		
	}
	
	
	
}