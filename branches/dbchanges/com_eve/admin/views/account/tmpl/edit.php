<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::stylesheet('common.css', 'administrator/components/com_eve/assets/');
?>
<form action="<?php //FIXME: JRoute::_('index.php?option=com_eve'); ?>" method="post" name="adminForm">
	<div class="col100">
	<fieldset>
		<legend><?php echo JText::_('USER DETAILS'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key">
						<label for="jformuserID"><?php echo JText::_('USER ID'); ?></label>
					</td>
					<td>
						<?php if ($this->item->userID): ?>
							<input type="hidden" name="jform[userID]" id="jformuserID" value="<?php echo $this->item->userID; ?>" />
							<strong><?php echo $this->item->userID; ?></strong>
						<?php else: ?>
							<input type="text" name="jform[userID]" id="jformuserID" value="<?php echo $this->item->userID; ?>" />
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformapiKey"><?php echo JText::_('API KEY'); ?></label>
					</td>
					<td>
						<input type="text" name="jform[apiKey]" id="jformapiKey" value="<?php echo $this->item->apiKey; ?>" size="96" maxlength="64" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformapiStatus"><?php echo JText::_('API KEY STATUS'); ?></label>
					</td>
					<td>
						<?php echo JHTML::_('select.genericlist', $this->apiStates, 'jform[apiStatus]', null, 'value', 'value', $this->item->apiStatus, 'jformapiStatus'); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformowner"><?php echo JText::_('OWNER'); ?></label>
					</td>
					<td>
						<?php echo JHTML::_('list.users', 'jform[owner]', $this->item->owner, 1, null, 'name', false); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>