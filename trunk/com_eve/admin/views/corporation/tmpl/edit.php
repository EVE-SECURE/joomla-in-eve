<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<form action="i<?php //FIXME: JRoute::_('index.php?option=com_eve'); ?>" method="post" name="adminForm">
	<div class="col100">
	<fieldset>
		<legend><?php echo JText::_('CORPORATION DETAILS'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key">
						<label for="jformcorporationID"><?php echo JText::_('CORPORATION ID'); ?></label>
					</td>
					<td>
						<?php if ($this->item->corporationID): ?>
							<input type="hidden" name="jform[corporationID]" id="jformcorporationID" value="<?php echo $this->item->corporationID; ?>" />
							<strong><?php echo $this->item->corporationID; ?></strong>
						<?php else: ?>
							<input type="text" name="jform[corporationID"] id="jformcorporationID" value="<?php echo $this->item->corporationID; ?>" />
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformcorporationName"><?php echo JText::_('CORPORATION NAME'); ?></label>
					</td>
					<td>
						<input type="text" name="jform[corporationName]" id="jformcorporationName" value="<?php echo $this->item->corporationName; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformticker"><?php echo JText::_('CORPORATION TAG'); ?></label>
					</td>
					<td>
						<input type="text" name="jform[ticker]" id="jformticker" value="<?php echo $this->item->ticker; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformallianceID"><?php echo JText::_('ALLIANCE'); ?></label>
					</td>
					<td>
						<?php echo $this->html_alliance; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformowner"><?php echo JText::_('OWNER?'); ?></label>
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
	<?php echo JHTML::_( 'form.token' ); ?>
</form>