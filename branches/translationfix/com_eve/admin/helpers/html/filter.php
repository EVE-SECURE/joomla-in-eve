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


abstract class JHTMLFilter {
	
	static function search($value, $name = 'filter_search', $id = null) {
		if (is_null($id)) {
			$id = $name;
		}
		return '<label for="'.$id.'">'.JText::_('COM_EVE_FILTER_SEARCH'). ': </label>'.
		'<input class="inputbox" type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" class="text_area" onchange="document.adminForm.submit();" /> '.
		'<button class="button" onclick="document.adminForm.submit();">'.JText::_('COM_EVE_FILTER_SUBMIT').'</button> '.
		'<button class="button" onclick="$(\''.$name.'\').value=\'\';this.form.submit();">'.JText::_('COM_EVE_FILTER_RESET').'</button> ';
	}
	
	
	static function owner($active, $option) {
		$options = array();
		$options[]	= JHtml::_('select.option', '0', JText::_('COM_EVE_FILTER_SHOW_ALL'));
		$options[]	= JHtml::_('select.option', '1', JText::_($option));

		$attribs = 'class="inputbox" size="1" onchange="document.adminForm.submit();"';
		$html = JHtml::_('select.genericlist', $options, 'filter_owner', $attribs, 'value', 'text', $active);
		echo $html;
	}
	
	static function membersof($active, $name = 'filter_membersof', $idtag = false) {
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('COM_EVE_FILTER_SHOW_ALL'));
		$options[] = JHtml::_('select.option', '*', JText::_('COM_EVE_FILTER_OWNER_MEMBERS'));
		$q = EveFactory::getQuery();
		$q->addTable('#__eve_alliances');
		$q->addQuery('allianceID AS value', 'name AS text');
		$q->addWhere('owner > 0');
		$q->addOrder('name');
		$list = $q->loadObjectList();
		if (!empty($list)) {
			$options[] = JHTML::_('select.option',  '<OPTGROUP>', JText::_( 'COM_EVE_FILTER_OWNER_ALLIANCES_MEMBERS' ) );
			foreach ($list as $item) {
				$options[] = JHtml::_('select.option', 'a'.$item->value, $item->text);
			}
			$options[] = JHTML::_('select.option',  '</OPTGROUP>' );
		}
		$q = EveFactory::getQuery();
		$q->addTable('#__eve_corporations', 'co');
		$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
		$q->addQuery('corporationID AS value');
		$q->addQuery('IF(al.allianceID IS NULL, corporationName, CONCAT(corporationName, \' [\', al.shortName, \']\')) AS text');
		$q->addWhere('al.owner > 0 OR co.owner > 0');
		$q->addOrder('corporationName');
		$list = $q->loadObjectList();
		if (!empty($list)) {
			$options[] = JHTML::_('select.option',  '<OPTGROUP>', JText::_( 'COM_EVE_FILTER_OWNER_CORPORATIONS_MEMBERS' ) );
			foreach ($list as $item) {
				$options[] = JHtml::_('select.option', 'c'.$item->value, $item->text);
			}
			$options[] = JHTML::_('select.option',  '</OPTGROUP>' );
		}
		$attribs = 'class="inputbox" size="1" onchange="document.adminForm.submit();"';
		$html = JHtml::_('select.genericlist', $options, $name, $attribs, 'value', 'text', $active, $idtag);
		echo $html;
	}
}