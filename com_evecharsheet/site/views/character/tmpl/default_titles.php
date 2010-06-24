<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<div class="evecharsheet-titles">
<h2><?php echo JText::_('Com_Evecharsheet_Titles'); ?></h2>
<table>
<?php foreach ($this->titles as $title): ?>
	<tr>
		<td><?php echo $title->titleName; ?></td>
	<tr>
<?php endforeach; ?>
</table>
</div>
