<?php
class eveMyCharsHelper {

	function getCharacters()
	{
		$user = &JFactory::getUser();
		$db = &JFactory::getDBO();

		$db = &JFactory::getDBO();
		$query = 'SELECT characterID, name FROM #__eve_characters AS ch'.
	    			' WHERE ch.user_id = '.intval($user->get('id')). 
	    			' ORDER BY name ASC';

		$db->setQuery($query);
		return ($items = $db->loadObjectList()) ? $items : array();
	}
}
