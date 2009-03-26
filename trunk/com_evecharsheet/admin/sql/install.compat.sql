--
-- Table structure for table `#__eve_charskills`
--

CREATE TABLE `#__eve_charskills` (
  `characterID` int(11) NOT NULL,
  `typeID` int(11) NOT NULL,
  `skillpoints` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY  (`characterID`,`typeID`)
);
