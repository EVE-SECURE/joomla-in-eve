DELETE FROM `#__eve_sections` WHERE `component`='assetlist';
DELETE FROM `#__eve_apicalls` WHERE `name`='AssetList';
DROP TABLE IF EXISTS `#__eve_assets`;
