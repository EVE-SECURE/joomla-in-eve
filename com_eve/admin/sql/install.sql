-- 
-- Table structure for table `#__eve_accounts`
-- 

CREATE TABLE IF NOT EXISTS `#__eve_accounts` (
  `userID` int(10) unsigned NOT NULL default '0',
  `owner` int(10) unsigned NOT NULL default '0',
  `apiKey` varchar(255) NOT NULL default '',
  `apiStatus` enum('Unknown','Inactive','Invalid','Limited','Full') NOT NULL default 'Unknown',
  `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (`userID`),
  KEY `eve_accounts_fk_owner` (`owner`)
) DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `#__eve_alecache`
-- 

CREATE TABLE IF NOT EXISTS `#__eve_alecache` (
  `host` varchar(64) NOT NULL,
  `path` varchar(64) NOT NULL,
  `params` varchar(64) NOT NULL,
  `content` longtext NOT NULL,
  `cachedUntil` datetime default NULL,
  PRIMARY KEY  (`host`,`path`,`params`)
) DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `#__eve_alliances`
-- 

CREATE TABLE IF NOT EXISTS `#__eve_alliances` (
  `allianceID` int(10) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `shortName` varchar(10) NOT NULL default '',
  `executorCorpID` int(10) unsigned NOT NULL default '0',
  `memberCount` smallint(5) unsigned NOT NULL default '0',
  `logo` varchar(5) NOT NULL default '',
  `standings` tinyint(4) unsigned NOT NULL default '0',
  `owner` tinyint(1) NOT NULL default '0',
  `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (`allianceID`)
) DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `#__eve_characters`
-- 

CREATE TABLE IF NOT EXISTS `#__eve_characters` (
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
  PRIMARY KEY  (`characterID`),
  KEY `eve_characters_fk_corporationID` (`corporationID`),
  KEY `eve_characters_fk_userID` (`userID`)
) DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `#__eve_corporations`
-- 

CREATE TABLE IF NOT EXISTS `#__eve_corporations` (
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
  `standings` tinyint(4) default NULL,
  `owner` tinyint(1) NOT NULL default '0',
  `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (`corporationID`),
  KEY `eve_corporations_fk_allianceID` (`allianceID`)
) DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `#__eve_apicalls`
-- 

CREATE TABLE IF NOT EXISTS `#__eve_apicalls` (
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
  PRIMARY KEY  (`id`),
  UNIQUE KEY `type_call` (`type`,`call`)
) DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `#__eve_schedule`
-- 

CREATE TABLE IF NOT EXISTS `#__eve_schedule` (
  `id` int(11) NOT NULL auto_increment,
  `apicall` int(11) NOT NULL,
  `userID` int(11) default NULL,
  `characterID` int(11) default NULL,
  `next` datetime NOT NULL,
  `published` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;


-- 
-- Table structure for table `#__eve_sections`
-- 

CREATE TABLE IF NOT EXISTS `#__eve_sections` (
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
  `access` tinyint(1) NOT NULL default '0',
  `roles` bigint( 20 ) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) DEFAULT CHARSET=utf8;

-- 
-- Table structure for table `#__eve_section_character_access`
-- 

CREATE TABLE IF NOT EXISTS `#__eve_section_character_access` (
  `section` int(11) NOT NULL,
  `characterID` int(11) NOT NULL,
  `access` int(3) default NULL,
  PRIMARY KEY  (`section`,`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table structure for table `#__eve_section_character_access`
-- 

CREATE TABLE IF NOT EXISTS `#__eve_section_corporation_access` (
  `section` int(11) NOT NULL,
  `corporationID` int(11) NOT NULL,
  `access` int(3) default NULL,
  `roles` bigint(20) unsigned default NULL,
  PRIMARY KEY  (`section`,`corporationID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- 
-- Data for table `#__eve_apicalls`
-- 

INSERT IGNORE INTO `#__eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `delay`, `params`) VALUES 
('account', 'Characters', 'User', 'Limited', 0, ''),
('char', 'CharacterSheet', 'Character', 'Limited', 0, ''),
('corp', 'CorporationSheet', 'Character', 'Limited', 0, ''),
('eve', 'AllianceList', 'None', 'None', 0, '');

-- 
-- Data for table `#__eve_sections`
-- 

INSERT IGNORE INTO `#__eve_sections` ( `name` , `title` , `alias` , `entity` , `component` , `view` , `layout` , `ordering` , `published`, `access` ) VALUES 
('character', 'Character', '', 'character', '', 'character', '', '0', '0', '0'),
('corporation', 'Corporation', '', 'corporation', '', 'corporation', '', '0', '0', '0'),
('alliance', 'Alliance', '', 'alliance', '', 'alliance', '', '0', '0', '0');


