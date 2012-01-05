<?php

/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Community Builder - Character Sheet
 * @copyright	Copyright (C) 2009 Pavol Kovalik. All rights reserved.
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

jimport('joomla.application.component.view');

class EveViewCorporation extends JView
{
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();

		$params = $this->get('Params');
		$corporation = $this->get('Item');
		$members = $this->get('Members');
		$components = $this->get('Components');

		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		if (is_object($menu)
		&& JArrayHelper::getValue($menu->query, 'option') == 'com_eve'
		&& JArrayHelper::getValue($menu->query, 'view') == 'corporation'
		&& JArrayHelper::getValue($menu->query, 'corporationID') == $corporation->corporationID) {
			$menu_params = new JParameter($menu->params);
			if (!$menu_params->get('page_title')) {
				$params->set('page_title',	$corporation->corporationName.' ['.$corporation->ticker.']');
			}
		} else {
			$params->set('page_title',	$corporation->corporationName.' ['.$corporation->ticker.']');
		}
		$document->setTitle($params->get('page_title'));

		$this->assignRef('components', $components);
		$this->assignRef('corporation', $corporation);
		$this->assignRef('params', $params);
		$this->assign('members', $members);

		parent::display();
		$this->_setPathway();
	}

	protected function _setPathway()
	{
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();

		$view = JArrayHelper::getValue($menu->query, 'view');
		switch ($view) {
			case null:
				if ($this->corporation->allianceID) {
					$pathway->addItem($this->corporation->allianceName,
					EveRoute::_('alliance', $this->corporation));
				}
			case 'alliance':
				$pathway->addItem($this->corporation->corporationName,
				EveRoute::_('corporation', $this->corporation, $this->corporation));
		}
	}
}