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
	<div class="col30">
	<fieldset>
		<legend><?php echo JText::_('CHARACTER DETAILS'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key">
						<label for="jformcharacterID"><?php echo JText::_('CHARACTER ID'); ?></label>
					</td>
					<td>
						<?php if ($this->item->characterID): ?>
							<input type="hidden" name="jform[characterID]" id="jformcharacterID" value="<?php echo $this->item->characterID; ?>" />
							<strong><?php echo $this->item->characterID; ?></strong>
						<?php else: ?>
							<input type="text" name="jform[characterID]" id="jformcharacterID" value="<?php echo $this->item->characterID; ?>" />
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformname"><?php echo JText::_('CHARACTER NAME'); ?></label>
					</td>
					<td>
						<input type="text" name="jform[name]" id="jformname" value="<?php echo $this->escape($this->item->name); ?>" />
					</td>
				</tr>
				<?php /*endif;*/ ?>
				<tr>
					<td class="key">
						<label for="jformuserID"><?php echo JText::_('USER NAME'); ?></label>
					</td>
					<td>
						<?php echo JHTML::_('eve.accountlist', 'jform[userID]', null, $this->item->userID, 'jformuserID'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	</div>
	
	<?php echo $this->loadTemplate('apischedule'); ?>
	
	<?php echo $this->loadTemplate('sectionaccess'); ?>
			
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>