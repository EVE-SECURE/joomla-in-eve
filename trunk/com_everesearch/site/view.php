<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Research
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

abstract class EveresearchView extends JView
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
		$summary	= $this->get('Summary', 'list');
		$pagination	= $this->get('Pagination', 'list');

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

		$this->assignRef('params', 		$params);
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('summary',		$summary);
		$this->assignRef('listState',	$listState);
		$this->assignRef('pagination',	$pagination);

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
		if (!$menu || $menu->component == 'com_everesearch') {
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
				$pathway->addItem(JText::_('Research'),
				EveRoute::_('charresearch', $this->character, $this->character, $this->character));
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
