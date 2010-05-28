<?php
defined('_JEXEC') or die();

class EveACL extends JObject {
	protected $_sections = array();
	protected $_ownedCharacters;
	protected $ownerCorporationIDs = null;
	protected $userCorporationIDs = null;
	
	const CHARACTER_IN_OWNER_CORPORATION = 10;
	const CHARACTER_OWNED_BY_USER = 100;
	const CHARACTER_SECTION_DISABLED = -1;
	
	public function authorize($section, $entityID = null)
	{
		//TODO: character_section_access
		$acl = JFactory::getACL();
		$user = JFactory::getUser();
		$result = false;
		if (!is_object($section)) {
			$section = $this->_getSectionByName($section);
		} 
		
		if (!$section) {
			return false;
		}
		$access = $section->access; 
		if ($section->entity == 'character') {
			return $this->authorizeCharacter($section, $entityID);
		}
		//todo owner coporations (and alliances?)
		if ($access > $user->get('aid', 0)) {
			return false;
		}
		
		return true;
	}
	
	protected function authorizeCharacter($section, $characterID = null)
	{
		$dbo = JFactory::getDBO();
		$query = EveFactory::getQuery($dbo);
		$query->addTable('#__eve_section_character_access');
		$query->addWhere('characterID = ' . (int) $characterID);
		$query->addWhere('section = ' . (int) $section->id);
		$query->addQuery('access');
		$result = $query->loadResult();

		//null (or false) means we should use default access
		if (!is_numeric($result)) {
			$result = $section->access;
		}
		
		if ($result == self::CHARACTER_SECTION_DISABLED) {
			return false;
		}
		
		//get user's characters and check if the character is among them
		$ids = $this->getUserCharacterIDs();
		if (isset($ids[$characterID])) {
			return true;
		}
		/* if ($result == self::CHARACTER_OWNED_BY_USER) {
			$ids = $this->getUserCharacterIDs();
			return isset($ids[$characterID]);
		}*/
		
		//get user's corporations and check if the character's corp is among them 
		if ($result == self::CHARACTER_IN_OWNER_CORPORATION) {
			$character = EveFactory::getInstance('character', $characterID);
			$ids = $this->getUserCorporationIDs();
			return isset($ids[$character->corporationID]); 
		}
		
		$user = JFactory::getUser();
		return $result <= $user->get('aid', 0);
		
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param JQuery $query
	 * @param string|EveTableSection $section Section object or name or table prefix
	 * @param string $table|EveTableCharacter Character object  or table prefix
	 */
	public function setCharacterQuery($query, $section, $character)
	{
		if (is_string($section) && substr($section, -1, 1) == '.') {
			$coalesced = 'COALESCE(#__eve_section_character_access.access, '.$section.'access)'; 
			$sectionID =  $section.'id';
		} else {
			if (!is_object($section)) {
				$section = $this->_getSectionByName($section);
			}
			if (!$section) {
				throw new RuntimeException('Invalid section name', 0);
			}
			$coalesced = 'COALESCE(#__eve_section_character_access.access, '.$section->access.')'; 
			$sectionID = $section->id;
		}
		
		if (is_string($character) && substr($character, -1, 1) == '.') {
			$corporationID = $character.'corporationID';
			$characterID = $character.'characterID';
		} else if (is_object($character)) {
			$corporationID = $character->corporationID;
			$characterID = $character->characterID;
		} else {
		}
		
		
		$query->addJoin('#__eve_section_character_access', '#__eve_section_character_access', ' #__eve_section_character_access.characterID = '.$characterID.
			' AND #__eve_section_character_access.section = '.$sectionID);
		
		$sql  = '(';
		$user = JFactory::getUser();
		$sql .= ('('.$coalesced.' NOT IN ('.self::CHARACTER_IN_OWNER_CORPORATION.','.self::CHARACTER_OWNED_BY_USER.','.self::CHARACTER_SECTION_DISABLED.')'.
			' AND '.$coalesced. ' <= '. $user->get('aid', 0).')');
		
		$ids = $this->getUserCharacterIDs();
		if (!empty($ids)) {
			$sql .= ' OR ';
			$sql .= ('('.$characterID.' IN ('. implode(',', $ids).'))');
			/*$sql .= ('('.$coalesced.' = '.self::CHARACTER_OWNED_BY_USER.
				' AND '.$table.'characterID IN ('. implode(',', $ids).'))'); */
		}
		
		$ids = $this->getUserCorporationIDs();
		if (!empty($ids)) {
			$sql .= ' OR ';
			$sql .= ('('.$coalesced.' = '.self::CHARACTER_IN_OWNER_CORPORATION.
				' AND '.$corporationID.' IN ('. implode(',', $ids).'))');
		}
		$sql .= ')';
		
		$query->addWhere($sql);
		$query->addWhere('('.$coalesced.' <> '. self::CHARACTER_SECTION_DISABLED.')');
	}
	
	protected function _getSectionByName($name)
	{
		if (!isset($this->_sections[$name])) {
			$dbo = JFactory::getDBO();
			$query = EveFactory::getQuery($dbo);
			$query->addTable('#__eve_sections');
			$query->addWhere("name = '%s'", $name);
			$section = $query->loadObject();
			if ($section) {
				$this->_sections[$name] = $section;
			} else {
				$this->_sections[$name] = false;
			}
		}
		return $this->_sections[$name];
	}
	
	protected function _loadUserEntityIDs()
	{
		$this->_userCharacters = array();
		$this->_userCorporations = array();
		$this->_userAccounts = array();
		$user = JFactory::getUser();
		$id = intval($user->id);
		if ($id) {
			$dbo = JFactory::getDBO();
			$query = EveFactory::getQuery($dbo);
			$query->addTable('#__eve_characters', 'c');
			$query->addJoin('#__eve_accounts', 'a', 'c.userID=a.userID');
			$query->addWhere('a.owner=%s', $id);
			$query->addQuery('characterID, corporationID, c.userID');
			$list = $query->loadObjectList();
			foreach ($list as $item) {
				$this->_userCharacters[$item->characterID] = $item->characterID;
				$this->_userCorporations[$item->corporationID] = $item->corporationID;
				$this->_userAccounts[$item->userID] = $item->userID;
			}
		}
		
	}
	
	public function getUserCharacterIDs()
	{
		if (!isset($this->_userCharacters)) {
			$this->_loadUserEntityIDs();
		}
		return $this->_userCharacters;
	}
	
	public function getUserCorporationIDs()
	{
		if (!isset($this->_userCorporations)) {
			$this->_loadUserEntityIDs();
		}
		return $this->_userCorporations;
	}
	
	public function getUserAccountIDs()
	{
		if (!isset($this->_userAccounts)) {
			$this->_loadUserEntityIDs();
		}
		return $this->_userAccounts;
	}
	
	public function getOwnerCorporationIDs() 
	{
		if (is_null($this->ownerCorporationIDs)) {
			$dbo = JFactory::getDBO();
			$q = EveFactory::getQuery($dbo);
			$q->addTable('#__eve_corporations', 'co');
			$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
			$q->addWhere('(co.owner OR al.owner)');
			$q->addQuery('co.corporationID');
			$this->ownerCorporationIDs = $q->loadResultArray();
		}
		return $this->ownerCorporationIDs;
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
