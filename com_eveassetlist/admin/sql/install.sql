CREATE TABLE IF NOT EXISTS `#__eve_assets` (
  `entityID` int(11) NOT NULL,
  `containerID` int(11) NOT NULL,
  `itemID` bigint(20) NOT NULL,
  `locationID` int(11) NOT NULL,
  `typeID` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `flag` smallint(5) NOT NULL,
  `singleton` tinyint(1) NOT NULL,
  PRIMARY KEY  (`entityID`,`itemID`)
);

INSERT IGNORE INTO `#__eve_sections` ( `name` , `title` , `alias` , `entity` , `component` , `view` , `layout` , `ordering` , `published`, `access`, `roles` ) VALUES 
('charassetlist', 'Asset List', 'asset-list', 'character', 'assetlist', 'character', '', '0', '1', '100', '0'),
('userassetlist', 'Asset List', 'asset-list', 'user', 'assetlist', 'user', '', '0', '1', '1', '0'),
('corpassetlist', 'Asset List', 'asset-list', 'corporation', 'assetlist', 'corporation', '', '0', '1', '100', '558552041120257');

INSERT IGNORE INTO `#__eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `delay`, `params`) VALUES 
('char', 'AssetList', 'Character', 'Full', 0, ''),
('corp', 'AssetList', 'Character', 'Full', 0, '');

