<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2009 Pavol Kovalik. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Cron Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_cron
 * @since		1.5
 */
class CronController extends JController
{
	/**
	 * Method to display a view.
	 */
	function display()
	{
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the default view name and format from the Request.
		$vName		= JRequest::getWord('view', 'jobs');
		$vFormat	= $document->getType();
		$lName		= JRequest::getWord('layout', 'default');

		// Get and render the view.
		if ($view = &$this->getView($vName, $vFormat))
		{
			// Get the model for the view.
			$model = &$this->getModel($vName);

			// Push the model into the view (as default).
			$view->setModel($model, true);
			$view->setLayout($lName);

			// Push document object into the view.
			$view->assignRef('document', $document);

			$view->display();
		}
	}
}