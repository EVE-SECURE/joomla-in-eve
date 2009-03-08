<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Plugin System - EVE
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

class  plgSystemEVE extends JPlugin {

	function plgSystemEVE(& $subject, $config) {
		parent::__construct($subject, $config);
	}

	/*
	 * Initializes igb template, provides automatic login if trusted
	 */

	function onAfterInitialise() {
		global $mainframe;
		if( $mainframe->isAdmin()) {
		 	return;
		}
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'loader.php';
		
		if (!EveHelperIgb::isIgb()) {
			return;
		}
		
		
		$igb_template = $this->params->get('igb_template');
		$mainframe->setTemplate($igb_template);

		if (!EveHelperIgb::isTrusted()) {
			$trustme = $this->params->get('trustme'); 
			$site = JURI::base();
			//die("eve.trustme:$site::$trustme");
			header("eve.trustme:$site::$trustme");
			return;
		}
		
	}

}
