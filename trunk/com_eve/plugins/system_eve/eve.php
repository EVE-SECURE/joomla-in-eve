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
		jimport('joomla.html');
		//require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'loader.php';
		$base = JPATH_PLUGINS.DS.'system'.DS.'eve'.DS;
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'tables');
		JHTML::addIncludePath($base.'html');
		JLoader::register('EveFactory', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'lib'.DS.'factory.php');
		JLoader::register('EveTable', $base.'database'.DS.'table.php');
		JLoader::register('JQuery', $base.'database'.DS.'query.php');
		JLoader::register('EveModel', $base.'component'.DS.'model.php');
		JLoader::register('JModelList', $base.'component'.DS.'modellist.php');
		JLoader::register('EveController', $base.'component'.DS.'controller.php');
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'lib'.DS.'igb.php';
				
		if( $mainframe->isAdmin()) {
		 	return;
		}
		
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
