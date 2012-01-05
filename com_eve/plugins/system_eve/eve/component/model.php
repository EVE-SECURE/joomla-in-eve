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
	//missing from 1.6 JModel
	protected $__state_set	= null;

	function __construct($config = array()) {
		$config['table_path'] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'tables';
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return	void
	 */
	protected function _populateState()
	{
	}

	/**
	 * Method to get model state variables
	 * 1.6 JModel override
	 *
	 * @param	string	Optional parameter name
	 * @param   mixed	Optional default value
	 * @return	object	The property where specified, the state object where omitted
	 */
	public function getState($property = null, $default = null)
	{
		if (!$this->__state_set) {
			// Private method to auto-populate the model state.
			$this->_populateState();

			// Set the model state set flat to true.
			$this->__state_set = true;
		}

		return $property === null ? $this->_state : $this->_state->get($property, $default);
	}

	/**
	 * Get instance of JQuery
	 *
	 * @return JQuery
	 */
	function getQuery() {
		$dbo = $this->getDBO();
		return EveFactory::getQuery($dbo);
	}

	/**
	 * Get possible options of enum type fields
	 *
	 * @param string $table
	 * @param string $field
	 * @return array
	 */
	function getEnumOptions($table, $field) {
		$dbo = $this->getDBO();
		$sql = "SHOW COLUMNS FROM `".$table."` LIKE '".$field."'";
		$dbo->Execute($sql);
		$desc = $dbo->loadRow();
		preg_match_all('/\'(.*?)\'/', $desc[1], $enum_array);
		$result = array();

		if(!empty($enum_array[1])) {
			foreach($enum_array[1] as $mkey => $value)  {
				$obj = new stdClass();
				$obj->value = $value;
				$result[] = $obj;
			}
		}
		return $result;
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
