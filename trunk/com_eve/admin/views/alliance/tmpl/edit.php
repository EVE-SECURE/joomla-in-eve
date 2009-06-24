<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<form action="<?php //FIXME: JRoute::_('index.php?option=com_eve'); ?>" method="post" name="adminForm">
	<div class="col100">
	<fieldset>
		<legend><?php echo JText::_('ALLIANCE DETAILS'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key">
						<label for="jformallianceID"><?php echo JText::_('ALLIANCE ID'); ?></label>
					</td>
					<td>
						<?php if ($this->item->allianceID): ?>
							<input type="hidden" name="jform[allianceID]" id="jformallianceID" value="<?php echo $this->item->allianceID; ?>" />
							<strong><?php echo $this->item->allianceID; ?></strong>
						<?php else: ?>
							<input type="text" name="jform[allianceID]" id="jformallianceID" value="<?php echo $this->item->allianceID; ?>" />
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformname"><?php echo JText::_('ALLIANCE NAME'); ?></label>
					</td>
					<td>
						<input type="text" name="jform[name]" id="jformname" value="<?php echo $this->item->name; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformshortName"><?php echo JText::_('ALLIANCE TAG'); ?></label>
					</td>
					<td>
						<input type="text" name="jform[shortName]" id="jformshortName" value="<?php echo $this->item->shortName; ?>" />
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
	<?php echo JHTML::_( 'form.token' ); ?>
</form>