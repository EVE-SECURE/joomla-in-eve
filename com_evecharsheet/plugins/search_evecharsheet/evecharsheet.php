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
class plgSearchEvecharsheet extends JPlugin {
	
	function onSearchAreas() {
		static $areas = array(
			'evecharshet' => 'Character Sheets'
		);
		return $areas;
	}
	
	function onSearch($text, $phrase='', $ordering='', $areas=null) {
		
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
		$dbo = JFactory::getDBO();
		
		$q = EveFactory::getQuery($dbo);
		$field1 = 'ch.name';
		$q->addQuery("ch.name AS title, CONCAT_WS(' ', ch.gender, ch. race, ch.bloodLine, ch.title) AS text");
		$q->addQuery('ch.characterID', 'ch.name', 'ch.corporationID');
		$q->addQuery('co.corporationName', 'co.ticker AS corporationTicker');
		$q->addQuery('al.allianceID', 'al.name AS allianceName', 'al.shortName AS allianceShortName');
		$q->addTable('#__eve_characters', 'ch');
		$q->addJoin('#__eve_corporations', 'co', 'co.corporationID=ch.corporationID');
		$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
		
		$wheres = array();
		switch ($phrase) {
			case 'exact':
				$text		= $q->Quote( '%'.$q->getEscaped( $text, true ).'%', false );
				$wheres2 	= array();
				$wheres2[] 	= $field1.' LIKE '.$text;
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
					$wheres[] 	= implode( ' OR ', $wheres2 );
				}
				$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
				break;
		}
		$q->addWhere($where);
		$q->addQuery('NULL AS created');
		$acl = EveFactory::getACL();
		//gender known = called /char/CharacterSheet.xml.aspx
		$q->addWhere("ch.gender <> 'Unknown'");
		$q->setLimit($limit);
		//TODO: check access
		

		switch ($ordering) {
			case 'oldest':
				//$q->addOrder('created', 'ASC');
				break;
			case 'newest':
				//$q->addOrder('created', 'DESC');
				break;
			case 'alpha':
			case 'category':
			case 'popular':
			default:
				$q->addOrder('title', 'ASC');
				break;
		}
		
		
		$results = array();
		$result = $q->loadObjectList();
		foreach ($result as $row) {
			$row->browsernav = 0;
			$row->section = JText::_('Character Sheet');
			$row->href = EveRoute::_('charsheet', $row, $row, $row);
			$results[] = $row;
		}
		return $results;
		
		
		
	}
	
}
//href, title (name), section (corp name, ticker?), text (gender, race), created