<?php

$app = JFactory::getApplication();

$plugins = array('eveapi_eve', 'system_eve', 'search_eve', 'user_eve', 'cron_eve');
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
	$dbo = JFactory::getDBO();
	$sql = "UPDATE #__plugins SET published = 1 WHERE element = 'eve'";
	$dbo->setQuery($sql);
	if ($dbo->query()) {
		$msg = JText::sprintf('Plugins enabled');
		$app->enqueueMessage($msg);
	}
}

function com_install() {
	?>
	<p>
		I should propably put some nice intro here.
	</p>
	<p>
		Before you start using this component, make sure to:
		<ol>
			<li>
				<a href="<?php echo JRoute::_('index.php?option=com_config'); ?>" target="_blank">
					<?php echo JText::_('Turn off "New User Account Activation"'); ?> if you want to use API account activation
				</a>
			</li>
			<li>
				Find &quot;language/en-GB/en-GB.com_user.ini&quot; file.
				Replace &quot;REG_COMPLETE=You may now log in.&quot; with &quot;REG_COMPLETE=Register your EVE API key to log in.&quot;
			</li>
			<li>
				<a href="<?php echo JRoute::_('index.php?option=com_eve'); ?>">
					<?php echo JText::_('Go and play'); ?>
				</a>
			</li>
		</ol>
	</p>
	<p>
		Feel free to throw some ISK at <a href="#">Lumy</a> to support this project.
	</p>
	<?php
	return true;
}
