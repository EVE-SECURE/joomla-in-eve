<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<div class="evecharsheet-roles">
<h2><?php echo JText::_('Roles'); ?></h2>
<table>
	<tr>
		<th></th>
		<?php foreach ($this->roleLocations as $location): ?>
			<th><?php echo JText::_($location); ?></th>
		<?php endforeach ?>
	<tr>
<?php foreach ($this->roles as $role): ?>
	<tr>
		<td><?php echo $role->roleName; ?></td>
		<?php foreach ($this->roleLocations as $location): ?>
			<td><?php echo $role->$location; ?></td>
		<?php endforeach ?>
	<tr>
	
<?php endforeach; ?>
</table>
</div>
