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

CREATE TABLE `#__eve_charcertificates` (
  `characterID` INT NOT NULL ,
  `certificateID` INT NOT NULL ,
PRIMARY KEY ( `characterID` , `certificateID` )
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `#__eve_charattributes` (
  `characterID` INT NOT NULL ,
  `attributeID` INT NOT NULL ,
  `value` INT NOT NULL ,
  `augmentatorValue` INT NOT NULL ,
  `augmentatorID` INT NULL ,
PRIMARY KEY ( `characterID` , `attributeID` )
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `#__eve_roles` (
`roleID` BIGINT NOT NULL ,
`roleName` VARCHAR (64) NOT NULL ,
PRIMARY KEY ( `roleID` )
) CHARACTER SET utf8 COLLATE utf8_general_ci;


CREATE TABLE `#__eve_charroles` (
`characterID` INT NOT NULL ,
`roleID` BIGINT NOT NULL ,
`location` SMALLINT NOT NULL COMMENT '0:-, 1:atHQ, 2:atBase, 3:atOther',
PRIMARY KEY ( `characterID` , `roleID` , `location` )
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `#__eve_chartitles` (
  `characterID` INT NOT NULL ,
  `titleID` INT NOT NULL ,
PRIMARY KEY ( `characterID` , `titleID` )
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `#__eve_corptitles` (
  `corporationID` INT NOT NULL ,
  `titleID` INT NOT NULL ,
  `titleName` VARCHAR (64) NOT NULL ,
PRIMARY KEY ( `corporationID` , `titleID` )
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `#__eve_charclone` (
  `characterID` INT NOT NULL ,
  `cloneID` INT NOT NULL ,
PRIMARY KEY ( `characterID` )
) CHARACTER SET utf8 COLLATE utf8_general_ci;


INSERT INTO `#__eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `pagination`, `delay`, `params`) VALUES 
('char', 'SkillQueue', 'Character', 'Limited', NULL, 0, '');

INSERT INTO #__eve_roles (`roleID`, `roleName`) VALUES
(1, 'roleDirector'),
-- (2, ''),
-- (4, ''),
-- (8, ''),
-- (16, ''),
-- (32, ''),
-- (64, ''),
(128, 'rolePersonnelManager'),
(256, 'roleAccountant'),
(512, 'roleSecurityOfficer'),
(1024, 'roleFactoryManager'),
(2048, 'roleStationManager'),
(4096, 'roleAuditor'),
(8192, 'roleHangarCanTake1'),
(16384, 'roleHangarCanTake2'),
(32768, 'roleHangarCanTake3'),
(65536, 'roleHangarCanTake4'),
(131072, 'roleHangarCanTake5'),
(262144, 'roleHangarCanTake6'),
(524288, 'roleHangarCanTake7'),
(1048576, 'roleHangarCanQuery1'),
(2097152, 'roleHangarCanQuery2'),
(4194304, 'roleHangarCanQuery3'),
(8388608, 'roleHangarCanQuery4'),
(16777216, 'roleHangarCanQuery5'),
(33554432, 'roleHangarCanQuery6'),
(67108864, 'roleHangarCanQuery7'),
(134217728, 'roleAccountCanTake1'),
(268435456, 'roleAccountCanTake2'),
(536870912, 'roleAccountCanTake3'),
(1073741824, 'roleAccountCanTake4'),
(2147483648, 'roleAccountCanTake5'),
(4294967296, 'roleAccountCanTake6'),
(8589934592, 'roleAccountCanTake7'),
(17179869184, 'roleAccountCanQuery1'),
(34359738368, 'roleAccountCanQuery2'),
(68719476736, 'roleAccountCanQuery3'),
(137438953472, 'roleAccountCanQuery4'),
(274877906944, 'roleAccountCanQuery5'),
(549755813888, 'roleAccountCanQuery6'),
(1099511627776, 'roleAccountCanQuery7'),
(2199023255552, 'roleEquipmentConfig'),
(4398046511104, 'roleContainerCanTake1'),
(8796093022208, 'roleContainerCanTake2'),
(17592186044416, 'roleContainerCanTake3'),
(35184372088832, 'roleContainerCanTake4'),
(70368744177664, 'roleContainerCanTake5'),
(140737488355328, 'roleContainerCanTake6'),
(281474976710656, 'roleContainerCanTake7'),
(562949953421312, 'roleCanRentOffice'),
(1125899906842624, 'roleCanRentFactorySlot'),
(2251799813685248, 'roleCanRentResearchSlot'),
(4503599627370496, 'roleJuniorAccountant'),
(9007199254740992, 'roleStarbaseConfig'),
(18014398509481984, 'roleTrader'),
(36028797018963968, 'roleChatManager'),
(72057594037927936, 'roleContractManager'),
(144115188075855872, 'roleInfrastructureTacticalOfficer'),
(288230376151711744, 'roleStarbaseCaretaker'),
(576460752303423488, 'roleFittingManager')

