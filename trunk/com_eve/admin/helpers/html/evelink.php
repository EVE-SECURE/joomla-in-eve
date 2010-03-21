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
	
	static public function character($character, $corporation = null, $alliance = null)
	{
		if (is_array($character)) {
			$characterID = $character[1].'ID';
			$characterName = $character[1].'Name';
			$characterObj = $character[0];
		} else {
			$characterID = 'characterID';
			$characterName = isset($character->characterName) ? 'characterName' : 'name';
			$characterObj = $character;
		}
		$class = 'ccpeve character-'.$characterObj->$characterID;
		$html = '<a class="'.$class.'" href="'.EveRoute::character($character, $corporation, $alliance).'">'.
					$characterObj->$characterName.
				'</a>';
		return $html;
	}

	static public function corporation($corporation, $alliance = null)
	{
		if (is_array($corporation)) {
			$corporationID = $corporation[1].'ID';
			$corporationName = $corporation[1].'Name';
			$corporationTicker = $corporation[1].'Ticker';
			$corporationObj = $corporation[0];
		} else {
			$corporationID = 'corporationID';
			$corporationName = isset($corporation->corporationName) ? 'corporationName' : 'name';
			$corporationTicker = isset($corporation->corporationTicker) ? 'corporationTicker' : 'ticker';
			$corporationObj = $corporation;
		}
		$class = 'ccpeve corporation-'.$corporationObj->$corporationID;
		$html = '<a class="'.$class.'" href="'.EveRoute::corporation($corporation, $alliance).'">'.
					$corporationObj->$corporationName.' ['.$corporationObj->$corporationTicker.']'.
				'</a>';
		return $html;
	}

	static public function alliance($alliance)
	{
		if (is_array($alliance)) {
			$allianceID = $alliance[1].'ID';
			$allianceName = $alliance[1].'Name';
			$allianceShortName = $alliance[1].'ShortName';
			$allianceObj = $alliance[0];
		} else {
			$allianceID = 'allianceID';
			$allianceName = isset($alliance->allianceName) ? 'allianceName' : 'name';
			$allianceShortName = isset($alliance->allianceShortName) ? 'allianceShortName' : 'shortName';
			$allianceObj = $alliance;
		}
		$class = 'ccpeve alliance-'.$allianceObj->$allianceID;
		$html = '<a class="'.$class.'" href="'.EveRoute::alliance($alliance).'">'.
					$allianceObj->$allianceName.' &lt;'.$allianceObj->$allianceShortName.'&gt;'.
				'</a>';
		return $html;
	}
	
	static public function ship($ship, $href = '#')
	{
		return self::type($ship, $href, 'ship');
	}
	
	static public function station($station, $href = '#')
	{
		return self::type($station, $href, 'station');
	}
	
	static public function solarSystem($solarSystem, $href = '#')
	{
		return self::type($solarSystem, $href, 'solarSystem');
	}
	
	static public function type($item, $href = '#', $type = 'type')
	{
		if (is_array($item)) {
			$itemID = JArrayHelper::getValue($item, 1, $type).'ID'.JArrayHelper::getValue($item, 2, '');
			$itemName = JArrayHelper::getValue($item, 1, $type).'Name'.JArrayHelper::getValue($item, 2, '');
			$itemObj = $item[0];
		} else {
			$itemID = $type.'ID';
			$itemName = $type.'Name';
			$itemObj = $item;
		}
		$class = 'ccpeve '.$type.'-'.$itemObj->$itemID;
		$html = '<a class="'.$class.'" href="'.$href.'">'.
					$itemObj->$itemName.
				'</a>';
		return $html;
	}
	
	
}