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

DELETE FROM `jos_components` WHERE `option`='com_evecharsheet';
INSERT INTO `jos_components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
('EVE Character Sheet', 'option=com_evecharsheet', 0, 0, 'option=com_evecharsheet', 'EVE Character Sheet', 'com_evecharsheet', 0, 'components/com_eve/assets/icon-char-16.png', 0, '', 1);
