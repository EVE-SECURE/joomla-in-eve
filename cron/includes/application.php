<?php
/**
* @version		$Id$
* @package		Joomla
* @subpackage	Cron
* @copyright	Copyright (C) 2005 - 2009 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
* Joomla! XML-RPC Application class
*
* Provide many supporting API functions
*
* @package		Joomla
* @final
*/
class JCron extends JApplication
{

	/**
	* Class constructor
	*
	* @access protected
	* @param	array An optional associative array of configuration settings.
	* Recognized key values include 'clientId' (this list is not meant to be comprehensive).
	*/
	function __construct($config = array()) {
		$config['clientId'] = 4;
		
		parent::__construct($config);


	}

	/**
	 * Render empty HTML
	 *
	 */
	function render() {
		$template = file_get_contents(JPATH_CRON.DS.'html'.DS.'render.html');
		JResponse::setBody($template);
	}
	
	function _createSession($name) {
		return null;
	}
	
	function _createConfiguration($file) {
		return parent::_createConfiguration($file);
	}
}
