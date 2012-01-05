<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Core
 * @copyright	Copyright (C) 2011 Pavol Kovalik. All rights reserved.
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

class EveModelCalllist extends JModelList {

	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_context = 'com_eve.calllist';

	protected function _getListQuery()
	{
		$search = $this->getState('filter.search');
		$apicall = intval($this->getState('filter.apicall'));
		//$state = $this->getState('filter.state');
		// Create a new query object.
		$dbo = $this->getDBO();
		$q = new JQuery($dbo);
		$q->addTable('#__eve_apicalls', 'cl');
		$q->addQuery('cl.*');
		$q->addQuery('CONCAT(`type`, \'/\',`name`) AS typeName');
		if ($search) {
			$searchString = sprintf('cl.name LIKE %1$s OR cl.`type` LIKE %1$s',
			$q->Quote( '%'.$q->getEscaped( $search, true ).'%', false ));
			$q->addWhere($searchString);
		}
		/*if ($state == 'P') {
			$q->addWhere('sc.published');
		}
		if ($state == 'U') {
			$q->addWhere('sc.published = 0');
		}(*/
		$q->addOrder($q->getEscaped($this->getState('list.ordering', 'type')),
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
		//$id	.= ':'.$this->getState('filter.state');
		//$id	.= ':'.$this->getState('filter.apicall');

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
		//$this->setState('filter.state', $app->getUserStateFromRequest($context.'filter.state', 'filter_state', ''));
		//$this->setState('filter.apicall', $app->getUserStateFromRequest($context.'filter.apicall', 'filter_apicall', ''));

		// Load the list state.
		$this->setState('list.start', $app->getUserStateFromRequest($context.'list.start', 'limitstart', 0, 'int'));
		$this->setState('list.limit', $app->getUserStateFromRequest($context.'list.limit', 'limit', $app->getCfg('list_limit', 25), 'int'));
		$this->setState('list.ordering', $app->getUserStateFromRequest($context.'list.ordering', 'filter_order', 'typeName', 'cmd'));
		$this->setState('list.direction', $app->getUserStateFromRequest($context.'list.direction', 'filter_order_Dir', 'ASC', 'word'));

		// Load the parameters.
		$this->setState('params', $params);
	}

	/*public function setEnabled($cid, $enabled) {
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initialize variables
		$dbo = $this->getDBO();

		if (empty($cid)) {
			JError::raiseWarning( 500, JText::_( 'No items selected' ) );
			return false;
		}

		JArrayHelper::toInteger( $cid );
		$cids = implode( ',', $cid );

		$query = 'UPDATE #__eve_schedule'
		. ' SET published = ' . (int) $enabled
		. ' WHERE id IN ( '. $cids.'  )';
		$dbo->setQuery( $query );
		if (!$dbo->query()) {
			JError::raiseWarning( 500, $dbo->getError() );
			return false;
		}
		return true;
	}*/

	/*public function run() {
		JPluginHelper::importPlugin('cron', 'eve');
		$dispatcher	=& JDispatcher::getInstance();
		$results	= $dispatcher->trigger('onCronTick', array());
	}*/


}
