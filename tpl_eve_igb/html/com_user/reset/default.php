<?php defined('_JEXEC') or die; ?>

<div class="componentheading">
	<?php echo JText::_('FORGOT_YOUR_PASSWORD'); ?>
</div>

<form action="index.php?option=com_user&amp;task=requestreset" method="post" class="josForm form-validate">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="contentpane">
		<tr>
			<td colspan="2" height="40">
				<p><?php echo JText::_('RESET_PASSWORD_REQUEST_DESCRIPTION'); ?></p>
			</td>
		</tr>
		<tr>
			<td height="40">
				<label for="email" class="hasTip" title="<?php echo JText::_('RESET_PASSWORD_EMAIL_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_EMAIL_TIP_TEXT'); ?>"><?php echo JText::_('Email Address'); ?>:</label>
			</td>
			<td>
				<input id="email" name="email" type="text" class="required validate-email" />
			</td>
		</tr>
	</table>

	<input type="submit" value="<?php echo JText::_('Submit'); ?>" class="validate" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>