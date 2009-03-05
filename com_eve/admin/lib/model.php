<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Core
 * @copyright	Copyright (C) 2008 Pavol Kovalik. All rights reserved.
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

class EveModel extends JModel {
	function __construct($config = array()) {
		$config['table_path'] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'tables';
		parent::__construct($config);
	}
	
	function getQuery() {
		$dbo = $this->getDBO();
		return EveFactory::getQuery($dbo);
	}
	
	/**
	 * Return instance of AleEVEOnline class (api adapter)
	 *
	 * @return AleEVEOnline
	 */
	function getAleEVEOnline() {
		return EveFactory::getAleEVEOnline($this->getDBO());
	}
	
	function getInstance($table, $id = null) {
		$config = array('dbo'=>$this->getDBO());
		return EveFactory::getInstance($table, $id, $config);
	}
	
	function getOwnerCorporations() {
		$user = JFactory::getUser();
		if (!$user->id) {
			return array();
		}
		
		$q = $this->getQuery();
		$q->addTable('#__eve_characters', 'ch');
		$q->addJoin('#__eve_accounts', 'ac', 'ac.userID=ch.userID');
		$q->addWhere('ac.owner = %s', $user->id);
		$q->addQuery('DISTINCT corporationID');
		$corps = $q->loadResultArray();
		if (empty($corps)) {
			return array();
		}
		
		$corps = implode(', ', $corps);
		
		$q = $this->getQuery();
		$q->addTable('#__eve_corporations', 'co');
		$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
		$q->addWhere('co.owner OR al.owner');
		$q->addWhere('co.corporationID IN (%s)', $corps);
		$q->addQuery('co.*');
		$q->addOrder('co.corporationName');
		return $q->loadObjectList('corporationID');		
	}
	
	function getCharacters($owner) {
		$user = JFactory::getUser();
		if ($owner == 0) {
			$owner = $user->id;
		}
		
		$q = $this->getQuery();
		$q->addTable('#__eve_characters', 'ch');
		if (!$owner != $user->id) {
			$q = $this->getQuery();
			$q->addTable('#__eve_characters', 'ch');
			$q->addJoin('#__eve_accounts', 'ac', 'ac.userID=ch.userID');
			$q->addWhere('ac.owner = %s', $user->id);
			$q->addQuery('DISTINCT corporationID');
			$corps = $q->loadResultArray();
			if (empty($corps)) {
				return array();
			}
			$q->addJoin('#__eve_corporations', 'co', 'ch.corporationID=co.corporationID');
			$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
			$q->addWhere('ch.corporationID IN (%s)', $corps);
			$q->addWhere('(co.owner OR al.owner)');
		}
		$q->addQuery('ch.*');
		$q->addWhere('ch.owner=%s', intval($owner));
		return $q->loadObjectList('characterID');
		
	}
	
}
