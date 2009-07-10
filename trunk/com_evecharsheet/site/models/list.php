<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Character Sheet
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

class EvecharsheetModelList extends JModelList {
	
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_context = 'com_evecharsheet.list';
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string		An SQL query
	 */
	protected function _getListQuery()
	{
		$dbo = $this->getDBO();
		$q = new JQuery($dbo);
		$q->addTable('#__eve_characters', 'ch');
		$q->addJoin('#__eve_accounts', 'ac', 'ac.userID=ch.userID');
		$q->addJoin('#__eve_corporations', 'co', 'co.corporationID=ch.corporationID');
		$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
		$q->addJoin('#__users', 'us', 'ac.owner=us.id');
		
		$q->addQuery('ch.characterID', 'ch.name AS characterName');
		$q->addQuery('co.corporationID', 'co.corporationName');
		$q->addQuery('al.allianceID', 'al.name AS allianceName');
		$q->addQuery('ac.owner', 'us.name AS ownerName');
		$q->addOrder('characterName');

		$owner = $this->getState('filter.owner');
		$corporationID = $this->getState('filter.corporationID');
		if ($owner && is_numeric($owner)) {
			$q->addWhere('ac.owner = %s', intval($owner));
		} elseif ($owner) {
			$q->addWhere('us.name LIKE \'%s\'', $owner);
		} elseif ($corporationID && is_numeric($corporationID)) {
			$q->addWhere('ch.corporationID = %s', intval($corporationID));
		} elseif ($corporationID) {
			$q->addWhere('co.corporationName LIKE \'%s\'', $corporationID);
		} else {
			$this->setError(JText::_('Invalid filter'));
		}
		
		$q->prepare();
		return $q;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$context	A prefix for the store id.
	 * @return	string		A store id.
	 */
	protected function _getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.ordering');
		$id	.= ':'.$this->getState('list.direction');
		$id	.= ':'.$this->getState('list.owner');
		$id	.= ':'.$this->getState('list.corporationID');
		
		return md5($id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function _populateState() {
		// Initialize variables.
		$app		= &JFactory::getApplication('administrator');
		$params		= JComponentHelper::getParams('com_eve');
		$context	= $this->_context.'.';
		$layout		= JRequest::getCmd('layout');
		
		if ($layout == 'owner') {
			$owner = JRequest::getString('owner');
			if (strpos($owner, ':') !== false) {
				$owner = preg_replace('/:.*/', '', $owner); 
			}
			$this->setState('filter.owner', $owner);
		} elseif ($layout == 'corporation') {
			$corporationID = JRequest::getString('corporationID');
			if (strpos($corporationID, ':') !== false) {
				$corporationID = preg_replace('/:.*/', '', $corporationID); 
			}
			$this->setState('filter.corporationID', $corporationID);
		}

		// Load the list state.
		$this->setState('list.start', $app->getUserStateFromRequest($context.'list.start', 'limitstart', 0, 'int'));
		$this->setState('list.limit', $app->getUserStateFromRequest($context.'list.limit', 'limit', $app->getCfg('list_limit', 25), 'int'));
		$this->setState('list.ordering', $app->getUserStateFromRequest($context.'list.ordering', 'filter_order', 'co.corporationName', 'cmd'));
		$this->setState('list.direction', $app->getUserStateFromRequest($context.'list.direction', 'filter_order_Dir', 'ASC', 'word'));

		// Load the parameters.
		$this->setState('params', $params);
	}	
	
	function _setWhere(&$q) {
		$q->addJoin('#__eve_accounts', 'ac', 'ac.userID=ch.userID');
		$q->addJoin('#__eve_corporations', 'co', 'co.corporationID=ch.corporationID');
		$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
		
		$show_all = $this->get('show_all');
		if (!$show_all) {
			$q->addWhere('(co.owner OR al.owner)');
		}
		
		$owner = $this->get('owner');
		if ($owner) {
			$q->addWhere('ac.owner = %s', intval($owner));
		}
		
		$corporationID = $this->get('corporationID');
		if ($corporationID) {
			$q->addWhere('ch.corporationID = %s', intval($corporationID));
		}
	}
	

}

