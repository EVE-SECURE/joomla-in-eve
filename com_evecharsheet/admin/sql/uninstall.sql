DELETE FROM `#__eve_sections` WHERE `component`='charsheet';
DELETE FROM `#__eve_apicalls` WHERE `name`='SkillQueue' OR `name`='Titles';
DROP TABLE IF EXISTS `#__eve_charskills`;
DROP TABLE IF EXISTS `#__eve_skillqueue`;
DROP TABLE IF EXISTS `#__eve_charcertificates`;
DROP TABLE IF EXISTS `#__eve_charattributes`;
DROP TABLE IF EXISTS `#__eve_roles`;
DROP TABLE IF EXISTS `#__eve_charroles`;
DROP TABLE IF EXISTS `#__eve_chartitles`;
DROP TABLE IF EXISTS `#__eve_corptitles`;
DROP TABLE IF EXISTS `#__eve_charclone`;
