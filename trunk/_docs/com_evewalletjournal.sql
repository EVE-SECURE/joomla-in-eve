CREATE TABLE IF NOT EXISTS `jos_eve_walletjournal` (
  `entityID` int(11) NOT NULL,
  `accountKey` mediumint(9) NOT NULL default '1000',
  `date` datetime NOT NULL,
  `refID` bigint(20) NOT NULL,
  `refTypeID` smallint(6) NOT NULL,
  `ownerName1` varchar(50) NOT NULL,
  `ownerID1` int(11) NOT NULL,
  `ownerName2` varchar(50) NOT NULL,
  `ownerID2` int(11) NOT NULL,
  `argName1` varchar(50) NOT NULL,
  `argID1` int(11) NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `balance` decimal(20,2) NOT NULL,
  `reason` varchar(255) NOT NULL,
  PRIMARY KEY  (`entityID`,`accountKey`,`refID`)
);

CREATE TABLE IF NOT EXISTS `jos_eve_reftypes` (
  `refTypeID` INT NOT NULL ,
  `refTypeName` VARCHAR( 100 ) NOT NULL ,
PRIMARY KEY ( `refTypeID` )
);

DELETE FROM `jos_components` WHERE `option`='com_evewalletjournal';
INSERT INTO `jos_components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
('EVE Wallet Journal', 'option=com_evewalletjournal', 0, 0, 'option=com_evewalletjournal', 'EVE Wallet Journal', 'com_evewalletjournal', 0, 'components/com_evewalletjournal/assets/icon-16-wallet.png', 0, '', 1);

INSERT INTO `jos_eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `paginationRowsetName`, `paginationAttrib`, `paginationParam`, `paginationPerPage`, `delay`, `params`) VALUES  
('char', 'WalletJournal', 'Character', 'Full', 'entries', 'refID', 'beforeRefID', 1000, 0, '');
