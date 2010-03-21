CREATE TABLE `#__eve_walletjournal` (
  `id` int(11) NOT NULL auto_increment,
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
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8 ;

INSERT INTO `#__eve_sections` ( `name` , `title` , `alias` , `entity` , `component` , `view` , `layout` , `ordering` , `published`, `access` ) VALUES 
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
