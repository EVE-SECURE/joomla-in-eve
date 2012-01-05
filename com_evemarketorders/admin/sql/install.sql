CREATE TABLE IF NOT EXISTS `#__eve_marketorders` (
  `entityID` int(11) NOT NULL,
  `orderID` bigint(20) NOT NULL,
  `charID` int(11) NOT NULL,
  `stationID` int(11) NOT NULL,
  `volEntered` int(11) NOT NULL,
  `volRemaining` int(11) NOT NULL,
  `minVolume` int(11) NOT NULL,
  `orderState` smallint(3) NOT NULL,
  `typeID` int(11) NOT NULL,
  `range` smallint(5) NOT NULL,
  `accountKey` smallint(5) NOT NULL default '1000',
  `duration` smallint(5) NOT NULL,
  `escrow` decimal(20,2) NOT NULL,
  `price` decimal(20,2) NOT NULL,
  `bid` tinyint(1) NOT NULL,
  `issued` datetime NOT NULL, 
  PRIMARY KEY  (`entityID`,`orderID`)
);

INSERT IGNORE INTO `#__eve_sections` ( `name` , `title` , `alias` , `entity` , `component` , `view` , `layout` , `ordering` , `published`, `access`, `roles` ) VALUES 
('charmarketorders', 'Market Orders', 'market-orders', 'character', 'marketorders', 'character', '', '0', '1', '100', '0'),
('usermarketorders', 'Market Orders', 'market-orders', 'user', 'marketorders', 'user', '', '0', '1', '1', '0'),
('corpmarketorders', 'Market Orders', 'market-orders', 'corporation', 'marketorders', 'corporation', '', '0', '1', '100', '22517998136852737');

INSERT IGNORE INTO `#__eve_apicalls` (`type`, `name`, `authentication`, `authorization`, `delay`, `params`) VALUES 
('char', 'MarketOrders', 'Character', 'Full', 0, ''),
('corp', 'MarketOrders', 'Character', 'Full', 0, '');

