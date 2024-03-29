<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Market Orders
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

jimport( 'joomla.application.component.view');

abstract class EvemarketordersView extends JView
{
	public $params;
	public $state;
	public $items;
	public $pagination;

	function display($tpl = null) {
		$app = JFactory::getApplication();

		$state		= $this->get('State');
		$params		= $this->get('Params');
		$item		= $this->get('Item');
		$listState	= $this->get('State', 'list');
		$items		= $this->get('Items', 'list');
		$pagination	= $this->get('Pagination', 'list');
		$accountKeys = $this->get('AccountKeys', 'list');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		if (count($errors = $this->get('Errors', 'list'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->_setEntity($item, $params);

		$accountKeysOption = JHTML::_('select.option', '-1', JText::_('Com_Evemarketorders_Account_Key_0_Option'));
		array_unshift($accountKeys, $accountKeysOption);

		$this->assignRef('params', 		$params);
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('listState',	$listState);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('accountKeys',	$accountKeys);

		parent::display();
		$this->_setPathway();
	}

	protected function _setEntity($item, $params)
	{

	}

	public function rangeName($range)
	{
		switch ($range) {
			case -1:
				return JText::_('Com_Evemarketorders_Range_Station');
			case 0:
				return JText::_('Com_Evemarketorders_Range_Solar_System');
			case 32767:
				return JText::_('Com_Evemarketorders_Range_Region');
			default:
				return JText::sprintf('Com_Evemarketorders_Range_N_Jumps', $range);

		}
	}

	public function orderStateName($orderState)
	{
		switch ($orderState) {
			case 0:
				return JText::_('Com_Evemarketorders_Order_State_Active');
			case 1:
				return JText::_('Com_Evemarketorders_Order_State_Closed');
			case 2:
				return JText::_('Com_Evemarketorders_Order_State_Expired');
			case 3:
				return JText::_('Com_Evemarketorders_Order_State_Cancelled');
			case 4:
				return JText::_('Com_Evemarketorders_Order_State_Pending');
			case 5:
				return JText::_('Com_Evemarketorders_Order_State_Character_Deleted');
			default:
				return JText::_('Com_Evemarketorders_Order_State_Unknown');

		}
	}

	public function bidName($bid)
	{
		if ($bid) {
			return JText::_('Com_Evemarketorders_Bid_Buy');
		} else {
			return JText::_('Com_Evemarketorders_Bid_Sell');
		}
	}

	protected function _setPathway()
	{
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		if (!$menu || $menu->component == 'com_evemarketorders') {
			return;
		}

		$app = JFactory::getApplication();
		$pathway = $app->getPathway();

		$view = JArrayHelper::getValue($menu->query, 'view');
		switch ($view) {
			case null:
				$pathway->addItem($this->character->allianceName,
				EveRoute::_('alliance', $this->character));
			case 'alliance':
				$pathway->addItem($this->character->corporationName,
				EveRoute::_('corporation', $this->character, $this->character));
			case 'corporation':
			case 'user':
				$pathway->addItem($this->character->name,
				EveRoute::_('character', $this->character, $this->character, $this->character));
			case 'character':
				$pathway->addItem(JText::_('Market Orders'),
				EveRoute::_('charmarketorders', $this->character, $this->character, $this->character));
		}
	}

	function loadTemplate($tpl = null, $layout = null)
	{
		if (!is_null($layout)) {
			$previous = $this->setLayout($layout);
		}
		$result = parent::loadTemplate($tpl);
		if (!is_null($layout)) {
			$this->setLayout($previous);
		}
		return $result;
	}

	/**
	 * Sets an entire array of search paths for templates or resources.
	 *
	 * @access protected
	 * @param string $type The type of path to set, typically 'template'.
	 * @param string|array $path The new set of search paths.  If null or
	 * false, resets to the current directory only.
	 */
	function _setPath($type, $path)
	{
		global $option;

		// clear out the prior search dirs
		$this->_path[$type] = array();

		// always add the fallback directories as last resort
		switch (strtolower($type))
		{
			case 'template':
				$app = JFactory::getApplication();
				$option = preg_replace('/[^A-Z0-9_\.-]/i', '', $option);

				//common not overriden template sould be last
				$this->_addPath('template', $this->_basePath.DS.'views'.DS.'_common'.DS.'tmpl');
				// set the alternative template search dir
				$fallback = JPATH_BASE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS.$option.DS.'_common';
				$this->_addPath('template', $fallback);
				//specific not overriden template sould be 2nd
				$this->_addPath('template', $path);
				//specific template override sould be 1st
				$fallback = JPATH_BASE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS.$option.DS.$this->getName();
				$this->_addPath('template', $fallback);
				break;
			default:
				$this->_addPath($type, $path);
		}
	}

}
