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
						<label for="jformkeyID"><?php echo JText::_('Com_Eve_Key_Id'); ?></label>
					</td>
					<td>
						<?php if ($this->item->keyID): ?>
							<input type="hidden" name="jform[keyID]" id="jformkeyID" value="<?php echo $this->item->keyID; ?>" />
							<strong><?php echo $this->item->keyID; ?></strong>
						<?php else: ?>
							<input type="text" name="jform[keyID]" id="jformkeyID" value="<?php echo $this->item->keyID; ?>" />
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformapiKey"><?php echo JText::_('Com_Eve_Verification_Code'); ?></label>
					</td>
					<td>
						<input type="text" name="jform[vCode]" id="jformvCode" value="<?php echo $this->item->vCode; ?>" size="96" maxlength="64" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformapiStatus"><?php echo JText::_('Com_Eve_Api_Key_Status'); ?></label>
					</td>
					<td>
						<?php echo JHTML::_('select.genericlist', $this->apiStates, 'jform[status]', null, 'value', 'value', $this->item->status, 'jformstatus'); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformowner"><?php echo JText::_('Com_Eve_User'); ?></label>
					</td>
					<td>
						<?php echo JHTML::_('list.users', 'jform[user_id]', $this->item->user_id, 1, null, 'name', false); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>