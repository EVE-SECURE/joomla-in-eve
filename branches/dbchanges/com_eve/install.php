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
	
}

function com_install() {
	$app = JFactory::getApplication();
	jimport('joomla.filesystem.file');
	$manifestPath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'eve.xml';
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
		case '0.2':
		case '0.5':
			$sql = "ALTER IGNORE TABLE `#__eve_apicalls` ADD UNIQUE `type_call` (`type`, `call`);";
			$dbo->setQuery($sql);
			if (!$dbo->query()) {
				$app->enqueueMessage($dbo->getError(), 'error');
			}
			$sql = "ALTER TABLE `#__eve_sections` ADD `roles` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `access` ;";
			$dbo->setQuery($sql);
			if (!$dbo->query()) {
				$app->enqueueMessage($dbo->getError(), 'error');
			}
			$sql = "SHOW INDEXES IN `#__eve_sections` WHERE key_name='name';";
			$dbo->setQuery($sql);
			if (!$dbo->loadRow()) {
				$sql = "ALTER IGNORE TABLE `#__eve_sections` ADD UNIQUE `name` (`name`);";
				$dbo->setQuery($sql);
				if (!$dbo->query()) {
					$app->enqueueMessage($dbo->getError(), 'error');
				}
			}
		case '0.6':
			$queries = array();
			$queries[] = "ALTER TABLE `#__eve_apicalls` CHANGE `params` `params` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ;";
			$queries[] = "ALTER TABLE `#__eve_apicalls` DROP INDEX `type_call` ;";
			$queries[] = "ALTER TABLE `#__eve_apicalls` ADD UNIQUE `type_call_params` (`type` ,`call` ,`params`) ;";
			foreach ($queries as $sql) {
				$dbo->setQuery($sql);
				$dbo->setQuery($sql);
				if (!$dbo->query()) {
					$app->enqueueMessage($dbo->getError(), 'error');
				}
			}
			break;
		default:
			//fresh install
			$sql = "SELECT id FROM `#__eve_apicalls` WHERE `call` = 'AllianceList'";
			$dbo->setQuery($sql);
			$id = $dbo->loadResult();
			if ($id) {
				$sql = "INSERT INTO `#__eve_schedule` (`apicall`, `userID`, `characterID`, `next`, `published`) VALUES ". 
					"(".$id.", NULL, NULL, '0000-00-00 00:00:00', 1);";
				$dbo->setQuery($sql);
				if (!$dbo->query()) {
					$app->enqueueMessage($error = $dbo->getError(), 'error');
				}
			}
			
			$sql = "UPDATE #__plugins SET published = 1 WHERE element = 'eve'";
			$dbo->setQuery($sql);
			if (!$dbo->query()) {
				$app->enqueueMessage($dbo->getError(), 'error');
			} else {
				$msg = JText::sprintf('Plugins enabled');
				$app->enqueueMessage($msg);
			}
	}
	
	$cron = JComponentHelper::getComponent('com_cron', true);
	if ($cron->enabled) {
		$sql = "SELECT id FROM #__cron_jobs WHERE event LIKE 'onCronTick'";
		$dbo->setQuery($sql);
		if (!$dbo->loadResult()) {
			$sql = "INSERT INTO `#__cron_jobs` (`id`, `pattern`, `type`, `plugin`, `event`, `next`, `runs`, `minutes`, `hours`, `days`, `months`, `weekdays`, `state`, `params`, `ordering`, `checked_out`, `checked_out_time`) VALUES 
			(1, '* * * * *', 'cron', '', 'onCronTick', '0000-00-00 00:00:00', 0, '.0.1.2.3.4.5.6.7.8.9.10.11.12.13.14.15.16.17.18.19.20.21.22.23.24.25.26.27.28.29.30.31.32.33.34.35.36.37.38.39.40.41.42.43.44.45.46.47.48.49.50.51.52.53.54.55.56.57.58.59.', '.0.1.2.3.4.5.6.7.8.9.10.11.12.13.14.15.16.17.18.19.20.21.22.23.', '.1.2.3.4.5.6.7.8.9.10.11.12.13.14.15.16.17.18.19.20.21.22.23.24.25.26.27.28.29.30.31.', '.1.2.3.4.5.6.7.8.9.10.11.12.', '.0.1.2.3.4.5.6.7.', 1, '', 1, 0, '0000-00-00 00:00:00');";
			$dbo->setQuery($sql);
			if ($dbo->query()) {
				$msg = JText::sprintf('Cron event inserted');
				$app->enqueueMessage($msg);
			} else {
				$msg = JText::sprintf('Failed to insert cron event');
				$app->enqueueMessage($msg, 'error');
			}
		}
	} else {
		$msg = JText::sprintf('Cron component not found');
		$app->enqueueMessage($msg, 'warning');
	}
	
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
