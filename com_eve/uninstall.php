<?php

function com_uninstall() {
	$plugins = array(
		array('user', 'eve'),
		array('cron', 'eve'),
		array('eveapi', 'eve'),
		array('system', 'eve'),
	);
	
	$where = array();
	foreach ($plugins as $plugin) {
		$where[] = vsprintf("(folder='%s' AND element='%s')", $plugin);
	}
	
	$query = 'SELECT id FROM #__plugins WHERE '.implode(' OR ', $where);
	
	$dbo = JFactory::getDBO();
	$dbo->setQuery($query);
	$tmp = $dbo->loadResultArray();
	$plugins = array();
	foreach ($tmp as $plugin) {
		$plugins[$plugin] = 0;
	}
	
	$model = JModel::getInstance('Plugins', 'InstallerModel');
	$model->remove($plugins);
	return true;
}