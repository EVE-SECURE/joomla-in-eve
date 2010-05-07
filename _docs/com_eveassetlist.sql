CREATE TABLE IF NOT EXISTS `jos_eve_assets` (
  `entityID` int(11) NOT NULL,
  `containerID` int(11) NOT NULL,
  `itemID` int(11) NOT NULL,
  `locationID` int(11) NOT NULL,
  `typeID` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `flag` mediumint(5) NOT NULL,
  `singleton` smallint(1) NOT NULL,
  PRIMARY KEY  (`entityID`,`itemID`)
);

DELETE FROM `jos_components` WHERE `option`='com_eveassetlist';
INSERT INTO `jos_components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
('EVE Asset List', 'option=com_eveassetlist', 0, 0, 'option=com_eveassetlist', 'EVE Asset List', 'com_eveassetlist', 0, 'components/com_eveassetlist/assets/icon-16-assets.png', 0, '', 1);

INSERT IGNORE INTO `jos_eve_sections` ( `name` , `title` , `alias` , `entity` , `component` , `view` , `layout` , `ordering` , `published`, `access` ) VALUES 
('charassetlist', 'Asset List', 'asset-list', 'character', 'assetlist', 'character', '', '0', '1', '2'),
('corpassetlist', 'Asset List', 'asset-list', 'corporation', 'assetlist', 'corporation', '', '0', '1', '2');

INSERT INTO `jos_eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `delay`, `params`) VALUES 
('char', 'AssetList', 'Character', 'Full', 0, ''),
('corp', 'AssetList', 'Character', 'Full', 0, '');
