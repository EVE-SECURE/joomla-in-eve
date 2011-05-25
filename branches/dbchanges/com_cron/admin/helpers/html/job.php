<?php
/**
 * @version		$Id$
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JHTMLJob
{
	static public function state($name, $attribs = null, $active=null, $idtag = false)
	{
		$options	= array();
		//$options[]	= JHtml::_('select.option', '*', JText::_('- Select State -'));
		$options[]	= JHtml::_('select.option', '0', JText::_('Disabled'));
		$options[]	= JHtml::_('select.option', '1', JText::_('enabled'));
		
		$html = JHtml::_('select.radiolist', $options, $name, $attribs, 'value', 'text', $active);
		return $html;
	}
	
	static public function type($name, $attribs, $selected = null, $idtag = false)
	{
		$client_id = 0;
		$query = 'SELECT folder AS value, folder AS text'
			. ' FROM #__plugins'
			. ' WHERE client_id = '.(int) $client_id
			. ' GROUP BY folder'
			. ' ORDER BY folder'
			;
		$types[] = JHTML::_('select.option',  '', '- '. JText::_( 'Select Type' ) .' -');
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$types 			= array_merge($types, $db->loadObjectList());
		return JHTML::_('select.genericlist',   $types, $name, $attribs, 'value', 'text', $selected, $idtag);
	}
	
} 