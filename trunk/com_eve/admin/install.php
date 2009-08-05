<?php

$app = JFactory::getApplication();

$plugins = array('eveapi_eve', 'system_eve', 'user_eve', 'cron_eve');
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
	?>
	<p>
		I should propably put some nice intro here.
	</p>
	<p>
		Before you start using this component, make sure to:
		<ol>
			<li>
				<a href="<?php echo JRoute::_('index.php?option=com_plugins&search=eve'); ?>" target="_blank">
					<?php echo JText::_('Enable all plugins'); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo JRoute::_('index.php?option=com_config'); ?>" target="_blank">
					<?php echo JText::_('Turn off "New User Account Activation"'); ?>
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
