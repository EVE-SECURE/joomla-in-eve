<?php
/**
 * @version $Id: xmlelement.php 215 2010-05-14 20:27:33Z kovalikp $
 * @license GNU/LGPL, see COPYING and COPYING.LESSER
 * This file is part of Ale - PHP API Library for EVE.
 *
 * Ale is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Ale is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Ale.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Wrapper for SimpleXMLElement providing most features of SimpleXML (read only)
 * Provides features speciffic for EVE API xml format, namely
 * <ul>
 * 	<li>Direct access to rowset nodes by their 'name' attribute</li>
 * 	<li>Array-like access to row nodes for rowsets by their 'key' attribute</li>
 * 	<li>Iteration through row nodes</li>
 * 	<li>Parsing to array structure</li>
 * </ul>
 *
 * @example
 * <?php
 * $data = $api->char->CharacterSheet(); //get character sheet
 * $xml = new AleParserXMLElement($data);
 * $cachedUntil = $xml->cachedUntil; //direct acces to node by node name
 * $skills = $xml->result->skills; //direct access to rowset node by attribute name
 * foreach ($xml->result->skills as $skill) {} //iteration
 * $skillCount = count($skills); //number of skills
 * $skillArray = $skills->toArray(); //convert node to array
 * $skills[3413]->skillpoints; //array-like access to rowset node, 'skillpoinsts' is attribute of row
 */
class AleParserXMLElement implements Countable, ArrayAccess, IteratorAggregate  {

	private $name = null;
	private $data = null;
	private $children = null;
	private $attributes = null;
	private $rows = null;

	/**
	 * Constuctor
	 *
	 * @param SimpleXMLElement|string $data
	 */
	public function __construct($data) {
		if (is_string($data)) {
			$data = new SimpleXMLElement($data);
		}
		if (!($data instanceof SimpleXMLElement)) {
			throw new InvalidArgumentException('EveApiParser::__construct(): requires instance of SimpleXMLElement or valid XML string');
		}
		$this->data = $data;
		$this->name = $data->getName();
		if (!$this->name) {
			return;
		}
	}

	/**
	 * Prepare child nodes. "rowset" modes will be accesed by their "name" attribute
	 *
	 */
	protected function prepareChildren() {
		if (isset($this->children)) {
			return;
		}

		$this->children = array();
		$nodes =  $this->data->children();
		foreach ($nodes as $node) {
			$name = (string) $node->getName();
			if ($name == 'rowset') {
				$attribs = $node->attributes();
				$this->children[(string) $attribs['name']] = $this->transformNode($node);
			} else {
				$this->children[$name] = $this->transformNode($node);
			}
		}

		$this->attributes = array();
		$attribs = $this->data->attributes();
		foreach ($attribs as $key => $value) {
			$this->attributes[(string) $key] = (string) $value;
		}
	}

	/**
	 * Prepare rows array attribute for 'rowset' node
	 *
	 */
	protected function prepareRows() {
		if (isset($this->rows)) {
			return;
		}

		$this->rows = array();
		if ($this->name == 'rowset') {
			$attribs = $this->data->attributes();
			$key = isset($attribs['key']) ? (string) $attribs['key'] : null;
			$keys = $key ? explode(',', $key) : array();
			$children = $this->data->children();
			foreach ($children as $child) {
				if ($child->getName() != 'row') {
					continue;
				}
				//echo (string)$child;
				$child = $this->transformNode($child);
				if ($key) {
					$attribs = $child->attributes();
					$rows = &$this->rows;
					for ($i = 0; $i < count($keys); $i++) {
						$keyVal = (string) $attribs[trim($keys[$i])];
						if ($i == count($keys) - 1) {
							$rows[$keyVal] = $child;
						} else {
							if (!isset($rows[$keyVal])){
								$rows[$keyVal] = array();
							}
							$rows = &$rows[$keyVal];
						}
					}
				} else {
					$this->rows[] = $child;
				}
			}
		}
	}

	/**
	 * Return instance of same class as "$this"
	 *
	 * @param SimpleXMLElement|string $node
	 * @return AleParserXMLElement
	 */
	protected function transformNode($node) {
		$classname = get_class($this);
		return new $classname($node);
	}

	/**
	 * Walk through node tree and fills $result array
	 *
	 * @param array $result
	 * @param SimpleXMLElement $node
	 * @param string $key
	 */
	protected function nodeToArray(&$result, $node, $key = null) {
		$name = (string) $node->getName();
		$attributes = $node->attributes();
		$children = $node->children();

		if (count($attributes) || count($children)) {
			$result = array();
			if (!count($children) && (string) $node) {
				$result['nodeText'] = (string) $node;
			}
		} else {
			$result = (string) $node;
		}

		if ($name != 'rowset') {
			foreach ($attributes as $aname => $avalue) {
				$result[(string)$aname] = (string) $avalue;
			}
		}

		$i = 0;
		foreach ($children as $name => $child) {
			if (!is_array($result)) {
				$result = array();
			}
			$attributes = $child->attributes();

			$_key = $key;
			if ($name == 'rowset') {
				$name = (string) $attributes['name'];
				$_key = (string) $attributes['key'];
			}
			if ($name == 'row') {
				//echo $key.'--';
				$keys = $key ? explode(',', $key) : array();
				//print_r($keys);
				$tmp = &$result;
				for ($ii = 0; $ii < count($keys); $ii++) {
					$keyVal = (string) $attributes[trim($keys[$ii])];
					if ($ii == count($keys) - 1) {
						$this->nodeToArray($tmp[$keyVal], $child, $_key);
						continue 2;
					} else {
						if (!isset($tmp[$keyVal])){
							$tmp[$keyVal] = array();
						}
						$tmp =& $tmp[$keyVal];
					}
						
				}
				$name = (string) $attributes[$key];
				if (!$name) {
					$name = $i;
				}
			}
			$this->nodeToArray($result[$name], $child, $_key);
			$i += 1;
		}
	}

	/**
	 * Returns data as SimpleXMLElement
	 *
	 * @return SimpleXMLElement
	 */
	public function getSimpleXMLElement() {
		return $this->data;
	}

	/**
	 * Finds children of given node
	 *
	 * @return array
	 */
	public function children() {
		$nodes =  $this->data->children();
		$result = array();
		foreach ($nodes as $node) {
			$result[] = $this->transformNode($node);
		}
		return $result;
	}

	/**
	 * Get node name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Return a well-formed XML string based on element
	 *
	 * @return string
	 */
	function asXML() {
		return $this->data->asXML();
	}

	/**
	 * Identifies an element's attributes
	 *
	 * @return unknown
	 */
	public function attributes() {
		$this->prepareChildren();
		return $this->attributes;
	}

	/**
	 * The xpath method searches the AleParserXMLElement node for children matching the XPath path.
	 *
	 * @param string $path
	 * @return array
	 */
	public function xpath($path) {
		$nodes = $this->data->xpath($path);
		$result = array();
		foreach ($nodes as $node) {
			$result[] = $this->transformNode($node);
		}
		return $result;
	}

	/**
	 * Node accessor
	 *
	 * @param string $name
	 * @return AleParserXMLElement
	 */
	public function __get($name) {
		$this->prepareChildren();
		if (isset($this->children[$name])) {
			return $this->children[$name];
		}
		if (isset($this->attributes[$name])) {
			return $this->attributes[$name];
		}
		return null;
	}

	/**
	 * Node isset checker
	 *
	 * @param string $name
	 * @return AleParserXMLElement
	 */
	public function __isset($name) {
		$this->prepareChildren();
		return isset($this->children[$name]) || isset($this->attributes[$name]);
	}

	/**
	 * Implements ArrayAccess::offsetExists()
	 *
	 * @param mixed $i
	 * @return bool
	 */
	public function offsetExists($i) {
		$this->prepareRows();
		return isset($this->rows[$i]);
	}

	/**
	 * Implements ArrayAccess::offsetGet()
	 *
	 * @param mixed $i
	 * @return AleParserXMLElement
	 */
	public function offsetGet($i) {
		$this->prepareRows();
		return $this->rows[$i];
	}

	/**
	 * Not implemented ArrayAccess::offsetSet()
	 * Class is read-only
	 */
	public function offsetSet($i, $data) {
		throw new BadMethodCallException('Not-Implemented');
	}

	/**
	 * Not implemented ArrayAccess::offsetUnset()
	 * Class is read-only
	 */
	public function offsetUnset($i) {
		throw new BadMethodCallException('Not-Implemented');
	}

	/**
	 * Implements Countable::count()
	 * Return number of rows for rowsets
	 *
	 * @return int
	 */
	public function count() {
		$this->prepareRows();
		return count($this->rows);
	}

	/**
	 * Implements IteratorAggregate::getIterator()
	 *
	 * @return ArrayIterator
	 */
	public function getIterator() {
		$this->prepareRows();
		return new ArrayIterator($this->rows);
	}

	/**
	 * String representation of node
	 *
	 * @return string
	 */
	public function __toString() {
		return (string) $this->data;
	}

	/**
	 * Transform node tree to array structure
	 *
	 * @return array
	 */
	public function toArray() {
		$key = null;
		$name = (string) $this->getName();
		if ($name == 'rowset') {
			$attributes = $this->attributes();
			$key = (string) $attributes['key'];
		}
		$result = array();
		$this->nodeToArray($result, $this->data, $key);
		return $result;
	}

}
