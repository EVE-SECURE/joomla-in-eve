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

class EveControllerAccess extends EveController {
	
	function __construct($config = array())
	{
		//$config['name'] = 'char';
		parent::__construct( $config );
		
		$this->registerTask('unpublish', 'publish');
	}
	
	/**
	 * Dummy method to redirect back to standard controller
	 *
	 * @return	void
	 */
	public function display()
	{
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=access', false));
	}
	
	public function cancel()
	{
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=eve', false));
	}
	
	function publish() {
		JRequest::checkToken() or jexit('Invalid Token');
		$cid		= JRequest::getVar('cid', array(), 'post', 'array');
		$task		= JRequest::getCmd('task');
		$enable		= ($task == 'publish');
		$model		= $this->getModel('Schedule');
		$result 	= $model->setEnabled($cid, $enable);
		if ($result) {
			$n = count( $cid );
			$this->setMessage( JText::sprintf( $enable ? 'Items enabled' : 'Items disabled', $n ) );
		}
		$url = 'index.php?option=com_eve&view=schedule';
		$this->setRedirect(JRoute::_($url, false));
	}
	
	function apply() {
		JRequest::checkToken() or jexit('Invalid Token');
		
		$app	= &JFactory::getApplication();
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		$access = JRequest::getVar('access', array(), 'post', 'array');
		$model = $this->getModel('Access');
		foreach ($access as $data) {
			$model->save($data);
		}
		
		$errors	= $model->getErrors();
		// Push up to three validation messages out to the user.
		for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
			if (JError::isError($errors[$i])) {
				$app->enqueueMessage($errors[$i]->getMessage(), 'notice');
			} else {
				$app->enqueueMessage($errors[$i], 'notice');
			}
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_eve&view=access', false));
	}
	
}
