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
 
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the EVE Custom Registration Component
 *
 * @package    EVE Custom Registration
 * @subpackage Core
 */
class EVERegistersViewEVERegisters extends JView
{
    function display($tpl = null)
    {	
    	JToolBarHelper::title(JText::_('EVE Custom Registration Manager','generic.png'));
    	JToolBarHelper::preferences( 'com_everegister' );
    	
    	$model =& $this->getModel();
        
    	// TODO Change valid corpID and allianceID to allow multiple owner corps/alliances.
    	$ValidCoprID = $model->getOwnerCorpID();
        $ValidAllianceID = $model->getOwnerAllianceID();
        
        $this->assignRef( 'v_corpID', $ValidCoprID );
        $this->assignRef( 'v_allianceID', $ValidAllianceID );
 
        parent::display($tpl);
    }
}
