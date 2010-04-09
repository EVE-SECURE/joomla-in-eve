CREATE TABLE IF NOT EXISTS `jos_eve_marketorders` (
  `entityID` int(11) NOT NULL,
  `orderID` int(11) NOT NULL,
  `charID` int(11) NOT NULL,
  `stationID` int(11) NOT NULL,
  `volEntered` int(11) NOT NULL,
  `volRemaining` int(11) NOT NULL,
  `minVolume` int(11) NOT NULL,
  `orderState` smallint(3) NOT NULL,
  `typeID` mediumint(5) NOT NULL,
  `range` mediumint(5) NOT NULL,
  `accountKey` mediumint(5) NOT NULL default '1000',
  `duration` mediumint(5) NOT NULL,
  `escrow` decimal(20,2) NOT NULL,
  `price` decimal(20,2) NOT NULL,
  `bid` smallint(1) NOT NULL,
  `issued` datetime NOT NULL, 
  PRIMARY KEY  (`entityID`,`orderID`,`issued`)
);


DELETE FROM `jos_components` WHERE `option`='com_evemarketorders';
INSERT INTO `jos_components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
('EVE Market Orders', 'option=com_evemarketorders', 0, 0, 'option=com_evemarketorders', 'EVE Market Orders', 'com_evemarketorders', 0, 'components/com_evemarketorders/assets/icon-16-market.png', 0, '', 1);

DELETE FROM `jos_eve_sections` WHERE `component`='marketorders';
INSERT INTO `jos_eve_sections` ( `name` , `title` , `alias` , `entity` , `component` , `view` , `layout` , `ordering` , `published`, `access` ) VALUES 
('charmarketorders', 'Market Orders', 'market-orders', 'character', 'marketorders', 'character', '', '0', '1', '2'),
('corpmarketorders', 'Market Orders', 'market-orders', 'corporation', 'marketorders', 'corporation', '', '0', '1', '2');

DELETE FROM `jos_eve_apicalls` WHERE `call`='MarketOrders';
INSERT INTO `jos_eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `paginationRowsetName`, `paginationAttrib`, `paginationParam`, `paginationPerPage`, `delay`, `params`) VALUES 
('char', 'MarketOrders', 'Character', 'Full', 'entries', 'refID', 'beforeRefID', 1000, 0, ''),
('corp', 'MarketOrders', 'Character', 'Full', 'entries', 'refID', 'beforeRefID', 1000, 0, '');
