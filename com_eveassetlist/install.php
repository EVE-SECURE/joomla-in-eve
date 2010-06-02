<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Asset List
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

$app = JFactory::getApplication();

$plugins = array('eveapi_eveassetlist');
foreach ($plugins as $plugin) {
	$p_dir = $this->parent->getPath('source').DS.'plugins'.DS.$plugin;
	
	$package = array();
	$package['packagefile'] = null;
	$package['extractdir'] = null;
	$package['dir'] = $p_dir;
	$package['type'] = JInstallerHelper::detectType($p_dir);
	
	$installer = new JInstaller();
	
	// Install the package
	if (!$installer->install($package['dir'])) {
		// There was an error installing the package
		$msg = JText::sprintf('INSTALLEXT', JText::_($package['type']), JText::_('Error'));
		$app->enqueueMessage($msg);
		$result = false;
	} else {
		// Package installed sucessfully
		$msg = JText::sprintf('INSTALLEXT', JText::_($package['type']), JText::_('Success'));
		$app->enqueueMessage($msg);
		$result = true;
	}
}

function com_install() {
	$app = JFactory::getApplication();
	jimport('joomla.filesystem.file');
	$manifestPath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eveassetlist'.DS.'eveassetlist.xml';
	$version = null;
	if (JFile::exists($manifestPath)) {
		$manifestContent = JFile::read($manifestPath);
		$manifest = new SimpleXMLElement($manifestContent);
		$version = (string) $manifest->version;
		$versionNumbers = explode('.', $version);
		$version = $versionNumbers[0].'.'.$versionNumbers[1]; 
	}
	
	$dbo = JFactory::getDBO();
	switch ($version) {
		case '0.5':
		case '0.6':
			break;
		default:
			$sql = "UPDATE #__plugins SET published = 1 WHERE element = 'eveassetlist'";
			$dbo->setQuery($sql);
			if ($dbo->query()) {
				$msg = JText::sprintf('Plugins enabled');
				$app->enqueueMessage($msg);
			}
			EveHelper::scheduleApiCalls('eveassetlist', true);
	}
	return true;
}
