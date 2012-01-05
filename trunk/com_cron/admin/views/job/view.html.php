<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2009 Pavol Kovalik. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of weblinks.
 *
 * @package		Joomla.job.inistrator
 * @subpackage	com_weblinks
 * @since		1.5
 */
class CronViewJob extends JView
{
	protected $state;
	protected $items;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$app	= JFactory::getApplication();
		$state	= $this->get('State');
		$item	= $this->get('Item');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->assignRef('state',	$state);
		$this->assignRef('item',	$item);

		$this->_setToolbar();
		parent::display($tpl);
	}

	/**
	 * Setup the Toolbar
	 */
	protected function _setToolbar()
	{
		JRequest::setVar('hidemainmenu', 1);

		JToolBarHelper::title('Cron', 'cron');
		JToolBarHelper::addNew('job.save2new', 'Save and new');
		JToolBarHelper::save('job.save');
		JToolBarHelper::apply('job.apply');
		if (empty($this->item->id))  {
			JToolBarHelper::cancel('job.cancel');
		} else {
			JToolBarHelper::cancel('job.cancel', 'Close');
		}
		JToolBarHelper::divider();
		JToolBarHelper::help('screen.cron');
	}
}
