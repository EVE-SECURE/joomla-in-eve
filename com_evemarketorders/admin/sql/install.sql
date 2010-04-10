CREATE TABLE IF NOT EXISTS `#__eve_marketorders` (
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

INSERT IGNORE INTO `#__eve_sections` ( `name` , `title` , `alias` , `entity` , `component` , `view` , `layout` , `ordering` , `published`, `access` ) VALUES 
('charmarketorders', 'Market Orders', 'market-orders', 'character', 'marketorders', 'character', '', '0', '1', '2'),
('corpmarketorders', 'Market Orders', 'market-orders', 'corporation', 'marketorders', 'corporation', '', '0', '1', '2');

INSERT INTO `#__eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `delay`, `params`) VALUES 
('char', 'MarketOrders', 'Character', 'Full', 0, ''),
('corp', 'MarketOrders', 'Character', 'Full', 0, '');

