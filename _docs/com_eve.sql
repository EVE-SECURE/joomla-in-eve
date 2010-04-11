CREATE TABLE IF NOT EXISTS `jos_eve_alecache` (
  `host` varchar(64) NOT NULL,
  `path` varchar(64) NOT NULL,
  `params` varchar(64) NOT NULL,
  `content` longtext NOT NULL,
  `cachedUntil` datetime default NULL,
  PRIMARY KEY  (`host`,`path`,`params`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `jos_eve_accounts`;
CREATE TABLE IF NOT EXISTS `jos_eve_accounts` (
  `userID` int(10) unsigned NOT NULL default '0',
  `owner` int(10) unsigned NOT NULL default '0',
  `apiKey` varchar(64) NOT NULL default '',
  `apiStatus` enum('Unknown', 'Invalid','Limited','Full', 'Inactive') NOT NULL default 'Unknown',
  `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
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
  `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`characterID`),
  KEY `eve_characters_fk_corporationID` (`corporationID`),
  KEY `eve_characters_fk_userID` (`userID`),
  KEY `eve_characters_name` (`name`)
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
  `owner` tinyint(1) NOT NULL default '0',
  `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`corporationID`),
  KEY `eve_corporations_fk_allianceID` (`allianceID`),
  KEY `eve_corporations_corporationName` (`corporationName`)
) CHARSET=utf8;

DROP TABLE IF EXISTS `jos_eve_alliances`;
CREATE TABLE IF NOT EXISTS `jos_eve_alliances` (
  `allianceID` int(10) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `shortName` varchar(10) NOT NULL default '',
  `executorCorpID` int(10) unsigned NOT NULL default '0',
  `memberCount` smallint(5) unsigned NOT NULL default '0',
  `logo` varchar(5) NOT NULL default '',
  `owner` tinyint(1) NOT NULL default '0',
  `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`allianceID`),
  KEY `eve_alliances_name` (`name`)
) CHARSET=utf8;

DROP TABLE IF EXISTS `jos_eve_apicalls`;
CREATE TABLE IF NOT EXISTS `jos_eve_apicalls` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(15) NOT NULL,
  `call` varchar(25) NOT NULL,
  `authentication` enum('None','User','Character') NOT NULL default 'None',
  `authorization` enum('None','Limited','Full') NOT NULL default 'None',
  `paginationRowsetName` varchar(20) default NULL,
  `paginationAttrib` varchar(20) default NULL,
  `paginationParam` varchar(20) default NULL,
  `paginationPerPage` int(11) default NULL,
  `delay` int(11) NOT NULL default '0',
  `params` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `jos_eve_schedule`;
CREATE TABLE IF NOT EXISTS `jos_eve_schedule` (
  `id` int(11) NOT NULL auto_increment,
  `apicall` int(11) NOT NULL,
  `userID` int(11) default NULL,
  `characterID` int(11) default NULL,
  `next` datetime NOT NULL,
  `published` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `jos_eve_sections`;
CREATE TABLE `jos_eve_sections` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `alias` varchar(50) NOT NULL,
  `entity` varchar(50) NOT NULL,
  `component` varchar(50) NOT NULL,
  `view` varchar(50) NOT NULL DEFAULT '',
  `layout` varchar(50) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '0',
  `access` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `jos_eve_section_character_access`;
CREATE TABLE `jos_eve_section_character_access` (
  `section` int(11) NOT NULL,
  `characterID` int(11) NOT NULL,
  `access` int(3) default NULL,
  PRIMARY KEY  (`section`,`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DELETE FROM `jos_components` WHERE `option` = 'com_eve';
INSERT INTO `jos_components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
('EVE', 'option=com_eve', 0, 0, 'option=com_eve&view=eve', 'EVE', 'com_eve', 0, 'components/com_eve/assets/icon-16-eve.png', 0, '', 1);

SET @lastid = LAST_INSERT_ID();
INSERT INTO `jos_components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
('Overview', '', 0, @lastid, 'option=com_eve&view=eve', 'Overview', 'com_eve', 0, 'components/com_eve/assets/icon-16-eve.png', 0, '', 1),
('Characters', '', 0, @lastid, 'option=com_eve&view=characters', 'Characters', 'com_eve', 1, 'components/com_eve/assets/icon-16-character.png', 0, '', 1),
('Corporations', '', 0, @lastid, 'option=com_eve&view=corporations', 'Corporations', 'com_eve', 2, 'components/com_eve/assets/icon-16-corporation.png', 0, '', 1),
('Alliances', '', 0, @lastid, 'option=com_eve&view=alliances', 'Alliances', 'com_eve', 3, 'components/com_eve/assets/icon-16-alliance.png', 0, '', 1),
('Accounts', '', 0, @lastid, 'option=com_eve&view=accounts', 'Accounts', 'com_eve', 4, 'components/com_eve/assets/icon-16-account.png', 0, '', 1),
('Schedule', '', 0, @lastid, 'option=com_eve&view=schedule', 'Accounts', 'com_eve', 5, 'components/com_eve/assets/icon-16-schedule.png', 0, '', 1);


INSERT INTO `jos_eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `delay`, `params`) VALUES 
('account', 'Characters', 'User', 'Limited', 0, ''),
('char', 'CharacterSheet', 'Character', 'Limited', 0, ''),
('corp', 'CorporationSheet', 'Character', 'Limited', 0, '');

INSERT INTO `jos_eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `delay`, `params`) VALUES 
('eve', 'AllianceList', 'None', 'None', 0, '');
SET @lastid = LAST_INSERT_ID();
INSERT INTO `jos_eve_schedule` (`apicall`, `userID`, `characterID`, `next`, `published`) VALUES 
(@lastid, NULL, NULL, '0000-00-00 00:00:00', 1);

INSERT INTO `jos_eve_sections` ( `name` , `title` , `alias` , `entity` , `component` , `view` , `layout` , `ordering` , `published`, `access` ) VALUES 
('character', 'Character', '', 'character', '', 'character', '', '0', '0', '0'),
('corporation', 'Corporation', '', 'corporation', '', 'corporation', '', '0', '0', '0'),
('alliance', 'Alliance', '', 'alliance', '', 'alliance', '', '0', '0', '0');
