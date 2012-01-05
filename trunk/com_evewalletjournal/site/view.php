<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Wallet Journal
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

abstract class EvewalletjournalView extends JView
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
		$refTypes	= $this->get('RefTypes', 'list');

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

		$refTypeOption = JHTML::_('select.option', '-1', JText::_('Com_Evewalletjournal_REF_TYPE_0_OPTION'), 'refTypeID', 'refTypeName');
		array_unshift($refTypes, $refTypeOption);

		$this->assignRef('params', 		$params);
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('listState',	$listState);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('accountKeys',	$accountKeys);
		$this->assignRef('refTypes',	$refTypes);

		parent::display();
		$this->_setPathway();
	}

	protected function _setEntity($item, $params)
	{

	}

	protected function _setPathway()
	{
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		if (!$menu || $menu->component == 'com_evewalletjournal') {
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
				$pathway->addItem(JText::_('Wallet Journal'),
				EveRoute::_('charwalletjournal', $this->character, $this->character, $this->character));
		}
	}

	public function getArgument($item)
	{
		switch ($item->refTypeID) {
			case 1:
				//station
				return JHTML::_('evelink.station', array($item, 'arg', '1'));
				break;
			case 2:
				//transactionID
				break;
			case 19:
				//ship typeID
				return JHTML::_('evelink.ship', array($item, 'arg', '1'));
				break;
			case 35:
				//CSPA characterID
				break;
			case 85:
				//Bonties solarSystemID
				return JHTML::_('evelink.solarSystem', array($item, 'arg', '1'));
				break;
			default:
				return $item->argName1;
		}
	}

	public function getReason($item)
	{
		switch ($item->refTypeID) {
			case 85:
				//For each type of NPC killed, its typeID is followed by a colon and the quantity killed.
				//These pairs are seperated by commas, and if there are too many (more than about 60 characters' worth)
				//the list is ended with a literal ",..." to indicate that more have been left off the list.
				return $this->escape($item->reason);
				break;
			default:
				return $this->escape($item->reason);
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
