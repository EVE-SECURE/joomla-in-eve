<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.controller' );

class EveController extends JController {
	
/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param sting $name
	 * @param sting $prefix
	 * @param array $config
	 * @return JModel
	 */
	function &getModel( $name = '', $prefix = '', $config = array() ) {
		if (empty($prefix)) {
			$prefix = 'EveModel';
		}
		if ($prefix == 'EveModel') {
			if (preg_match('/^([^_]+)/', $name, $matches)) {
				$name = $matches[1];
			}
		}
		return parent::getModel($name, $prefix, $config);
	}
	
}