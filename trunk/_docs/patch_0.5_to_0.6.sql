-- 
-- Table structure for table `#__eve_section_character_access`
-- 

CREATE TABLE IF NOT EXISTS `#__eve_section_character_access` (
  `section` int(11) NOT NULL,
  `characterID` int(11) NOT NULL,
  `access` int(3) default NULL,
  PRIMARY KEY  (`section`,`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;