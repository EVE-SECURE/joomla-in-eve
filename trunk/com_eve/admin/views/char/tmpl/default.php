<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<form action="index.php?option=com_eve&amp;control=char" method="post" name="adminForm">
	<div class="col100">
	<fieldset>
		<legend><?php echo JText::_('CHARACTER DETAILS'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key">
						<label for="characterID"><?php echo JText::_('CHARACTER ID'); ?></label>
					</td>
					<td>
						<?php if ($this->item->characterID): ?>
							<input type="hidden" name="characterID" value="<?php echo $this->item->characterID; ?>" />
							<strong><?php echo $this->item->characterID; ?></strong>
						<?php else: ?>
							<input type="text" name="characterID" value="<?php echo $this->item->characterID; ?>" />
						<?php endif; ?>
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
				<?php /*endif;*/ ?>
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