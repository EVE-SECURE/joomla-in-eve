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
		$search = $this->getState('filter.search');
		// Create a new query object.
		$dbo = $this->getDBO();
		$q = new JQuery($dbo);
		$q->addTable('#__eve_characters', 'c');
		$q->addJoin('#__eve_corporations', 'co', 'c.corporationID=co.corporationID');
		$q->addJoin('#__eve_alliances', 'al', 'al.allianceID=co.allianceID');
		$q->addJoin('#__eve_accounts', 'us', 'us.userID=c.userID');
		$q->addJoin('#__users', 'u', 'us.owner=u.id');
		$q->addQuery('c.*');
		$q->addQuery('co.corporationName, co.ticker');
		$q->addQuery('al.name AS allianceName, al.shortName');
		$q->addQuery('u.name AS userName');
		if ($search) {
			$q->addWhere('c.name LIKE '.$q->Quote( '%'.$q->getEscaped( $search, true ).'%', false ));
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
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.ordering');
		$id	.= ':'.$this->getState('list.direction');
		$id	.= ':'.$this->getState('filter.search');

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

		// Load the filter state.
		$this->setState('filter.search', $app->getUserStateFromRequest($context.'filter.search', 'filter_search', ''));

		// Load the list state.
		$this->setState('list.start', $app->getUserStateFromRequest($context.'list.start', 'limitstart', 0, 'int'));
		$this->setState('list.limit', $app->getUserStateFromRequest($context.'list.limit', 'limit', $app->getCfg('list_limit', 25), 'int'));
		$this->setState('list.ordering', $app->getUserStateFromRequest($context.'list.ordering', 'filter_order', 'c.name', 'cmd'));
		$this->setState('list.direction', $app->getUserStateFromRequest($context.'list.direction', 'filter_order_Dir', 'ASC', 'word'));

		// Load the parameters.
		$this->setState('params', $params);
	}

}
