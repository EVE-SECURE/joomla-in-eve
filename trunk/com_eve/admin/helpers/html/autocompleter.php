<?php
/**
 * @version		$Id$
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JHtmlAutocompleter
{
	static protected $location = 'administrator/components/com_eve/assets/autocompleter/';
	
	static public function json($id, $url, $options = '{}')
	{
		JHTML::_('behavior.combobox');
		JHTML::script('observer.js', self::$location, true);
		JHTML::script('autocompleter.js', self::$location, true);
		JHTML::stylesheet('autocompleter.css', self::$location);
		
		$script  = "window.addEvent('domready', function(){ \n";
		$script .= "\tvar completer = new Autocompleter.Ajax.Json($('".$id."'), '".$url."', ".$options.");\n";
		//$script .= "\talert(completer);\n";
		$script .= "})\n";
		
		
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);
	}
}