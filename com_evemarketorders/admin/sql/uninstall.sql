DELETE FROM `#__eve_sections` WHERE `component`='marketorders';
DELETE FROM `#__eve_apicalls` WHERE `name`='MarketOrders';
DROP TABLE IF EXISTS `#__eve_marketorders`;
