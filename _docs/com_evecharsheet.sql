DROP TABLE IF EXISTS `jos_eve_charskills`;
CREATE TABLE `jos_eve_charskills` (
  `characterID` INT NOT NULL ,
  `typeID` INT NOT NULL ,
  `skillpoints` INT NOT NULL ,
  `level` INT NOT NULL ,
PRIMARY KEY ( `characterID` , `typeID` )
) CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `jos_eve_skillqueue`;
CREATE TABLE `jos_eve_skillqueue` (
  `characterID` INT NOT NULL ,
  `queuePosition` INT NOT NULL ,
  `typeID` INT NOT NULL ,
  `level` INT NOT NULL ,
  `startSP` INT NOT NULL ,
  `endSP` INT NOT NULL ,
  `startTime` datetime NOT NULL ,
  `endTime` datetime NOT NULL ,
PRIMARY KEY ( `characterID` , `queuePosition` )
) CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `jos_eve_charcertificates`;
CREATE TABLE `jos_eve_charcertificates` (
  `characterID` INT NOT NULL ,
  `certificateID` INT NOT NULL ,
PRIMARY KEY ( `characterID` , `certificateID` )
) CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `jos_eve_charattributes`;
CREATE TABLE `jos_eve_charattributes` (
  `characterID` INT NOT NULL ,
  `attributeID` INT NOT NULL ,
  `value` INT NOT NULL ,
  `augmentatorValue` INT NOT NULL ,
  `augmentatorID` INT NULL ,
PRIMARY KEY ( `characterID` , `attributeID` )
) CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `jos_eve_roles`;
CREATE TABLE `jos_eve_roles` (
`roleID` BIGINT NOT NULL ,
`roleName` VARCHAR (64) NOT NULL ,
PRIMARY KEY ( `roleID` )
) CHARACTER SET utf8 COLLATE utf8_general_ci;


DROP TABLE IF EXISTS `jos_eve_charroles`;
CREATE TABLE `jos_eve_charroles` (
`characterID` INT NOT NULL ,
`roleID` BIGINT NOT NULL ,
`location` SMALLINT NOT NULL COMMENT '0:-, 1:atHQ, 2:atBase, 3:atOther',
PRIMARY KEY ( `characterID` , `roleID` , `location` )
) CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `jos_eve_chartitles`;
CREATE TABLE `jos_eve_chartitles` (
  `characterID` INT NOT NULL ,
  `titleID` INT NOT NULL ,
PRIMARY KEY ( `characterID` , `titleID` )
) CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `jos_eve_corptitles`;
CREATE TABLE `jos_eve_corptitles` (
  `corporationID` INT NOT NULL ,
  `titleID` INT NOT NULL ,
  `titleName` VARCHAR (64) NOT NULL ,
PRIMARY KEY ( `corporationID` , `titleID` )
) CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `jos_eve_charclone`;
CREATE TABLE `jos_eve_charclone` (
  `characterID` INT NOT NULL ,
  `cloneID` INT NOT NULL ,
PRIMARY KEY ( `characterID` )
) CHARACTER SET utf8 COLLATE utf8_general_ci;

DELETE FROM `jos_components` WHERE `option`='com_evecharsheet';
INSERT INTO `jos_components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
('EVE Character Sheet', 'option=com_evecharsheet', 0, 0, 'option=com_evecharsheet', 'EVE Character Sheet', 'com_evecharsheet', 0, 'components/com_eve/assets/icon-char-16.png', 0, '', 1);

INSERT INTO `jos_eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `pagination`, `delay`, `params`) VALUES 
('char', 'SkillQueue', 'Character', 'Limited', NULL, 0, ''),
('corp', 'Titles', 'Character', 'Full', NULL, 0, '');

INSERT INTO jos_eve_roles (`roleID`, `roleName`) VALUES
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

