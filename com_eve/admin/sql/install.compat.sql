-- 
-- Table structure for table `#__eve_accounts`
-- 

CREATE TABLE `#__eve_accounts` (
  `userID` int(10) unsigned NOT NULL default '0',
  `owner` int(10) unsigned NOT NULL default '0',
  `apiKey` varchar(255) NOT NULL default '',
  `apiStatus` enum('Unknown','Inactive','Invalid','Limited','Full') NOT NULL default 'Unknown',
  `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (`userID`),
  KEY `eve_accounts_fk_owner` (`owner`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `#__eve_alecache`
-- 

CREATE TABLE `#__eve_alecache` (
  `host` varchar(64) NOT NULL,
  `path` varchar(64) NOT NULL,
  `params` varchar(64) NOT NULL,
  `content` longtext NOT NULL,
  `cachedUntil` datetime default NULL,
  PRIMARY KEY  (`host`,`path`,`params`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `#__eve_alliances`
-- 

CREATE TABLE `#__eve_alliances` (
  `allianceID` int(10) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `shortName` varchar(10) NOT NULL default '',
  `executorCorpID` int(10) unsigned NOT NULL default '0',
  `memberCount` smallint(5) unsigned NOT NULL default '0',
  `logo` varchar(5) NOT NULL default '',
  `owner` tinyint(1) NOT NULL default '0',
  `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (`allianceID`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `#__eve_characters`
-- 

CREATE TABLE `#__eve_characters` (
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
);

-- --------------------------------------------------------

-- 
-- Table structure for table `#__eve_corporations`
-- 

CREATE TABLE `#__eve_corporations` (
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
  PRIMARY KEY  (`corporationID`),
  KEY `eve_corporations_fk_allianceID` (`allianceID`)
);
