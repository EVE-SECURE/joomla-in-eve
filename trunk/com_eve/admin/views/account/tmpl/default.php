<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<form action="index.php?option=com_eve&amp;control=account" method="post" name="adminForm">
	<div class="col100">
	<fieldset>
		<legend><?php echo JText::_('USER DETAILS'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key">
						<label for="userID"><?php echo JText::_('USER ID'); ?></label>
					</td>
					<td>
						<?php if ($this->item->userID): ?>
							<input type="hidden" name="userID" value="<?php echo $this->item->userID; ?>" />
							<strong><?php echo $this->item->userID; ?></strong>
						<?php else: ?>
							<input type="text" name="userID" value="<?php echo $this->item->userID; ?>" />
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="name"><?php echo JText::_('API KEY'); ?></label>
					</td>
					<td>
						<input type="text" name="apiKey" value="<?php echo $this->item->apiKey; ?>" size="96" maxlength="64" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="apiStatus"><?php echo JText::_('API KEY STATUS'); ?></label>
					</td>
					<td>
						<?php echo JHTML::_('select.genericlist', $this->apiStates, 'apiStatus', null, 'value', 'value', $this->item->apiStatus); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="userID"><?php echo JText::_('OWNER'); ?></label>
					</td>
					<td>
						<?php echo JHTML::_('select.genericlist', $this->users, 'owner', null, 'id', 'name', $this->item->owner); ?>
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