<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<form action="index.php?option=com_eve&amp;control=alliance" method="post" name="adminForm">
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<div class="col100">
	<fieldset>
		<legend><?php echo JText::_('ALLIANCE DETAILS'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key">
						<label for="allianceID"><?php echo JText::_('ALLIANCE ID'); ?></label>
					</td>
					<td>
						<input type="text" name="allianceID" value="<?php echo $this->item->allianceID; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="name"><?php echo JText::_('ALLIANCE NAME'); ?></label>
					</td>
					<td>
						<input type="text" name="name" value="<?php echo $this->item->name; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="shortName"><?php echo JText::_('ALLIANCE TAG'); ?></label>
					</td>
					<td>
						<input type="text" name="shortName" value="<?php echo $this->item->shortName; ?>" />
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