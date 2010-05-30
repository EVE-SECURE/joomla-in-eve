-- 
-- Table structure for table `#__eve_section_character_access`
-- 

CREATE TABLE IF NOT EXISTS `#__eve_section_character_access` (
  `section` int(11) NOT NULL,
  `characterID` int(11) NOT NULL,
  `access` int(3) default NULL,
  PRIMARY KEY  (`section`,`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__eve_section_corporation_access` (
  `section` int(11) NOT NULL,
  `corporationID` int(11) NOT NULL,
  `access` int(3) default NULL,
  `roles` bigint(20) unsigned default NULL,
  PRIMARY KEY  (`section`,`corporationID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `jos_eve_sections` ADD `roles` BIGINT( 20 ) UNSIGNED NULL AFTER `access` ;


ALTER TABLE `#__eve_skillqueue` CHANGE `startTime` `startTime` DATETIME NOT NULL , CHANGE `endTime` `endTime` DATETIME NOT NULL;