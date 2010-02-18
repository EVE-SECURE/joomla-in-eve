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

class EveTableSection extends JTable {
	//information sent by eve client
	/** @var int */
	var $id			= null;
	/** @var int */
	var $name		= null;
	/** @var int */
	var $title		= null;
	/** @var string */
	var $alias		= null;
	/** @var string */
	var $entity 	= null;
	/** @var string */
	var $component	= null;
	/** @var string */
	var $view		= null;
	/** @var string */
	var $layout		= null;
	/** @var int */
	var $ordering	= null;
	/** @var int */
	var $published	= null;
	/** @var int */
	var $access		= null;

	/**
	* @param database A database connector object
	*/
	function __construct( &$dbo )
	{
		parent::__construct( '#__eve_sections', 'id', $dbo );
	}

}
