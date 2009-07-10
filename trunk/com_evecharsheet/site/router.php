<?php
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
