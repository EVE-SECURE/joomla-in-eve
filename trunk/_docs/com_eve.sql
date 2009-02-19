DROP TABLE IF EXISTS `jos_eve_apicache`;
CREATE TABLE `jos_eve_apicache` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `host` VARCHAR( 64 ) NOT NULL ,
  `path` VARCHAR( 64 ) NOT NULL ,
  `params` VARCHAR( 41 ) NOT NULL ,
  `content` TEXT NULL ,
  `currentTime` DATETIME NOT NULL ,
  `cachedUntil` DATETIME NOT NULL ,
PRIMARY KEY ( `id` )
) CHARSET=utf8;

DROP TABLE IF EXISTS `jos_eve_apicalls`;
CREATE TABLE IF NOT EXISTS `jos_eve_apicalls` (
  `id` int(11) NOT NULL auto_increment,
  `component` varchar(64) NOT NULL default '',
  `model` varchar(64) NOT NULL default '',
  `apicall` varchar(64) NOT NULL default '',
  `enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY (`id`)
) CHARSET=utf8;

DROP TABLE IF EXISTS `jos_eve_accounts`;
CREATE TABLE IF NOT EXISTS `jos_eve_accounts` (
  `userID` int(10) unsigned NOT NULL default '0',
  `owner` int(10) unsigned NOT NULL default '0',
  `apiKey` varchar(64) NOT NULL default '',
  `apiStatus` enum('Invalid','Limited','Full', 'Inactive') NOT NULL default 'Invalid',
  PRIMARY KEY (`userID`),
  KEY `eve_accounts_fk_owner` (`owner`)
) CHARSET=utf8;

DROP TABLE IF EXISTS `jos_eve_characters`;
CREATE TABLE IF NOT EXISTS `jos_eve_characters` (
  `characterID` int(10) unsigned NOT NULL default '0',
  `userID` int(10) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `race` varchar(20) NOT NULL default '',
  `gender` enum('Unknown','Male','Female') NOT NULL default 'Unknown',
  `bloodLine` varchar(20) NOT NULL default '',
  `corporationID` int(10) unsigned NOT NULL default '0',
  `balance` decimal(17,2) NOT NULL default '0.00',
  `startDateTime` datetime default NULL,
  `title` varchar(100) default NULL,
  `baseID` int(10) unsigned NOT NULL default '0',
  `logonDateTime` datetime default NULL,
  `logoffDateTime` datetime default NULL,
  `locationID` int(10) unsigned NOT NULL default '0',
  `shipTypeID` int(10) unsigned NOT NULL default '0',
  `roles` bigint(20) NOT NULL default '0',
  `grantableRoles` bigint(20) NOT NULL default '0',
  PRIMARY KEY (`characterID`),
  KEY `eve_characters_fk_corporationID` (`corporationID`),
  KEY `eve_characters_fk_userID` (`userID`)
) CHARSET=utf8;

DROP TABLE IF EXISTS `jos_eve_corporations`;
CREATE TABLE IF NOT EXISTS `jos_eve_corporations` (
  `corporationID` int(10) unsigned NOT NULL default '0',
  `corporationName` varchar(50) NOT NULL default '',
  `ticker` varchar(10) NOT NULL default '',
  `ceoID` int(10) unsigned NOT NULL default '0',
  `stationID` int(10) unsigned NOT NULL default '0',
  `description` text,
  `url` varchar(255) NOT NULL default '',
  `taxRate` decimal(5,2) NOT NULL default '0.00',
  `memberCount` smallint(5) unsigned NOT NULL default '0',
  `memberLimit` smallint(5) unsigned NOT NULL default '0',
  `shares` int(10) unsigned NOT NULL default '0',
  `allianceID` int(10) unsigned NOT NULL default '0',
  `standings` tinyint(4) NULL,
  `owner` tinyint(1) NOT NULL default '0',
  PRIMARY KEY (`corporationID`),
  KEY `eve_corporations_fk_allianceID` (`allianceID`)
) CHARSET=utf8;

DROP TABLE IF EXISTS `jos_eve_alliances`;
CREATE TABLE IF NOT EXISTS `jos_eve_alliances` (
  `allianceID` int(10) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `shortName` varchar(10) NOT NULL default '',
  `executorCorpID` int(10) unsigned NOT NULL default '0',
  `memberCount` smallint(5) unsigned NOT NULL default '0',
  `logo` varchar(5) NOT NULL default '',
  `standings` tinyint(4) unsigned NOT NULL default '0',
  `owner` tinyint(1) NOT NULL default '0',
  PRIMARY KEY (`allianceID`)
) CHARSET=utf8;


DELETE FROM `jos_components` WHERE `option` = 'com_eve';
INSERT INTO `jos_components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
('EVE', 'option=com_eve', 0, 0, 'option=com_eve&control=char', 'EVE', 'com_eve', 0, 'components/com_eve/assets/icon-eve-16.png', 0, '', 1);

SET @lastid = LAST_INSERT_ID();
INSERT INTO `jos_components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
('Characters', '', 0, @lastid, 'option=com_eve&control=char', 'Characters', 'com_eve', 0, 'components/com_eve/assets/icon-char-16.png', 0, '', 1),
('Corporations', '', 0, @lastid, 'option=com_eve&control=corp', 'Corporations', 'com_eve', 1, 'components/com_eve/assets/icon-corp-16.png', 0, '', 1),
('Alliances', '', 0, @lastid, 'option=com_eve&control=alliance', 'Alliances', 'com_eve', 2, 'components/com_eve/assets/icon-alliance-16.png', 0, '', 1),
('Account', '', 0, @lastid, 'option=com_eve&control=account', 'Account', 'com_eve', 3, 'components/com_eve/assets/icon-account-16.png', 0, '', 1);
