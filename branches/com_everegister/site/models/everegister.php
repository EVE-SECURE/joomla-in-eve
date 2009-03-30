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
class EVERegisterModelEVERegister extends EveModel
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
    /**
     * Processes API Form to see if account is allowed to register
     * @param $hash ($_POST)
     * @return boolean 
     */
    function chkAPI($hash) {
    	// TODO Get corp/alliance ID's that are allowed to register and check against chars from API data.
    	$APIUser = JArrayHelper::getValue($hash, 'APIUser', '', 'int');
		$APIKey = JArrayHelper::getValue($hash, 'APIKey', '', 'string');
		if (!preg_match('/[a-zA-Z0-9]{64}/', $APIKey)) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_("INVALID API KEY FORMAT"));
			return false;
		}
		
		// TODO Check ALE Cache vice hiting API server every time
		//$ale = $this->getAleEVEOnline();
		//$ale->setCredentials($APIUser, $APIKey);
		//$xml = $ale->account->Characters();
		
		return true;
    }
    function getXML($APIUser, $APIKey){
    	 
    	$ale = $this->getAleEVEOnline();
		$ale->setCredentials($APIUser, $APIKey);
		$xml = $ale->account->Characters();
		return $xml;
		
    }
	
}
