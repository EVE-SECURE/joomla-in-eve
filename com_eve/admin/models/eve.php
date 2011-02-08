<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Core
 * @copyright	Copyright (C) 2009 Pavol Kovalik. All rights reserved.
 * @license		GNU/GPL, see http://www.gnu.org/licenses/gpl.html
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

class EveModelEve extends JModel {
	
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	public function getIcons() 
	{
		$user = JFactory::getUser();
		$result = array();
		$result[] = array('icon'=>'icon-48-character.png', 'view'=>'characters', 'caption'=>JText::_('COM_EVE_CHARACTERS'));
		$result[] = array('icon'=>'icon-48-corporation.png', 'view'=>'corporations', 'caption'=>JText::_('COM_EVE_CORPORATIONS'));
		$result[] = array('icon'=>'icon-48-alliance.png', 'view'=>'alliances', 'caption'=>JText::_('COM_EVE_ALLIANCES'));
		$result[] = array('icon'=>'icon-48-account.png', 'view'=>'accounts', 'caption'=>JText::_('COM_EVE_ACCOUNTS'));
		$result[] = array('icon'=>'icon-48-schedule.png', 'view'=>'schedule', 'caption'=>JText::_('COM_EVE_SCHEDULE'));
		/*if ($user->authorize('com_config', 'manage')) {
			$result[] = array('icon'=>'icon-48-diagnose.png', 'view'=>'diagnose', 'caption'=>JText::_('COM_EVE_DIAGNOSE'));
		}*/
		if ($user->authorize('com_config', 'manage')) {
			$result[] = array('icon'=>'icon-48-encryption.png', 'view'=>'access', 'caption'=>JText::_('COM_EVE_ACCESS_CONTROL'));
		}
		if ($user->authorize('com_config', 'manage') && !file_exists(JPATH_COMPONENT_ADMINISTRATOR.DS.'configs'.DS.'encryption.php')) {
			$result[] = array('icon'=>'icon-48-encryption.png', 'view'=>'encryption', 'caption'=>JText::_('COM_EVE_APIKEY_ENCRYPTION'));
		}
		return $result;
		
	}
	
	public function getOwnerCorporations()
	{
		$dbo = $this->getDBO();
		$q = new JQuery($dbo);
		$q->addTable('#__eve_corporations', 'co');
		$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
		$q->addJoin('#__eve_characters', 'ceo', 'ceo.characterID=co.ceoID');
		$q->addJoin('#__eve_accounts', 'ac', 'ceo.userID=ac.userID');
		$q->addQuery('co.*, al.name AS allianceName, al.shortName, ceo.name AS ceoName, ac.apiStatus');
		$q->addWhere('(co.owner = 1 OR al.owner = 1)');
		$list = $q->loadObjectList();
		return $list;
		
	}
	
	public function getCCPDbDumpTables()
	{
		$result = array(
			'agtAgents' => false,
			'agtAgentTypes' => false,
			'agtConfig' => false,
			'agtResearchAgents' => false,
			'chrAncestries' => false,
			'chrAttributes' => false,
			'chrBloodlines' => false,
			'chrFactions' => false,
			'chrRaces' => false,
			'crpActivities' => false,
			'crpNPCCorporationDivisions' => false,
			'crpNPCCorporationResearchFields' => false,
			'crpNPCCorporations' => false,
			'crpNPCCorporationTrades' => false,
			'crpNPCDivisions' => false,
			'crtCategories' => false,
			'crtCertificates' => false,
			'crtClasses' => false,
			'crtRecommendations' => false,
			'crtRelationships' => false,
			'dgmAttributeCategories' => false,
			'dgmAttributeTypes' => false,
			'dgmEffects' => false,
			'dgmTypeAttributes' => false,
			'dgmTypeEffects' => false,
			'eveGraphics' => false,
			'eveNames' => false,
			'eveUnits' => false,
			'invBlueprintTypes' => false,
			'invCategories' => false,
			'invContrabandTypes' => false,
			'invControlTowerResourcePurposes' => false,
			'invControlTowerResources' => false,
			'invFlags' => false,
			'invGroups' => false,
			'invMarketGroups' => false,
			'invMetaGroups' => false,
			'invMetaTypes' => false,
			'invTypeMaterials' => false,
			'invTypeReactions' => false,
			'invTypes' => false,
			'mapCelestialStatistics' => false,
			'mapConstellationJumps' => false,
			'mapConstellations' => false,
			'mapDenormalize' => false,
			'mapJumps' => false,
			'mapLandmarks' => false,
			'mapLocationScenes' => false,
			'mapLocationWormholeClasses' => false,
			'mapRegionJumps' => false,
			'mapRegions' => false,
			'mapSolarSystemJumps' => false,
			'mapSolarSystems' => false,
			'mapUniverse' => false,
			'ramActivities' => false,
			'ramAssemblyLines' => false,
			'ramAssemblyLineStations' => false,
			'ramAssemblyLineTypeDetailPerCategory' => false,
			'ramAssemblyLineTypeDetailPerGroup' => false,
			'ramAssemblyLineTypes' => false,
			'ramInstallationTypeContents' => false,
			'ramTypeRequirements' => false,
			'staOperations' => false,
			'staOperationServices' => false,
			'staServices' => false,
			'staStations' => false,
			'staStationTypes' => false,
			'trnTranslationColumns' => false,
			'trnTranslations' => false,
		);

		$eveparams = JComponentHelper::getParams('com_eve');
		$dbdump_database = $eveparams->get('dbdump_database');
		if (!$dbdump_database) {
			$app = JFactory::getApplication();
			$dbdump_database = $app->getCfg('db');
		}
		
		$dbo = $this->getDBO();
		$sql = 'SHOW TABLES FROM '.$dbo->nameQuote($dbdump_database);
		$dbo->setQuery($sql);
		$tables =  $dbo->loadResultArray();
		$tmp = array();
		foreach ($tables as $i => $table) {
			$tmp[strtolower($table)] = true;
		}
		foreach ($result as $table => $empty) {
			$result[$table] = isset($tmp[strtolower($table)]);
		}
		
		return $result;
	}
}