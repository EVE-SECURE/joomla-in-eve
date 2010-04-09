DELETE FROM `#__eve_sections` WHERE `component`='marketorders';
DELETE FROM `#__eve_apicalls` WHERE `call`='MarketOrders';
DROP TABLE IF EXISTS `#__eve_marketorders`;
