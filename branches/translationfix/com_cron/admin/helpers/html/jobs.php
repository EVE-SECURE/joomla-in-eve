<?php
/**
 * @version		$Id$
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JHTMLJobs
{
	static public function state(&$row, $id)
	{
		if ($row->state == 0) {
			$img = 'publish_x.png';
			$alt = JText::_('Enable');
			$task = 'jobs.enable';
		} else {
			$img = 'tick.png';
			$alt = JText::_('Disable');
			$task = 'jobs.disable';
		}

		$picture = JHTML::_('image.administrator', $img, '/images/', null, '/images/', $alt);
		$link = 'javascript:void(0)';
		$attribs = array('onclick'=>"return listItemTask('cb".$id."','".$task."')", 'title'=>$alt);
		return JHTML::link($link, $picture, $attribs);
	}
	
}