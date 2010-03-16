DELETE FROM `jos_plugins` WHERE `element` IN ('eve', 'evecharsheet', 'evechartracking', 'evewalletjournal');

INSERT INTO `jos_plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES
('User - EVE', 'eve', 'user', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
('System - EVE', 'eve', 'system', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', 'igb_template=eve_igb\ntrustme=Add this site to your trusted sites to allow advanced features like autologin. <br /><br />More to come.<br /><br />Warning: this is test site, using alt char is recommended.\n'),
('Cron - EVE', 'eve', 'cron', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', '');

INSERT INTO `jos_plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES
('Eveapi - Core', 'eve', 'eveapi', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
('Eveapi - Character Sheet', 'evecharsheet', 'eveapi', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
('Eveapi - Character Tracking', 'evechartracking', 'eveapi', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
('Eveapi - Wallet Journal', 'evewalletjournal', 'eveapi', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', '');

INSERT INTO `jos_plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES
('Search - EVE', 'eve', 'search', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
('Search - Character Sheet', 'evecharsheet', 'search', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', '');

