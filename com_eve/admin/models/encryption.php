<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Core
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

jimport('joomla.application.component.model');

class EveModelEncryption extends JModel {
	
	public function getAlgorithms()
	{
		$result = array();
		
		$result[] = JHTML::_('select.option', '', '- '.JText::_('None').' -');
		if (function_exists('mcrypt_encrypt')) {
			$algorithms = mcrypt_list_algorithms();
			foreach ($algorithms as $cipher) {
				$result[] = JHTML::_('select.option', $cipher, JText::_(mcrypt_get_cipher_name($cipher)));
			}
		}
		return $result;
	}
	
	public function getModes() 
	{
		$result = array();
		
		$result[] = JHTML::_('select.option', '', '- '.JText::_('None').' -');
		if (function_exists('mcrypt_encrypt')) {
			$modes = mcrypt_list_modes();
			foreach ($modes as $mode) {
				$result[] = JHTML::_('select.option', $mode, JText::_($mode));
			}
		}
		return $result;
	}
	
	public function getConfigContent()
	{
		$cipher = $this->getState('cipher');
		$mode = $this->getState('mode');
		$showapikey = $this->getState('showapikey');
		$key = $this->getState('key');
		$iv = $this->getState('iv');
		
		$config = new JRegistry('config');
		$config_array = array();
		$config_array['cipher'] = $cipher;
		$config_array['mode'] = $mode;
		$config_array['showapikey'] = $showapikey;
		$config_array['key'] = $key;
		$config_array['iv'] = base64_encode($iv); 
		$config->loadArray($config_array);
		
		return $config->toString('PHP', 'config', array('class' => 'EveConfigEncryption'));
		
	}
	
	public function writeConfiguration()
	{
		$fname = JPATH_COMPONENT_ADMINISTRATOR.DS.'configs'.DS.'encryption.php';
		jimport('joomla.filesystem.path');
		/*
		if (!$ftp['enabled'] && JPath::isOwner($fname) && !JPath::setPermissions($fname, '0644')) {
			JError::raiseNotice('SOME_ERROR_CODE', 'Could not make configuration.php writable');
			return;
		}
		*/
		jimport('joomla.filesystem.file');
		$result = JFile::write($fname, $this->getConfigContent());
		if (!$result) {
			JError::raiseNotice('SOME_ERROR_CODE', JText::sprintf('Could not write to %s', $fname));
		}
		return $result;
	}
	
	public function setConfiguration($data)
	{
		$cipher = JArrayHelper::getValue($data, 'cipher', '');
		$mode = JArrayHelper::getValue($data, 'mode', '');
		$key = JArrayHelper::getValue($data, 'key', '');
		$show = JArrayHelper::getValue($data, 'displayapikey', 'int');
		if (strlen($cipher) > 0 && function_exists('mcrypt_encrypt')) {
			$algorithms = mcrypt_list_algorithms();
			if (!in_array($cipher, $algorithms)) {
				JError::raiseWarning(0, JText::_('Invalid Cipher'));
				return false;
			}
			$modes = mcrypt_list_modes();
			if (!in_array($mode, $modes)) {
				$this->setError(JText::_('Invalid Mode'));
				return false;
			}
			$iv_size = mcrypt_get_iv_size($cipher, $mode);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);			
		} else {
			if (strlen($cipher) > 0) {
				JError::raiseNotice(0, JText::_('Encryption not supported'));
			}
			$cipher = '';
			$mode = '';
			$key = '';
			$iv = '';
		}
		
		$this->setState('cipher', $cipher);
		$this->setState('mode', $mode);
		$this->setState('key', $key);
		$this->setState('iv', $iv);
		$this->setState('showapikey', $show);
		
		return true;
	}
	
	public function getPath()
	{
		return 'administrator'.DS.'components'.DS.'com_eve'.DS.'configs'.DS.'encryption.php';
	}
	
}