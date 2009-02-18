<?php
defined('ALE_BASE') or die('Restricted access');

class AleUtilContext {
	
	private $object;
	private $context = array();
	
	/**
	 * Enter description here...
	 *
	 * @param AleBase $object
	 * @param string $context
	 */
	public function __construct($object, $context) {
		$this->object = $object;
		$this->context[] = $context;
	}
	
	/**
	 * Add path segment
	 *
	 * @param string $name
	 * @return AleUtilContext $this
	 */
	public function __get($name) {
		$this->context[] = $name;
		return $this;
	}
	
	public function __call($name, $arguments) {
		$this->context[] = $name;
		return $this->object->_retrieveXml($this->context, $arguments);
	}

}
