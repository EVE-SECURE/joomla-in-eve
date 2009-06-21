<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<form action="index.php?option=com_eve&amp;control=corp" method="post" name="adminForm">
	<div class="col100">
	<fieldset>
		<legend><?php echo JText::_('CORPORATION DETAILS'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key">
						<label for="corporationID"><?php echo JText::_('CORPORATION ID'); ?></label>
					</td>
					<td>
						<?php if ($this->item->corporationID): ?>
							<input type="hidden" name="corporationID" value="<?php echo $this->item->corporationID; ?>" />
							<strong><?php echo $this->item->corporationID; ?></strong>
						<?php else: ?>
							<input type="text" name="corporationID" value="<?php echo $this->item->corporationID; ?>" />
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="corporationName"><?php echo JText::_('CORPORATION NAME'); ?></label>
					</td>
					<td>
						<input type="text" name="corporationName" value="<?php echo $this->item->corporationName; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="ticker"><?php echo JText::_('CORPORATION TAG'); ?></label>
					</td>
					<td>
						<input type="text" name="ticker" value="<?php echo $this->item->ticker; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="alliance"><?php echo JText::_('ALLIANCE'); ?></label>
					</td>
					<td>
						<?php echo $this->html_alliance; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="standings"><?php echo JText::_('STANDINGS'); ?></label>
					</td>
					<td>
						<input type="text" name="standings" value="<?php echo $this->item->standings; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="owner"><?php echo JText::_('OWNER?'); ?></label>
					</td>
					<td>
						<?php echo $this->html_owner; ?>
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