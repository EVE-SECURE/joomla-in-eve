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
	
	static protected $roles = array(
		'Director' => '1',
		'PersonnelManager' => '128',
		'Accountant' => '256',
		'SecurityOfficer' => '512',
		'FactoryManager' => '1024',
		'StationManager' => '2048',
		'Auditor' => '4096',
		'HangarCanTake1' => '8192',
		'HangarCanTake2' => '16384',
		'HangarCanTake3' => '32768',
		'HangarCanTake4' => '65536',
		'HangarCanTake5' => '131072',
		'HangarCanTake6' => '262144',
		'HangarCanTake7' => '524288',
		'HangarCanQuery1' => '1048576',
		'HangarCanQuery2' => '2097152',
		'HangarCanQuery3' => '4194304',
		'HangarCanQuery4' => '8388608',
		'HangarCanQuery5' => '16777216',
		'HangarCanQuery6' => '33554432',
		'HangarCanQuery7' => '67108864',
		'AccountCanTake1' => '134217728',
		'AccountCanTake2' => '268435456',
		'AccountCanTake3' => '536870912',
		'AccountCanTake4' => '1073741824',
		'AccountCanTake5' => '2147483648',
		'AccountCanTake6' => '4294967296',
		'AccountCanTake7' => '8589934592',
		'AccountCanQuery1' => '17179869184',
		'AccountCanQuery2' => '34359738368',
		'AccountCanQuery3' => '68719476736',
		'AccountCanQuery4' => '137438953472',
		'AccountCanQuery5' => '274877906944',
		'AccountCanQuery6' => '549755813888',
		'AccountCanQuery7' => '1099511627776',
		'EquipmentConfig' => '2199023255552',
		'ContainerCanTake1' => '4398046511104',
		'ContainerCanTake2' => '8796093022208',
		'ContainerCanTake3' => '17592186044416',
		'ContainerCanTake4' => '35184372088832',
		'ContainerCanTake5' => '70368744177664',
		'ContainerCanTake6' => '140737488355328',
		'ContainerCanTake7' => '281474976710656',
		'CanRentOffice' => '562949953421312',
		'CanRentFactorySlot' => '1125899906842624',
		'CanRentResearchSlot' => '2251799813685248',
		'JuniorAccountant' => '4503599627370496',
		'StarbaseConfig' => '9007199254740992',
		'Trader' => '18014398509481984',
		'ChatManager' => '36028797018963968',
		'ContractManager' => '72057594037927936',
		'InfrastructureTacticalOfficer' => '144115188075855872',
		'StarbaseCaretaker' => '288230376151711744',);
	
	static protected $generalRoles = array('Director', 'PersonnelManager', 'Accountant', 
		'SecurityOfficer', 'FactoryManager', 'StationManager', 'Auditor', 
		'EquipmentConfig',
		'CanRentOffice', 'CanRentFactorySlot', 'CanRentResearchSlot', 'JuniorAccountant', 'StarbaseConfig', 
		'Trader', 'ChatManager', 'ContractManager', 'InfrastructureTacticalOfficer', 'StarbaseCaretaker');
	
	public function getRoles($name = null)
	{
		$result = array();
		switch (strtolower($name)) {
			case 'general':
				foreach (self::$generalRoles as $key) {
					$result[$key] = self::$roles[$key]; 
				}
				break;
			case 'hangar':
				for ($i = 1; $i <= 7; $i += 1) {
					$result['HangarCanTake'.$i] = self::$roles['HangarCanTake'.$i];
					$result['HangarCanQuery'.$i] = self::$roles['HangarCanQuery'.$i];
				}
				break;
			case 'account':
				for ($i = 1; $i <= 7; $i += 1) {
					$result['AccountCanTake'.$i] = self::$roles['AccountCanTake'.$i];
					$result['AccountCanQuery'.$i] = self::$roles['AccountCanQuery'.$i];
				}
				break;
			case 'container':
				for ($i = 1; $i <= 7; $i += 1) {
					$result['ContainerCanTake'.$i] = self::$roles['ContainerCanTake'.$i];
				}
				break;
			default:
				$result = self::$roles;
				
		}
		return $result;
	}
	
	public function hasRole($role, $roles)
	{
		if (is_null($roles)) {
			$roles = 0;
		}
		if (isset(self::$roles[$role])) {
			$role = self::$roles[$role];
		}
		if (function_exists('gmp_init')) {
			$role = gmp_init($role);
			$roles = gmp_init($roles);
			$and = gmp_and($role, $roles);
			return (bool) gmp_strval($and);
		} else {
			return false;
		}
	}
	
	public function sumRoles($roles)
	{
		if (!is_array($roles)) {
			return '0';
		}
		if (function_exists('gmp_init')) {
			$sum = gmp_init('0');
			foreach ($roles as $role) {
				$role = gmp_init($role);
				$sum = gmp_add($sum, $role);
			}
			return gmp_strval($sum);
		} else {
			return '0';
		}
	}
	
}
