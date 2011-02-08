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

defined('_JEXEC') or die;

jimport('joomla.database.query');

class JElementAlliance extends JElement
{
	/**
	 * The name of the element.
	 *
	 * @var		string
	 */
	var	$_name = 'Alliance';

	function fetchElement($name, $value, &$node, $control_name)
	{
		JHTML::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'helpers'.DS.'html');
		$options = "
		{
			postVar: 'filter_search',
			injectChoice: function(choice) {
				var el = new Element('li')
					.setHTML(this.markQueryValue(choice.allianceID)+':'+this.markQueryValue(choice.name));
				el.inputValue = choice.allianceID+':'+choice.name;
				this.addChoiceEvents(el).injectInside(this.choices);
			},
		}";
		$url = JRoute::_('index.php?option=com_eve&task=alliance.search&format=raw', false);
		JHTML::_('autocompleter.json', $control_name.$name, $url, $options);
		
		$size = ($node->attributes('size') ? 'size="'.$node->attributes('size').'"' : '');
		$class = ($node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="text_area"');

		// Required to avoid a cycle of encoding &
		$value = htmlspecialchars(htmlspecialchars_decode($value, ENT_QUOTES), ENT_QUOTES);

		return '<input type="text" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'" value="'.$value.'" '.$class.' '.$size.' />';
	}
}