--
-- Table structure for table `#__eve_charskills`
--

CREATE TABLE `#__eve_charskills` (
  `characterID` int(11) NOT NULL,
  `typeID` int(11) NOT NULL,
  `skillpoints` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY  (`characterID`,`typeID`)
) DEFAULT CHARSET=utf8;

--
-- Table structure for table `#__eve_skillqueue`
--

CREATE TABLE `#__eve_skillqueue` (
  `characterID` int(11) NOT NULL,
  `queuePosition` smallint(5) NOT NULL,
  `typeID` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `startSP` int(11) NOT NULL,
  `endSP` int(11) NOT NULL,
  `startTime` date NOT NULL, 
  `endTime` date NOT NULL, 
  PRIMARY KEY  (`characterID`,`typeID`, `level`)
) DEFAULT CHARSET=utf8;
