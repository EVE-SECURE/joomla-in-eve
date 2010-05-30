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
							<input type="text" name="jform[corporationID]" id="jformcorporationID" value="<?php echo $this->item->corporationID; ?>" />
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformcorporationName"><?php echo JText::_('CORPORATION NAME'); ?></label>
					</td>
					<td>
						<input type="text" name="jform[corporationName]" id="jformcorporationName" value="<?php echo $this->escape($this->item->corporationName); ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformticker"><?php echo JText::_('CORPORATION TAG'); ?></label>
					</td>
					<td>
						<input type="text" name="jform[ticker]" id="jformticker" value="<?php echo $this->escape($this->item->ticker); ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformallianceID"><?php echo JText::_('ALLIANCE'); ?></label>
					</td>
					<td>
						<?php echo JHTML::_('eve.alliancelist', 'jform[allianceID]', null, $this->item->allianceID, 'jformallianceID'); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformowner"><?php echo JText::_('OWNER?'); ?></label>
					</td>
					<td>
						<?php echo JHTML::_('select.booleanlist', 'jform[owner]', null, $this->item->owner); ?>
					</td>
				</tr>				
			</tbody>
		</table>
	</fieldset>
	</div>
	
	<?php echo $this->loadTemplate('sectionaccess'); ?>
		
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>