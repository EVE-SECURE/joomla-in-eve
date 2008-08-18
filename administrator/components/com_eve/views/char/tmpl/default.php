<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<form action="index.php?option=com_eve&amp;control=char" method="post" name="adminForm">
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<div class="col100">
	<fieldset>
		<legend><?php echo JText::_('CHARACTER DETAILS'); ?></legend>
		<table class="admintable">
			<tbody>
				<?php if ($this->item->id > 0): ?>
				<tr>
					<td class="key">
						<?php echo JText::_('CHARACTER ID'); ?>
					</td>
					<td>
						<?php echo $this->item->characterID; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('CHARACTER NAME'); ?>
					</td>
					<td>
						<?php echo $this->item->name; ?>
					</td>
				</tr>
				<?php else: ?>
				<tr>
					<td class="key">
						<label for="characterID"><?php echo JText::_('CHARACTER ID'); ?></label>
					</td>
					<td>
						<input type="text" name="characterID" value="<?php echo $this->item->characterID; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="name"><?php echo JText::_('CHARACTER NAME'); ?></label>
					</td>
					<td>
						<input type="text" name="name" value="<?php echo $this->item->name; ?>" />
					</td>
				</tr>
				<?php endif; ?>
				<tr>
					<td class="key">
						<label for="userID"><?php echo JText::_('USER NAME'); ?></label>
					</td>
					<td>
						<?php echo $this->html_users; ?>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	</div>
		
	

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>