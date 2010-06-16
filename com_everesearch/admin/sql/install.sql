CREATE TABLE IF NOT EXISTS `#__eve_research` (
  `characterID` int(11) NOT NULL,
  `agentID` int(11) NOT NULL,
  `skillTypeID` int(11) NOT NULL,
  `researchStartDate` datetime NOT NULL,
  `pointsPerDay` decimal(20,2) NOT NULL,
  `remainderPoints` decimal(20,2) NOT NULL,
  PRIMARY KEY  (`characterID`,`agentID`)
);

INSERT IGNORE INTO `#__eve_sections` ( `name` , `title` , `alias` , `entity` , `component` , `view` , `layout` , `ordering` , `published`, `access`, `roles` ) VALUES 
('charresearch', 'Research', 'research', 'character', 'research', 'character', '', '0', '1', '2', '0'),
('userresearch', 'Research', 'research', 'user', 'research', 'user', '', '0', '1', '0', '0');

INSERT IGNORE INTO `#__eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `paginationRowsetName`, `paginationAttrib`, `paginationParam`, `paginationPerPage`, `delay`, `params`) VALUES 
('char', 'Research', 'Character', 'Full', 'entries', 'refID', 'beforeRefID', 1000, 0, '');