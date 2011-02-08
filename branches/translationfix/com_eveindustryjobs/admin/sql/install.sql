CREATE TABLE IF NOT EXISTS `#__eve_industryjobs` (
  `entityID` int(11) NOT NULL,
  `jobID` int(11) NOT NULL,
  `assemblyLineID` int(11) NOT NULL,
  `containerID` int(11) NOT NULL,
  `installedItemID` int(11) NOT NULL,
  `installedItemLocationID` int(11) NOT NULL,
  `installedItemQuantity` mediumint(5) NOT NULL,
  `installedItemProductivityLevel` mediumint(5) NOT NULL,
  `installedItemMaterialLevel` mediumint(5) NOT NULL,
  `installedItemLicensedProductionRunsRemaining` mediumint(5) NOT NULL,
  `outputLocationID` int(11) NOT NULL,
  `installerID` int(11) NOT NULL,
  `runs` mediumint(5) NOT NULL,
  `licensedProductionRuns` mediumint(5) NOT NULL,
  `installedInSolarSystemID` int(11) NOT NULL,
  `containerLocationID` int(11) NOT NULL,
  `materialMultiplier` FLOAT NOT NULL,
  `charMaterialMultiplier` FLOAT NOT NULL,
  `timeMultiplier` FLOAT NOT NULL,
  `charTimeMultiplier` FLOAT NOT NULL,
  `installedItemTypeID` int(11) NOT NULL,
  `outputTypeID` int(11) NOT NULL,
  `containerTypeID` int(11) NOT NULL,
  `installedItemCopy` smallint(1) NOT NULL,
  `completed` smallint(1) NOT NULL,
  `completedSuccessfully` smallint(1) NOT NULL,
  `installedItemFlag` mediumint(5) NOT NULL,
  `outputFlag` mediumint(5) NOT NULL,
  `activityID` mediumint(5) NOT NULL,
  `completedStatus` int(11) NOT NULL,
  `installTime` datetime NOT NULL,
  `beginProductionTime` datetime NOT NULL,
  `endProductionTime` datetime NOT NULL,
  `pauseProductionTime` datetime NOT NULL,
PRIMARY KEY  (`entityID`,`jobID`)
);

INSERT IGNORE INTO `#__eve_sections` ( `name` , `title` , `alias` , `entity` , `component` , `view` , `layout` , `ordering` , `published`, `access`, `roles` ) VALUES 
('charindustryjobs', 'Industry Jobs', 'industry-jobs', 'character', 'industryjobs', 'character', '', '0', '1', '100', '0'),
('userindustryjobs', 'Industry Jobs', 'industry-jobs', 'user', 'industryjobs', 'user', '', '0', '1', '1', '0'),
('corpindustryjobs', 'Industry Jobs', 'industry-jobs', 'corporation', 'industryjobs', 'corporation', '', '0', '1', '100', '3377699720527873');

INSERT IGNORE INTO `#__eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `delay`, `params`) VALUES 
('char', 'IndustryJobs', 'Character', 'Full', 0, ''),
('corp', 'IndustryJobs', 'Character', 'Full', 0, '');

