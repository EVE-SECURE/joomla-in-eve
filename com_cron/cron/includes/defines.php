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

//Joomla framework path definitions
$parts = explode( DS, JPATH_BASE );
array_pop( $parts );

define( 'JPATH_ROOT',			implode( DS, $parts ) );
define( 'JPATH_SITE',			JPATH_ROOT );
define( 'JPATH_CONFIGURATION',	JPATH_ROOT );
define( 'JPATH_INSTALLATION',	JPATH_ROOT . DS . 'installation' );
define( 'JPATH_ADMINISTRATOR',	JPATH_ROOT . DS . 'administrator' );
define( 'JPATH_CRON', 			JPATH_ROOT . DS . 'cron' );
define( 'JPATH_XMLRPC', 		JPATH_ROOT . DS . 'xmlrpc' );
define( 'JPATH_LIBRARIES',		JPATH_ROOT . DS . 'libraries' );
define( 'JPATH_PLUGINS',		JPATH_ROOT . DS . 'plugins'   );
define( 'JPATH_CACHE',			JPATH_BASE . DS . 'cache');
