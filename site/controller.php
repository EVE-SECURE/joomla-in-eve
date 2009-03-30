<?php
/**
 * @version		$Id$
 * @author		Nigel Bazzeghin
 * @package		EVE Custom Registration
 * @subpackage	Core
 * @copyright	Copyright (C) 2009 Nigel Bazzeghin. All rights reserved.
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
 
// No direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport('joomla.application.component.controller');
 
/**
 * EVE Custom Registration Component Controller
 *
 * @package    EVE Custom Registration
 * @subpackage Core
 */
class EVERegisterController extends JController
{
    
	function __construct($config = array()) {
		parent::__construct($config);
		$this->registerTask('chkAPI', 'chkAPI');
		$this->registerTask('register', 'register');
	}
	/** 
	 * Method to check for valid API Key and if user is in proper corp/alliance
	 * @return unknown_type
	 */
	function chkAPI() {
		$model = $this->getModel('EVERegister');
		if ($model->chkAPI($_POST)) {
			//$this->setRedirect(JRoute::_('index.php?option=com_everegister&view=everegister&layout=chkapi_success', false));
			JRequest::setVar( 'view', 'everegister' );
			JRequest::setVar( 'layout', 'chkapi_success'  );
			parent::display();
			
		} else {
			$this->setRedirect(JRoute::_('index.php?option=com_everegister&view=everegister&layout=chkapi_failure', false));
		}
		
	}
	function register() {
		$model = $this->getModel('EVERegister');
		JRequest::setVar( 'view', 'everegister' );
		JRequest::setVar( 'layout', 'register'  );
		parent::display();
	}
    /**
     * Method to display the view
     *
     * @access    public
     */
    function display()
    {
        parent::display();
    }

}
