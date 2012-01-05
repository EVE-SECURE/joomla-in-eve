<?php
defined('_JEXEC') or die('Restricted Access');
require_once(dirname(__FILE__).DS.'helper.php');

//$numposts = $params->get('numposts');

$characters = eveMyCharsHelper::getCharacters();

if (empty($characters)) {
	return;
}
/*
 $ccbtitle = $module->title;
 $dispdate = $params->get('dispdate');
 $dispuser = $params->get('dispuser');
 $themeltpost = $params->get('theme');
 if( $themeltpost == 'mix') {
 $clr = array('blue','red','gray','green');
 $themeltpost = $clr[rand(0,3)];
 }
 */
require(JModuleHelper::getLayoutPath('mod_evemychars'));