<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.controller' );

class EveController extends JController {
	
	/**
	 * Method to get a singleton controller instance.
	 *
	 * @param	string		$name		The prefix for the controller.
	 * @param	array		$config		An array of optional constructor options.
	 * @return	mixed		JController derivative class or JException on error.
	 * @since	1.6
	 */
	public static function &getInstance($prefix, $config = array())
	{
		static $instance;

		if (!empty($instance)) {
			return $instance;
		}

		// Get the environment configuration.
		$basePath	= array_key_exists('base_path', $config) ? $config['base_path'] : JPATH_COMPONENT;
		$protocol	= JRequest::getWord('protocol');
		$command	= JRequest::getCmd('task', 'display');

		// Check for a controller.task command.
		if (strpos($command, '.') !== false)
		{
			// Explode the controller.task command.
			list($type, $task) = explode('.', $command);

			// Define the controller filename and path.
			$file	= self::_createFileName('controller', array('name' => $type, 'protocol' => $protocol));
			$path	= $basePath.DS.'controllers'.DS.$file;

			// Reset the task without the contoller context.
			JRequest::setVar('task', $task);
		}
		else
		{
			// Base controller.
			$type	= null;
			$task	= $command;

			// Define the controller filename and path.
			$file	= self::_createFileName('controller', array('name' => 'controller', 'protocol' => $protocol));
			$path	= $basePath.DS.$file;
		}

		// Get the controller class name.
		$class = ucfirst($prefix).'Controller'.ucfirst($type);

		// Include the class if not present.
		if (!class_exists($class))
		{
			// If the controller file path exists, include it.
			if (file_exists($path)) {
				require_once $path;
			} else {
				$error = new JException(JText::sprintf('INVALID CONTROLLER', $type), 1056, E_ERROR, $type, true);
				return $error;
			}
		}

		// Instantiate the class.
		if (class_exists($class)) {
			$instance = new $class($config);
		} else {
			$error = new JException(JText::sprintf('INVALID CONTROLLER CLASS', $class), 1057, E_ERROR, $class, true);
			return $error;
		}

		return $instance;
	}	
	
	/**
	 * Create the filename for a resource.
	 *
	 * @access	private
	 * @param	string	The resource type to create the filename for.
	 * @param	array	An associative array of filename information. Optional.
	 * @return	string	The filename.
	 * @since	1.5
	 */
	function _createFileName($type, $parts = array())
	{
		$filename = '';

		switch ($type)
		{
			case 'controller':
				if (!empty($parts['protocol'])) {
					$parts['protocol'] = '.'.$parts['protocol'];
				}

				$filename = strtolower($parts['name']).$parts['protocol'].'.php';
				break;

			case 'view':
				if (!empty($parts['type'])) {
					$parts['type'] = '.'.$parts['type'];
				}

				$filename = strtolower($parts['name']).DS.'view'.$parts['type'].'.php';
			break;
		}
		return $filename;
	}
	
	public function display()
	{
		return parent::display();
	}
}