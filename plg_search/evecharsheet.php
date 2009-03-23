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
			'evecharshet' => 'EVE Character Sheet'
		);
		return $areas;
	}
	
	function onSearch($text, $phrase='', $ordering='', $areas=null) {
		
		if (is_array( $areas )) {
			if (!array_intersect($areas, array_keys( $this->onSearchAreas()))) {
				return array();
			}
		}
		
	 	$plugin =& JPluginHelper::getPlugin('search', 'evecharsheet');
	 	$pluginParams = new JParameter( $plugin->params );
	
		$limit = $pluginParams->def( 'search_limit', 50 );
		
			
		$text = trim( $text );
		if ($text == '') {
			return array();
		}
		$dbo = JFactory::getDBO();
		$text	= $dbo->Quote( '%'.$dbo->getEscaped( $text, true ).'%', false );
		
		$searches = array();
		$searches[] = 'ch.name LIKE '.$text;
		if ($pluginParams->def('search_owner', 0)) {
			$searches[] = 'COALESCE(co.owner, al.owner) AND us.name LIKE '.$text;
		}
		if ($pluginParams->def('search_corporation', 0)) { 
			$searches[] = 'co.corporationName LIKE '.$text;
			$searches[] = 'co.ticker LIKE '.$text;
		}
		
		$q = EveFactory::getQuery($dbo);
		$q->addTable('#__eve_characters', 'ch');
		$q->addJoin('#__eve_accounts', 'ac', 'ac.userID=ch.userID');
		$q->addJoin('#__eve_corporations', 'co', 'co.corporationID=ch.corporationID');
		$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
		$q->addJoin('#__users', 'us', 'ac.owner=us.id');
		
		$q->addQuery('ch.characterID');;
		$q->addQuery('ch.name AS title');
		$q->addQuery('ch.gender', 'ch.race', 'ch.bloodLine, ch.title as corporationTitle');
		$q->addQuery('co.corporationName', 'co.ticker');
		$q->addQuery('COALESCE(co.owner, al.owner) AS showOwner');
		$q->addQuery('us.name AS userName');
		$q->addQuery('startDateTime AS created');
		$q->addWhere('(('.implode(') OR (', $searches).'))');
		$q->addWhere("ch.gender <> 'Unknown'");
		$q->setLimit($limit);
		switch ($ordering) {
			case 'oldest':
				$q->addOrder('created', 'ASC');
				break;
			case 'newest':
				$q->addOrder('created', 'DESC');
				break;
			case 'alpha':
			case 'category':
			case 'popular':
			default:
				$q->addOrder('title', 'ASC');
				break;
		}
		$result = $q->loadObjectList();
		foreach ($result as $row) {
			$row->browsernav = 0;
			$row->section = JText::_('Character Sheet');
			$row->text = $row->corporationName;
			if ($row->ticker) {
				$row->text .= ' ['.$row->ticker.']';
			}
			if ($row->userName && $row->showOwner) {
				$row->text .= '; '.JText::sprintf('Owned by %s', $row->userName);
			}
			if ($row->corporationTitle) {
				$row->text .= '; '.JText::sprintf('Title %s', $row->corporationTitle);
			}
			$row->text .= sprintf("; %s %s %s", $row->gender, $row->race, $row->bloodLine);
			$row->href = JRoute::_('index.php?option=com_evecharsheet&view=sheet&characterID='.$row->characterID);
		}
		return $result;
		
	}
	
}
//href, title (name), section (corp name, ticker?), text (gender, race), created