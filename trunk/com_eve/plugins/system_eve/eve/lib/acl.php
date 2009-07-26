<?php
defined('_JEXEC') or die();

define('EVE_ROLE_DIRECTOR');

class EveACL extends JObject {
	var $dbo = null;
	
	function __construct($dbo) {
		$this->dbo = $dbo;
	}
	
	function getOwnerCoroprationIDs() {
		$q = EveFactory::getQuery($this->dbo);
		$q->addTable('#__eve_corporations', 'co');
		$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
		$q->addWhere('(co.owner OR al.owner)');
		$q->addQuery('co.corporationID');
		return $q->loadResultArray();
	}
}

$roles = array(
'corpRoleDirector' => '1',
'corpRolePersonnelManager' => '128',
'corpRoleAccountant' => '256',
'corpRoleSecurityOfficer' => '512',
'corpRoleFactoryManager' => '1024',
'corpRoleStationManager' => '2048',
'corpRoleAuditor' => '4096',
'corpRoleHangarCanTake1' => '8192',
'corpRoleHangarCanTake2' => '16384',
'corpRoleHangarCanTake3' => '32768',
'corpRoleHangarCanTake4' => '65536',
'corpRoleHangarCanTake5' => '131072',
'corpRoleHangarCanTake6' => '262144',
'corpRoleHangarCanTake7' => '524288',
'corpRoleHangarCanQuery1' => '1048576',
'corpRoleHangarCanQuery2' => '2097152',
'corpRoleHangarCanQuery3' => '4194304',
'corpRoleHangarCanQuery4' => '8388608',
'corpRoleHangarCanQuery5' => '16777216',
'corpRoleHangarCanQuery6' => '33554432',
'corpRoleHangarCanQuery7' => '67108864',
'corpRoleAccountCanTake1' => '134217728',
'corpRoleAccountCanTake2' => '268435456',
'corpRoleAccountCanTake3' => '536870912',
'corpRoleAccountCanTake4' => '1073741824',
'corpRoleAccountCanTake5' => '2147483648',
'corpRoleAccountCanTake6' => '4294967296',
'corpRoleAccountCanTake7' => '8589934592',
'corpRoleAccountCanQuery1' => '17179869184',
'corpRoleAccountCanQuery2' => '34359738368',
'corpRoleAccountCanQuery3' => '68719476736',
'corpRoleAccountCanQuery4' => '137438953472',
'corpRoleAccountCanQuery5' => '274877906944',
'corpRoleAccountCanQuery6' => '549755813888',
'corpRoleAccountCanQuery7' => '1099511627776',
'corpRoleEquipmentConfig' => '2199023255552',
'corpRoleContainerCanTake1' => '4398046511104',
'corpRoleContainerCanTake2' => '8796093022208',
'corpRoleContainerCanTake3' => '17592186044416',
'corpRoleContainerCanTake4' => '35184372088832',
'corpRoleContainerCanTake5' => '70368744177664',
'corpRoleContainerCanTake6' => '140737488355328',
'corpRoleContainerCanTake7' => '281474976710656',
'corpRoleCanRentOffice' => '562949953421312',
'corpRoleCanRentFactorySlot' => '1125899906842624',
'corpRoleCanRentResearchSlot' => '2251799813685248',
'corpRoleJuniorAccountant' => '4503599627370496',
'corpRoleStarbaseConfig' => '9007199254740992',
'corpRoleTrader' => '18014398509481984',
'corpRoleChatManager' => '36028797018963968',
'corpRoleContractManager' => '72057594037927936',
'corpRoleInfrastructureTacticalOfficer' => '144115188075855872',
'corpRoleStarbaseCaretaker' => '288230376151711744',);
