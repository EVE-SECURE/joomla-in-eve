<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
<?php if ($this->params->get('show_page_title')) : ?>
<div class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
	<?php echo $this->escape($this->params->get('page_title')); ?>
</div>
<?php endif; ?>
<div>
<form action="<?php echo JRoute::_('index.php?option=com_eve'); ?>" method="post">
	<input type="hidden" name="task" value="apiform.process" />
	<table>
		<?php if ($this->require_credentials): ?>
		<tr>
			<td>
				<label for="username"><?php echo JText::_('COM_EVE_USER_NAME'); ?></label>
			</td>
			<td>
				<input name="username" id="username" value="" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="passwd"><?php echo JText::_('COM_EVE_PASSWORD'); ?></label>
			</td>
			<td>
				<input type="password" name="passwd" id="passwd" value="" />
			</td>
		</tr>
		
		<?php endif; ?>
		<tr>
			<td>
				<label for="userID"><?php echo JText::_('COM_EVE_API_USERID'); ?></label>
			</td>
			<td>
				<input name="userID" id="userID" value="" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="apiKey"><?php echo JText::_('COM_EVE_APIKEY'); ?></label>
			</td>
			<td>
				<input name="apiKey" value="" />
			</td>
		</tr>
	</table>
	<input type="submit" value="<?php echo JText::_('COM_EVE_SUBMIT'); ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>
</div>
