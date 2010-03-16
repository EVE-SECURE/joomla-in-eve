<?php
defined('_JEXEC') or die();

class EveACL extends JObject {
	protected $_section;
	protected $_ownedCharacters;
	
	
	public function setSection($section)
	{
		$this->_section = $section;
	}
	
	public function authorize($section = null)
	{
		$acl = JFactory::getACL();
		$user = JFactory::getUser();
		$result = false;
		if (is_null($section)) {
			$section = $this->getSection();
		} 
		
		if ($section) {
			$access = $section->access; 
			if ($section->entity == 'character') {
				//always allow access to user's characters
				$id = JRequest::getInt('characterID');
				$ids = $this->getOwnedCharacterIDs();
				if (isset($ids[$id])) {
					return true;
				}
			}
			//todo owner coporations (and alliances?)
			if ($access > $user->get('aid', 0)) {
				return false;
			}
		}
		
		return true;
	}
	
	public function getSection()
	{
		global $option;
		if (!isset($this->_section)) {
			$component = substr($option, 7);
			$view = JRequest::getCmd('view');
			$dbo = JFactory::getDBO();
			$query = EveFactory::getQuery($dbo);
			$query->addTable('#__eve_sections');
			$query->addWhere("component = '%s' AND view = '%s'", $component, $view);
			//TODO: check layout?
			$section = $query->loadObject();
			if ($section) {
				$this->_section = $section;
			} else {
				$this->_section = false;
			}
		}
		return $this->_section;
	}
	
	public function getOwnedCharacterIDs()
	{
		if (!isset($this->_ownedCharacters)) {
			$user = JFactory::getUser();
			$id = intval($user->id);
			$this->_ownedCharacters = array();
			if ($id) {
				$dbo = JFactory::getDBO();
				$query = EveFactory::getQuery($dbo);
				$query->addTable('#__eve_characters', 'c');
				$query->addJoin('#__eve_accounts', 'a', 'c.userID=a.userID');
				$query->addWhere('a.owner=%s', $id);
				$query->addQuery('characterID');
				$tmp = $query->loadResultArray();
				foreach ($tmp as $characterID) {
					$this->_ownedCharacters[$characterID] = $characterID;
				}
			}
		}
		return $this->_ownedCharacters;
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
