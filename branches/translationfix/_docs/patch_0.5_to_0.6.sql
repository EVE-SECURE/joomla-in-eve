-- com_eve

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

ALTER IGNORE TABLE `#__eve_apicalls` ADD UNIQUE `type_call` (`type`, `call`);

ALTER TABLE `#__eve_sections` ADD `roles` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `access` ;

-- maybe not necessoary
ALTER IGNORE TABLE `#__eve_sections` ADD UNIQUE `name` (`name`);

-- com_evecharsheet

ALTER TABLE `#__eve_skillqueue` CHANGE `startTime` `startTime` DATETIME NOT NULL , CHANGE `endTime` `endTime` DATETIME NOT NULL;

