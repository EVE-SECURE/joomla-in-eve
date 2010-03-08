DELETE FROM `jos_components` WHERE `option`='com_evechartracking';
INSERT INTO `jos_components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
('EVE Character Tracking', 'option=com_evechartracking', 0, 0, 'option=com_evechartracking', 'EVE Character Tracking', 'com_evechartracking', 0, 'components/com_eve/assets/icon-char-16.png', 0, '', 1);

DELETE FROM `jos_eve_sections` WHERE `component`='chartracking';
INSERT INTO `jos_eve_sections` ( `name` , `title` , `alias` , `entity` , `component` , `view` , `layout` , `ordering` , `published`, `access` ) VALUES 
('chartracking', 'Member Tracking', 'member-tracking', 'corporation', 'chartracking', 'corporation', '', '0', '1', '2');

INSERT INTO `jos_eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `delay`, `params`) VALUES 
('corp', 'MemberTracking', 'Character', 'Full', 0, '');
