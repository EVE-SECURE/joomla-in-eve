CREATE TABLE IF NOT EXISTS `#__eve_walletjournal` (
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

CREATE TABLE IF NOT EXISTS `#__eve_reftypes` (
  `refTypeID` INT NOT NULL ,
  `refTypeName` VARCHAR( 100 ) NOT NULL ,
PRIMARY KEY ( `refTypeID` )
);

INSERT IGNORE INTO `#__eve_sections` ( `name` , `title` , `alias` , `entity` , `component` , `view` , `layout` , `ordering` , `published`, `access` ) VALUES 
('charwalletjournal', 'Wallet Journal', 'wallet-journal', 'character', 'walletjournal', 'character', '', '0', '1', '2'),
('corpwalletjournal', 'Wallet Journal', 'wallet-journal', 'corporation', 'walletjournal', 'corporation', '', '0', '1', '2');

INSERT INTO `#__eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `paginationRowsetName`, `paginationAttrib`, `paginationParam`, `paginationPerPage`, `delay`, `params`) VALUES 
('char', 'WalletJournal', 'Character', 'Full', 'entries', 'refID', 'beforeRefID', 1000, 0, ''),
('corp', 'WalletJournal', 'Character', 'Full', 'entries', 'refID', 'beforeRefID', 1000, 0, '{"accountKey":1000}'),
('corp', 'WalletJournal', 'Character', 'Full', 'entries', 'refID', 'beforeRefID', 1000, 0, '{"accountKey":1001}'),
('corp', 'WalletJournal', 'Character', 'Full', 'entries', 'refID', 'beforeRefID', 1000, 0, '{"accountKey":1002}'),
('corp', 'WalletJournal', 'Character', 'Full', 'entries', 'refID', 'beforeRefID', 1000, 0, '{"accountKey":1003}'),
('corp', 'WalletJournal', 'Character', 'Full', 'entries', 'refID', 'beforeRefID', 1000, 0, '{"accountKey":1004}'),
('corp', 'WalletJournal', 'Character', 'Full', 'entries', 'refID', 'beforeRefID', 1000, 0, '{"accountKey":1005}'),
('corp', 'WalletJournal', 'Character', 'Full', 'entries', 'refID', 'beforeRefID', 1000, 0, '{"accountKey":1006}');

INSERT IGNORE INTO `#__eve_reftypes` (`refTypeID`, `refTypeName`) VALUES 
(0, 'Undefined'),
(1, 'Player Trading'),
(2, 'Market Transaction'),
(3, 'GM Cash Transfer'),
(4, 'ATM Withdraw'),
(5, 'ATM Deposit'),
(6, 'Backward Compatible'),
(7, 'Mission Reward'),
(8, 'Clone Activation'),
(9, 'Inheritance'),
(10, 'Player Donation'),
(11, 'Corporation Payment'),
(12, 'Docking Fee'),
(13, 'Office Rental Fee'),
(14, 'Factory Slot Rental Fee'),
(15, 'Repair Bill'),
(16, 'Bounty'),
(17, 'Bounty Prize'),
(18, 'Agents_temporary'),
(19, 'Insurance'),
(20, 'Mission Expiration'),
(21, 'Mission Completion'),
(22, 'Shares'),
(23, 'Courier Mission Escrow'),
(24, 'Mission Cost'),
(25, 'Agent Miscellaneous'),
(26, 'LP Store'),
(27, 'Agent Location Services'),
(28, 'Agent Donation'),
(29, 'Agent Security Services'),
(30, 'Agent Mission Collateral Paid'),
(31, 'Agent Mission Collateral Refunded'),
(32, 'Agents_preward'),
(33, 'Agent Mission Reward'),
(34, 'Agent Mission Time Bonus Reward'),
(35, 'CSPA'),
(36, 'CSPAOfflineRefund'),
(37, 'Corporation Account Withdrawal'),
(38, 'Corporation Dividend Payment'),
(39, 'Corporation Registration Fee'),
(40, 'Corporation Logo Change Cost'),
(41, 'Release Of Impounded Property'),
(42, 'Market Escrow'),
(43, 'Agent Services Rendered'),
(44, 'Market Fine Paid'),
(45, 'Corporation Liquidation'),
(46, 'Brokers Fee'),
(47, 'Corporation Bulk Payment'),
(48, 'Alliance Registration Fee'),
(49, 'War Fee'),
(50, 'Alliance Maintainance Fee'),
(51, 'Contraband Fine'),
(52, 'Clone Transfer'),
(53, 'Acceleration Gate Fee'),
(54, 'Transaction Tax'),
(55, 'Jump Clone Installation Fee'),
(56, 'Manufacturing'),
(57, 'Researching Technology'),
(58, 'Researching Time Productivity'),
(59, 'Researching Material Productivity'),
(60, 'Copying'),
(61, 'Duplicating'),
(62, 'Reverse Engineering'),
(63, 'Contract Auction Bid'),
(64, 'Contract Auction Bid Refund'),
(65, 'Contract Collateral'),
(66, 'Contract Reward Refund'),
(67, 'Contract Auction Sold'),
(68, 'Contract Reward'),
(69, 'Contract Collateral Refund'),
(70, 'Contract Collateral Payout'),
(71, 'Contract Price'),
(72, 'Contract Brokers Fee'),
(73, 'Contract Sales Tax'),
(74, 'Contract Deposit'),
(75, 'Contract Deposit Sales Tax'),
(76, 'Secure EVE Time Code Exchange'),
(77, 'Contract Auction Bid (corp)'),
(78, 'Contract Collateral Deposited (corp)'),
(79, 'Contract Price Payment (corp)'),
(80, 'Contract Brokers Fee (corp)'),
(81, 'Contract Deposit (corp)'),
(82, 'Contract Deposit Refund'),
(83, 'Contract Reward Deposited'),
(84, 'Contract Reward Deposited (corp)'),
(85, 'Bounty Prizes'),
(86, 'Advertisement Listing Fee'),
(87, 'Medal Creation'),
(88, 'Medal Issued'),
(89, 'Betting'),
(90, 'DNA Modification Fee'),
(91, 'Sovereignity bill'),
(92, 'Bounty Prize Corporation Tax'),
(93, 'Agent Mission Reward Corporation Tax'),
(94, 'Agent Mission Time Bonus Reward Corporation Tax'),
(95, 'Upkeep adjustment fee');
