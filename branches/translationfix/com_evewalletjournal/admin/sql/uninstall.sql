DELETE FROM `#__eve_sections` WHERE `component`='walletjournal';
DELETE FROM `#__eve_apicalls` WHERE `call`='WalletJournal';
DROP TABLE IF EXISTS `#__eve_reftypes`;
DROP TABLE IF EXISTS `#__eve_walletjournal`;
