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

class EveModelCharacters extends JModelList {
	
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_context = 'com_eve.characters';
	
	protected function _getListQuery()
	{
		$list_query = $this->getState('list.query', 'c.*, co.corporationName, co.ticker, al.name AS allianceName, al.shortName, editor.name AS editor, u.name AS userName, us.apiStatus'); 
		
		$search = $this->getState('filter.search');
		$membersof = $this->getState('filter.membersof');
		$membersofpref = substr($membersof, 0, 1);
		$membersofnum = intval(substr($membersof, 1));
		// Create a new query object.
		$dbo = $this->getDBO();
		$q = new JQuery($dbo);
		$q->addTable('#__eve_characters', 'c');
		$q->addJoin('#__eve_corporations', 'co', 'c.corporationID=co.corporationID');
		$q->addJoin('#__eve_alliances', 'al', 'al.allianceID=co.allianceID');
		$q->addJoin('#__eve_accounts', 'us', 'us.userID=c.userID');
		$q->addJoin('#__users', 'u', 'us.owner=u.id');
		$q->addJoin('#__users', 'editor', 'c.checked_out=editor.id');
		
		$q->addQuery($list_query);
		if ($search) {
			$searchParts = explode(':', $search, 2);
			$sarchVal = $this->getState('filter.fullsearch') ? '%' : '';
			$sarchVal .= $q->getEscaped($search, true).'%';
			if (count($searchParts) == 2) {
				$q->addWhere('c.characterID = '.$q->quote($searchParts[0]));
			} elseif (is_numeric($search)) {
				$q->addWhere('c.characterID LIKE '.$q->Quote($sarchVal, false));
			} else {
				$q->addWhere('c.name LIKE '.$q->Quote($sarchVal, false));
			}
		}
		if ($membersofpref == '*') {
			$q->addWhere('(co.owner > 0 OR al.owner >  0)');
		} elseif ($membersofpref == 'c') {
			$q->addWhere('co.corporationID = %s', $membersofnum);
		} elseif ($membersofpref == 'a') {
			$q->addWhere('al.allianceID = %s', $membersofnum);
		}
		$q->addOrder($q->getEscaped($this->getState('list.ordering', 'c.name')), 
			$q->getEscaped($this->getState('list.direction', 'ASC')));
		return $q;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function _getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.membersof');
		
		return parent::_getStoreId($id);
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

		// Load the filter state.
		$this->setState('filter.fullsearch', 1); 
		$this->setState('filter.search', $app->getUserStateFromRequest($context.'filter.search', 'filter_search', ''));
		$this->setState('filter.membersof', $app->getUserStateFromRequest($context.'filter.membersof', 'filter_membersof', ''));
		
		// Load the parameters.
		$this->setState('params', $params);
		
		return parent::_populateState('c.name');
	}

}
