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

/**
 * Basic tab extender. Any plugin that needs to display a tab in the user profile
 * needs to have such a class. Also, currently, even plugins that do not display tabs (e.g., auto-welcome plugin)
 * need to have such a class if they are to access plugin parameters (see $this->params statement).
 */

class getEvecharsheetTab extends cbTabHandler
{
	/**
	 * Layout name
	 *
	 * @var		string
	 * @access 	protected
	 */
	protected $_layout = 'default';
	protected $_name = 'character';
	protected $_option = 'com_evecharsheet';
	
	protected $_escape = 'htmlspecialchars';
	protected $_charset = 'UTF-8';
	
	protected $_path = array(
		'template' => array(),
		'helper' => array()
	);
	
	protected $_output = null;
	/**
	 * Layout extension
	 *
	 * @var		string
	 * @access 	protected
	 */
	protected $_layoutExt = 'php';
	
	//Construnctor
	public function __construct()
	{
		$this->cbTabHandler();
		
		$this->_basePath = JPATH_BASE.DS.'components'.DS.$this->_option;
		
		$this->_setPath('template', $this->_basePath.DS.'views'.DS.$this->_name.DS.'tmpl');
	}
	
	/**
	* Generates the HTML to display the user profile tab
	* @param object tab reflecting the tab database entry
	* @param object mosUser reflecting the user being displayed
	* @param int 1 for front-end, 2 for back-end
	* @returns mixed : either string HTML for tab content, or false if ErrorMSG generated


	*/
	public function getDisplayTab($tab, $user, $ui)
	{
		JComponentHelper::isEnabled('com_eve', true);
		JComponentHelper::isEnabled('com_evecharsheet', true);
		jimport('joomla.application.component.model');
		JModel::addIncludePath(JPATH_SITE.DS.'components'.DS.'com_evecharsheet'.DS.'models');
		$this->model = JModel::getInstance('Character', 'EvecharsheetModel');
		if (!$this->model) {
			//echo JText::_('com_evecharsheet missing');
			return false;
		}
		$characters = $this->getCharacters($user);
		//return 'hello';
		
		$result = '';
		switch (count($characters)) {
			case 0:
				break;
			case 1:
				$character = reset($characters);
				$result .= $this->displayCharacter($character);
				break;
			default:
				jimport('joomla.html.pane');
				$tabs	=& JPane::getInstance('tabs');
				$result .= $tabs->startPane("evecharsheet-pane");
				foreach ($characters as $character) {
					$result .= $tabs->startPanel( $character->name, 'character' . $character->characterID );
					$result .= $this->displayCharacter($character);
					$result .= $tabs->endPanel();
				}
				$result .= $tabs->endPane();
				break;
		}
		return $result;
	}
	
	private function show($section)
	{
		return intval($this->params->get('show'.$section, 0));
	}
	
	private function getCharacters($user)
	{
		$owner = $user->user_id;
		$dbo = JFactory::getDBO();
		$q = new JQuery($dbo);
		$q->addTable('#__eve_characters', 'ch');
		$q->addJoin('#__eve_accounts', 'ac', 'ac.userID=ch.userID');
		$q->addJoin('#__eve_corporations', 'co', 'co.corporationID=ch.corporationID');
		$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
		$q->addJoin('#__users', 'us', 'ac.owner=us.id');
		$q->addJoin('#__eve_charclone', 'cc', 'cc.characterID=ch.characterID');
		$q->addJoin('invTypes', 'it', 'cc.cloneID=it.typeID');
		$q->addJoin('dgmTypeAttributes', 'dta', 'dta.typeID=it.typeID AND dta.attributeID=419'); //FIXME: magical consant
		
		$q->addQuery('ch.*');
		$q->addQuery('co.corporationID', 'co.corporationName', 'co.ticker');
		$q->addQuery('al.allianceID', 'al.name AS allianceName', 'al.shortName');
		$q->addQuery('ac.owner', 'us.name AS ownerName');
		$q->addQuery('cloneID', 'it.typeName AS cloneName', 'dta.valueInt AS cloneSkillPoints');
		$q->addOrder('name');
		$q->addWhere('ac.owner = %s', intval($owner));
		if (!$this->params->get('listallcharacters', 0)) {
			$corps = EveHelper::getOwnerCoroprationIDs($dbo);
			if (!$corps) {
				return array();
			} else {
				$q->addWhere('ch.corporationID IN (%s)', implode(', ', $corps));
			}
		}
		return $q->loadObjectList();
		
		$pane = JPane::getInstance();
	}
	
	/**
	* Adds to the search path for templates and resources.
	*
	* @access protected
	* @param string|array $path The directory or stream to search.
	*/
	function _addPath($type, $path)
	{
		// just force to array
		settype($path, 'array');

		// loop through the path directories
		foreach ($path as $dir)
		{
			// no surrounding spaces allowed!
			$dir = trim($dir);

			// add trailing separators as needed
			if (substr($dir, -1) != DIRECTORY_SEPARATOR) {
				// directory
				$dir .= DIRECTORY_SEPARATOR;
			}

			// add to the top of the search dirs
			array_unshift($this->_path[$type], $dir);
		}
	}

	/**
	* Sets an entire array of search paths for templates or resources.
	*
	* @access protected
	* @param string $type The type of path to set, typically 'template'.
	* @param string|array $path The new set of search paths.  If null or
	* false, resets to the current directory only.
	*/
	protected function _setPath($type, $path)
	{
		$app = JFactory::getApplication();

		// clear out the prior search dirs
		$this->_path[$type] = array();

		// actually add the user-specified directories
		$this->_addPath($type, $path);

		// always add the fallback directories as last resort
		switch (strtolower($type))
		{
			case 'template':
			{
				// set the alternative template search dir
				if (isset($app))
				{
					$option = preg_replace('/[^A-Z0-9_\.-]/i', '', $this->_option);
					$fallback = JPATH_BASE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS.$option.DS.$this->_name;
					$this->_addPath('template', $fallback);
				}
			}	break;
		}
	}
	
	/**
	 * Create the filename for a resource
	 *
	 * @access private
	 * @param string 	$type  The resource type to create the filename for
	 * @param array 	$parts An associative array of filename information
	 * @return string The filename
	 * @since 1.5
	 */
	function _createFileName($type, $parts = array())
	{
		$filename = '';

		switch($type)
		{
			case 'template' :
				$filename = strtolower($parts['name']).'.'.$this->_layoutExt;
				break;

			default :
				$filename = strtolower($parts['name']).'.php';
				break;
		}
		return $filename;
	}
	
	 /**
     * Sets the _escape() callback.
     *
     * @param mixed $spec The callback for _escape() to use.
     */
    function setEscape($spec)
    {
        $this->_escape = $spec;
    }
	
	/**
     * Escapes a value for output in a view script.
     *
     * If escaping mechanism is one of htmlspecialchars or htmlentities, uses
     * {@link $_encoding} setting.
     *
     * @param  mixed $var The output to escape.
     * @return mixed The escaped value.
     */
    function escape($var)
    {
        if (in_array($this->_escape, array('htmlspecialchars', 'htmlentities'))) {
            return call_user_func($this->_escape, $var, ENT_COMPAT, $this->_charset);
        }

        return call_user_func($this->_escape, $var);
    }
    
	/**
	 * Load a template file -- first look in the templates folder for an override
	 *
	 * @access	public
	 * @param string $tpl The name of the template source file ...
	 * automatically searches the template paths and compiles as needed.
	 * @return string The output of the the template script.
	 */
	function loadTemplate( $tpl = null)
	{
		// clear prior output
		$this->_output = null;

		//create the template file name based on the layout
		$file = isset($tpl) ? $this->_layout.'_'.$tpl : $this->_layout;
		// clean the file name
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
		$tpl  = preg_replace('/[^A-Z0-9_\.-]/i', '', $tpl);

		// load the template script
		jimport('joomla.filesystem.path');
		$filetofind	= $this->_createFileName('template', array('name' => $file));
		$this->_template = JPath::find($this->_path['template'], $filetofind);

		if ($this->_template != false)
		{
			// unset so as not to introduce into template scope
			unset($tpl);
			unset($file);

			// never allow a 'this' property
			if (isset($this->this)) {
				unset($this->this);
			}

			// start capturing output into a buffer
			ob_start();
			// include the requested template filename in the local scope
			// (this will execute the view logic).
			include $this->_template;

			// done with the requested template; get the buffer and
			// clear it.
			$this->_output = ob_get_contents();
			ob_end_clean();

			return $this->_output;
		}
		else {
			return JError::raiseError( 500, 'Layout "' . $file . '" not found' );
		}
	}

	
	private function displayCharacter($character)
	{
		$this->character = $character;
		$this->model->setCharacterID($character->characterID);
		$this->groups = $this->model->getSkillGroups();
		$this->queue = $this->model->getQueue();
		$this->categories = $this->model->getCertificateCategories();
		$this->attributes = $this->model->getAttributes();
		$this->roles = $this->model->getRoles();
		$this->roleLocations = $this->model->getRoleLocations();
		$this->titles = $this->model->getTitles();
		
		return $this->loadTemplate();
	}
} 
