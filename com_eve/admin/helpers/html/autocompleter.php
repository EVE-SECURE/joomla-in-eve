<?php
/**
 * @version		$Id$
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JHtmlAutocompleter
{
	static public function json($id, $url, $options = '{}')
	{
		JHTML::_('behavior.combobox');
		JHTML::script('observer.js', 'media/com_eve/js/', true);
		JHTML::script('autocompleter.js', 'media/com_eve/js/', true);
		JHTML::stylesheet('autocompleter.css', 'media/com_eve/css/');

		$script  = "window.addEvent('domready', function(){ \n";
		$script .= "\tvar completer = new Autocompleter.Ajax.Json($('".$id."'), '".$url."', ".$options.");\n";
		//$script .= "\talert(completer);\n";
		$script .= "})\n";


		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);
	}
}