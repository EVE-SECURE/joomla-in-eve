<?php
class EvecharsheetRoute
{
	public static function character(&$item)
	{
		
	}
	
	public static function corporation($item)
	{
		
	}
	
	public static function owner($item)
	{
		
	}
	
	protected static function _findItemId($needles)
	{
		// Prepare the reverse lookup array.
		if (self::$lookup === null)
		{
			self::$lookup = array();

			$component	= &JComponentHelper::getComponent('com_evecharsheet');
			$menus		= &JApplication::getMenu('site', array());
			$items		= $menus->getItems('component_id', $component->id);

			foreach ($items as &$item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];
					$layout = $item->query['layout'];
					$desc = "$view.$layout";
					if (!isset(self::$lookup[$view])) {
						self::$lookup[$view] = array();
					}
					if (isset($item->query['id'])) {
						self::$lookup[$view][$item->query['id']] = $item->id;
					}
				}
			}
		}

		$match = null;

		foreach ($needles as $view => $id)
		{
			if (isset(self::$lookup[$view]))
			{
				if (isset(self::$lookup[$view][$id])) {
					return self::$lookup[$view][$id];
				}
			}
		}

		return null;
	}
}


function EvecharsheetBuildRoute(&$query)
{
	$segments = array();
	
	$view = JArrayHelper::getValue($query, 'view');
	$layout = JArrayHelper::getValue($query, 'layout', 'default');
	//urlencode();
	
	switch ($view.'.'.$layout) {
		case 'sheet.default':
			$segments[] = $query['characterID'];
			unset($query['characterID']);
			unset($query['layout']);
			unset($query['view']);
			break;
		case 'list.owner':
			$segments[] = 'owner';
			$segments[] = $query['owner'];
			unset($query['owner']);
			unset($query['layout']);
			unset($query['view']);
			break;
		case 'list.corporation':
			$segments[] = 'corporation';
			$segments[] = $query['corporationID'];
			unset($query['corporationID']);
			unset($query['layout']);
			unset($query['view']);
			break;
	}

	return $segments;
}

function EvecharsheetParseRoute($segments)
{
	$vars = array();
	$count = count($segments);
	if ($count == 1) {
		$vars['view'] = 'sheet';
		$vars['layout'] = 'default';
		$vars['characterID'] = $segments[0];
	} elseif ($count == 2) {
		$vars['view'] = 'list';
		$vars['layout'] = $segments[0];
		if ($segments[0] == 'corporation') {
			$key = 'corporationID';
		} else {
			$key = 'owner';
		}
		$vars[$key] = $segments[1];
	}
	

	return $vars;
}
