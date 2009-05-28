DELETE FROM `jos_components` WHERE `option`='com_evechartracking';

INSERT INTO `jos_components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
('EVE Character Tracking', 'option=com_evechartracking', 0, 0, 'option=com_evechartracking', 'EVE Character Tracking', 'com_evechartracking', 0, 'components/com_eve/assets/icon-char-16.png', 0, '', 1);

INSERT INTO `jos_eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `pagination`, `delay`, `params`) VALUES 
('corp', 'MemberTracking', 'Character', 'Full', NULL, 0, '');
