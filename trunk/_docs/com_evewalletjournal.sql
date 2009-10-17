CREATE TABLE `jos_eve_walletjournal` (
`date` DATETIME NOT NULL ,
`refID` INT NOT NULL ,
`refTypeID` SMALLINT NOT NULL ,
`ownerName1` VARCHAR( 50 ) NOT NULL ,
`ownerID1` INT NOT NULL ,
`ownerName2` VARCHAR( 50 ) NOT NULL ,
`ownerID2` INT NOT NULL ,
`argName1` VARCHAR( 50 ) NOT NULL ,
`argID1` INT NOT NULL ,
`amount` DECIMAL( 20, 2 ) NOT NULL ,
`balance` DECIMAL( 20, 2 ) NOT NULL ,
`reason` VARCHAR( 255 ) NOT NULL
) ENGINE = MYISAM ;

INSERT INTO `jos_eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `paginationRowsetName`, `paginationAttrib`, `paginationParam`, `paginationPerPage`, `delay`, `params`) VALUES  
('char', 'WalletJournal', 'Character', 'Full', 'entries', 'refID', 'beforeRefID', 1000, 0, '');
