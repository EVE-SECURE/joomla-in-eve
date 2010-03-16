<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

/**
 * EVE Character Sheet Search plugin
 *
 * @package		Joomla
 * @subpackage	JFramework
 * @since 		1.5
 */
class plgSearchEve extends JPlugin {
	
	function onSearchAreas() {
		$areas = array();
		foreach (array('character', 'corporation', 'alliance') as $entity) {
			$entities = $entity.'s';
			if ($this->params->get('search_'.$entities, 1)) {
				$areas['eve_'.$entities] = JText::_(ucfirst($entities));				
			}
		}
		return $areas;
	}
	
	function _prepareQuery($entity, $text, $phrase = '', $ordering = '')
	{
		$dbo = JFactory::getDBO();
		$q = EveFactory::getQuery($dbo);
		switch ($entity) {
			case 'character':
				$field1 = $field2 = 'ch.name';
				$q->addQuery("ch.name AS title, CONCAT_WS(' ', ch.gender, ch. race, ch.bloodLine, ch.title) AS text");
				$q->addQuery('ch.characterID', 'ch.name', 'ch.corporationID');
				$q->addQuery('co.corporationName', 'co.ticker AS corporationTicker');
				$q->addQuery('al.allianceID', 'al.name AS allianceName', 'al.shortName AS allianceShortName');
				$q->addTable('#__eve_characters', 'ch');
				$q->addJoin('#__eve_corporations', 'co', 'co.corporationID=ch.corporationID');
				$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
				break;
			case 'corporation':
				$field1 = 'co.corporationName';
				$field2 = 'co.ticker';
				$q->addQuery("CONCAT(co.corporationName, ' [', co.ticker, ']') AS title, description AS text");
				$q->addQuery('co.corporationID', 'co.corporationName', 'co.ticker AS corporationTicker');
				$q->addQuery('al.allianceID', 'al.name AS allianceName', 'al.shortName AS allianceShortName');
				$q->addTable('#__eve_corporations', 'co');
				$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
				break;
			case 'alliance':
				$field1 = 'al.name';
				$field2 = 'al.shortName';
				$q->addQuery("CONCAT(al.name, ' <', al.shortName, '>') AS title, NULL AS text");
				$q->addQuery('al.allianceID', 'al.name AS allianceName', 'al.shortName AS allianceShortName');
				$q->addTable('#__eve_alliances', 'al');
				break;
		}
		$wheres = array();
		switch ($phrase) {
			case 'exact':
				$text		= $q->Quote( '%'.$q->getEscaped( $text, true ).'%', false );
				$wheres2 	= array();
				$wheres2[] 	= $field1.' LIKE '.$text;
				$wheres2[] 	= $field2.' LIKE '.$text;
				$where 		= '(' . implode( ') OR (', $wheres2 ) . ')';
				break;
	
			case 'all':
			case 'any':
			default:
				$words = explode( ' ', $text );
				$wheres = array();
				foreach ($words as $word) {
					$word		= $q->Quote( '%'.$q->getEscaped( $word, true ).'%', false );
					$wheres2 	= array();
					$wheres2[] 	= $field1.' LIKE '.$word;
					$wheres2[] 	= $field2.' LIKE '.$word;
					$wheres[] 	= implode( ' OR ', $wheres2 );
				}
				$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
				break;
		}
		$q->addWhere($where);
		$q->addQuery('NULL AS created');
		//TODO: ordering
		
		return $q;
		
	}
	
	function onSearch($text, $phrase = '', $ordering = '', $areas = null) {
		
		if (is_array( $areas )) {
			if (!array_intersect($areas, array_keys( $this->onSearchAreas()))) {
				return array();
			}
		}
		
		$limit = $this->params->def('search_limit', 50);
		
		$text = trim( $text );
		if ($text == '') {
			return array();
		}
		
		$results = array();
		foreach (array('character', 'corporation', 'alliance') as $entity) {
			$entities = $entity.'s';
			if (!$this->params->get('search_'.$entities, 1)) {
				continue;				
			}
			$q = $this->_prepareQuery($entity, $text, $phrase, $ordering);
			$q->setLimit($limit);
			$result = $q->loadObjectList();
			foreach ($result as $row) {
				$row->browsernav = 0;
				$row->section = JText::_(ucfirst($entity));
				$row->href = EveRoute::_($entity, $row, $row, $row);
				$results[] = $row;
			}
		}
		return $results;
	}
	
}
//href, title (name), section (corp name, ticker?), text (gender, race), created