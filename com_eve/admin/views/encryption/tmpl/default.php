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
		<legend><?php echo JText::_('ENCRYPTION'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key">
						<label for="jforcipher"><?php echo JText::_('Cipher'); ?></label>
					</td>
					<td>
						<?php echo JHTML::_('select.genericlist', $this->algorithms, 'jform[cipher]',null, 'value', 'text'); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jformmode"><?php echo JText::_('Mode'); ?></label>
					</td>
					<td>
						<?php echo JHTML::_('select.genericlist', $this->modes, 'jform[mode]',null, 'value', 'text'); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jforkey"><?php echo JText::_('Key Phrase'); ?></label>
					</td>
					<td>
						<input type="text" name="jform[key]" id="jformkey" value="" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="jfordisplayapikey"><?php echo JText::_('Display'); ?></label>
					</td>
					<td>
						<?php echo JHTML::_('select.booleanlist', 'jform[displayapikey]'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	</div>
		
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>
