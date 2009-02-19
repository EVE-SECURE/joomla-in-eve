DROP TABLE IF EXISTS `jos_eve_charskills`;
CREATE TABLE `jos_eve_charskills` (
  `characterID` INT NOT NULL ,
  `typeID` INT NOT NULL ,
  `skillpoints` INT NOT NULL ,
  `level` INT NOT NULL ,
PRIMARY KEY ( `characterID` , `typeID` )
) CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `jos_eve_apicalls` (`component` , `model` , `apicall` , `enabled`) VALUES 
('evecharsheet', 'sheet', 'charCharacterSheet', '1' );

DELETE FROM `jos_components` WHERE `option`='com_evecharsheet';
INSERT INTO `jos_components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
('EVE Character Sheet', 'option=com_evecharsheet', 0, 0, 'option=com_evecharsheet&view=dbcheck', 'EVE Character Sheet', 'com_evecharsheet', 0, 'components/com_eve/assets/icon-char-16.png', 0, '', 1);
