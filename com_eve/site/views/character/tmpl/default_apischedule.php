<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="characterApischeduleForm">
	<input type="hidden" name="option" value="com_eve" />
	<input type="hidden" name="task" value="character.apischedule" />
	<input type="hidden" name="characterID" value="<?php echo $this->character->characterID; ?>" />
	<?php echo JHTML::_('form.token'); ?>
<table>
	<tr>
		<th class="title">
			<?php echo JText::_('Com_Eve_Api_Call_Name'); ?>
		</th>
		<th class="title">
			<?php echo JText::_('Com_Eve_Api_Call_Published'); ?>
		</th>
	</tr>
	<?php foreach ($this->apischedule as $i => $item): ?>
		<tr>
			<td>
				<?php echo $this->escape($item->call); ?>
			</td>
			<td>
				<input type="hidden" name="apischedule[<?php echo $i; ?>][id]" value="<?php echo $item->id ?>">
				<input type="hidden" name="apischedule[<?php echo $i; ?>][apicall]" value="<?php echo $item->apicall ?>">
				<input type="checkbox" name="apischedule[<?php echo $i; ?>][published]" value="1" <?php echo $item->published ? 'checked="checked"' : ''; ?> />
			</td>
		</tr>
	<?php endforeach; ?>
</table>
</form>