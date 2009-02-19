<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Plugin EVE - EVE API
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
jimport('joomla.plugin.plugin');

/**
 * Joomla! in EVE Api core plugin
 *
 * @author		Pavol Kovalik  <kovalikp@gmail.com>
 * @package		Joomla
 * @subpackage	JFramework
 * @since 		1.5
 */
class plgEveapiCalls extends JPlugin {
	private $calls = array();
	private $models = array();
	
	function __construct($subject, $config = array()) {
		parent::__construct($subject, $config);
		$dbo = JFactory::getDBO();
		$q = new JQuery($dbo);
		$q->addTable('#__eve_apicalls');
		$q->addWhere('enabled = 1');
		$this->calls = $q->loadObjectList();
	}
	
	private function _getModel($component, $model) {
		$className = ucfirst($component).'Model'.ucfirst($model);
		if (isset($this->models[$className])) {
			return $this->models[$className];
		}
		if (!class_exists($className)) {
			$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_'.strtolower($component).DS.'models'.DS.strtolower($model).'.php';
			if (file_exists($path)) {
				require_once $path;
			}
		}
		if (!class_exists($className)) {
			return null;
		}
		$this->models[$className] = JModel::getInstance($model, $component.'Model');
		return $this->models[$className]; 
		
	}
	
	public function onFetchEveapi($apicall, $xml, $fromCache, $options = array()) {
		foreach ($this->calls as $call) {
			if ($call->apicall != $apicall) {
				continue;
			}
			$model = $this->_getModel($call->component, $call->model);
			if ($model && method_exists($model, $call->apicall)) {
				call_user_func(array($model, $call->apicall), $xml, $fromCache, $options);
			}
			
		}
	}
	

}
