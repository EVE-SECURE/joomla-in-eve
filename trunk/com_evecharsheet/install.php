<?php
global $mainframe;

$plugins = array('eveapi_evecharsheet', 'search_evecharsheet');
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
		$mainframe->enqueueMessage($msg);
		$result = false;
	} else {
		// Package installed sucessfully
		$msg = JText::sprintf('INSTALLEXT', JText::_($package['type']), JText::_('Success'));
		$mainframe->enqueueMessage($msg);
		$result = true;
	}
	$dbo = JFactory::getDBO();
	$sql = "UPDATE #__plugins SET published = 1 WHERE element = 'evecharsheet'";
	$dbo->setQuery($sql);
	if ($dbo->query()) {
		$msg = JText::sprintf('Plugins enabled');
		$app->enqueueMessage($msg);
	}
}
