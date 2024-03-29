<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::stylesheet('common.css', 'administrator/components/com_eve/assets/');
JHTML::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'html');
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
						<input type="text" name="jform[name]" id="jformname" value="<?php echo $this->escape($this->item->name); ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformshortName"><?php echo JText::_('ALLIANCE TAG'); ?></label>
					</td>
					<td>
						<input type="text" name="jform[shortName]" id="jformshortName" value="<?php echo $this->escape($this->item->shortName); ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformowner1"><?php echo JText::_('OWNER?'); ?></label>
					</td>
					<td>
						<?php echo JHTML::_('select.booleanlist', 'jform[owner]', null, $this->item->owner, 'yes', 'no', 'jformowner'); ?>
					</td>
				</tr>				
			</tbody>
		</table>
	</fieldset>
	</div>

	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>