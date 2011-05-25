<?php
/**
* @version		$Id$
* @package		Joomla
* @subpackage	Cron
* @copyright	Copyright (C) 2005 - 2009 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Set flag that this is a parent file
define('_JEXEC', 1);

define('JPATH_BASE', dirname(__FILE__));

define('DS', DIRECTORY_SEPARATOR);

require_once JPATH_BASE.DS.'includes'.DS.'defines.php';
require_once JPATH_BASE.DS.'includes'.DS.'framework.php';

JDEBUG ? $_PROFILER->mark( 'afterLoad' ) : null;

// We want to log the errors so we could check them later
JError::setErrorHandling(E_ERROR,	'die');
JError::setErrorHandling(E_WARNING, 'log');
JError::setErrorHandling(E_NOTICE,	'log');

/**
 * CREATE THE APPLICATION
 *
 * NOTE :
 */
$mainframe =& JFactory::getApplication('cron');

JPluginHelper::importPlugin('system');

// trigger the onAfterInitialise events
JDEBUG ? $_PROFILER->mark('afterInitialise') : null;
$mainframe->triggerEvent('onAfterInitialise');


// Run cronned jobs
$mainframe->runJobs();
JDEBUG ? $_PROFILER->mark( 'afterRunJobs' ) : null;

/**
 * RENDER THE APPLICATION
 *
 * NOTE : 
 */
$mainframe->render();

// trigger the onAfterRender events
JDEBUG ? $_PROFILER->mark( 'afterRender' ) : null;
$mainframe->triggerEvent( 'onAfterRender' );

/**
 * RETURN THE RESPONSE
 */
echo JResponse::toString($mainframe->getCfg('gzip'));