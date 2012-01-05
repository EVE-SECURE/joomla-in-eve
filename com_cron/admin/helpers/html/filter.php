<?php
/**
 * @version		$Id$
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JHTMLFilter
{
	static function state($name, $active = null, $attribs = null)
	{
		$options	= array();
		$options[]	= JHtml::_('select.option', '*', JText::_('- Select State -'));
		$options[]	= JHtml::_('select.option', '0', JText::_('Disabled'));
		$options[]	= JHtml::_('select.option', '1', JText::_('Enabled'));

		// Build the select list.
		$attr = 'class="inputbox" size="1" onchange="document.adminForm.submit();"';
		$html = JHtml::_('select.genericlist', $options, $name, $attribs, 'value', 'text', $active);

		return $html;
	}

}