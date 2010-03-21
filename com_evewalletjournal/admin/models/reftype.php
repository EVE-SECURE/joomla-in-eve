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

class EvewalletjournalModelReftype extends EveModel {
	
	public function apiGetRefTypes() {
		$app = JFactory::getApplication();
		try {
			$ale = $this->getAleEVEOnline();
			$xml = $ale->eve->RefTypes();
			
			JPluginHelper::importPlugin('eveapi');
			$dispatcher =& JDispatcher::getInstance();
			
			$dispatcher->trigger('eveRefTypes', 
				array($xml, $ale->isFromCache(), array()));
			
			$app->enqueueMessage(JText::_('Com_Evewalletjournal_Ref_Types_Imported'));
		}
		catch (RuntimeException $e) {
			JError::raiseWarning($e->getCode(), $e->getMessage());
		}
		catch (Exception $e) {
			JError::raiseError($e->getCode(), $e->getMessage());
		}
	}
	
}
