<?php
/**
 * @version		$Id$
 * @author		Nigel Bazzeghin
 * @package		EVE Custom Registration
 * @subpackage	Core
 * @copyright	Copyright (C) 2009 Nigel Bazzeghin. All rights reserved.
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
 
// No direct access
  
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport('joomla.application.component.model');
 
/**
 * EVE Custom Registration Component Model
 *
 * @package    EVE Custom Registration
 * @subpackage Core
 */
class EVERegistersModelEVERegisters extends JModel
{
    /**
    * Gets the Owner CorpID from the database
    * @return string owner corpID
    */
    function getOwnerCorpID()
    {
        $db = JFactory::getDBO();
        
        $query = 'SELECT corporationID FROM #__eve_corporations WHERE owner = 1';
        $db->setQuery($query);
        $corpID = $db->loadResult();
        
        return $corpID;
        
    }
	/**
    * Gets the owner allianceID from the database
    * @return string owner allianceID
    */
    function getOwnerAllianceID()
    {
        $db = JFactory::getDBO();
        
        $query = 'SELECT allianceID  FROM #__eve_corporations WHERE owner = 1';
        $db->setQuery($query);
        $allianceID = $db->loadResult();
        
        return $allianceID;
        
    }
	
}
