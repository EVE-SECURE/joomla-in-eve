<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<div class="evecharsheet-titles">
<h3><?php echo JText::_('Titles'); ?></h3>
<table>
<?php foreach ($this->titles as $title): ?>
	<tr>
		<td><?php echo $title->titleName; ?></td>
	<tr>
<?php endforeach; ?>
</table>
</div>
