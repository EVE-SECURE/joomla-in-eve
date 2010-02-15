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

class EveModelAlliances extends JModelList {
	
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_context = 'com_eve.alliances';
	
	protected function _getListQuery()
	{
		$list_query = $this->getState('list.query', 'al.*, editor.name AS editor');
		
		$search = $this->getState('filter.search');
		$owner = $this->getState('filter.owner');
		// Create a new query object.
		$dbo = $this->getDBO();
		$q = new JQuery($dbo);
		$q->addTable('#__eve_alliances', 'al');
		$q->addQuery($list_query);
		$q->addJoin('#__users', 'editor', 'al.checked_out=editor.id');
		if ($search) {
			$searchParts = explode(':', $search, 2);
			$sarchVal = $this->getState('filter.fullsearch') ? '%' : '';
			$sarchVal .= $q->getEscaped($search, true).'%';
			if (count($searchParts) == 2) {
				$q->addWhere('al.allianceID = '.$q->quote($searchParts[0]));
			} elseif (is_numeric($search)) {
				$q->addWhere('al.allianceID LIKE '.$q->Quote($sarchVal, false));
			} else {
				$q->addWhere('al.name LIKE '.$q->Quote($sarchVal, false));
			}
		}
		if ($owner) {
			$q->addWhere('al.owner');
		}
		$q->addOrder($q->getEscaped($this->getState('list.ordering', 'al.name')), 
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
		$this->setState('filter.owner', $app->getUserStateFromRequest($context.'filter.owner', 'filter_owner', 0, 'int'));
		
		// Load the list state.
		$this->setState('params', $params);
		
		return parent::_populateState('al.name');
	}

}
