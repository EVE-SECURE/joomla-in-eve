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

class JHTMLFilter {
	
	function select($name, $selected = null, $definition = null) {
		if (empty($definition)) {
			$definition = JPATH_COMPONENT.DS.'filters.xml';
		}
		
		$filter_data = file_get_contents($definition);
		$xml = new SimpleXMLElement($filter_data);
		$xpath = $xml->xpath('/filters/filter[@name="'.$name.'"]/option');
		return JHTML::_('select.genericlist', $xpath, $name, 'onchange="document.adminForm.submit()"', 'key', 'text', $selected, false, true);
	}
	
	function search($value, $name = 'filter_search') {
		return JText::_( 'Filter' ). ':'.
		'<input type="text" name="'.$name.'" id="'.$name.'" value="'.$value.'" class="text_area" onchange="document.adminForm.submit();" />'.
		'<button onclick="this.form.submit();">'.JText::_( 'Go' ).'</button>'.
		'<button onclick="document.getElementById(\''.$name.'\').value=\'\';this.form.submit();">'.JText::_( 'Reset' ).'</button>';
		
	}
}
?>