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
				<label for="username"><?php echo JText::_('USER NAME'); ?></label>
			</td>
			<td>
				<input name="username" id="username" value="" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="passwd"><?php echo JText::_('PASSWORD'); ?></label>
			</td>
			<td>
				<input type="password" name="passwd" id="passwd" value="" />
			</td>
		</tr>
		
		<?php endif; ?>
		<tr>
			<td>
				<label for="keyID"><?php echo JText::_('Com_Eve_Key_Id'); ?></label>
			</td>
			<td>
				<input name="keyID" id="keyID" value="" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="vCode"><?php echo JText::_('Com_Eve_Verification_Code'); ?></label>
			</td>
			<td>
				<input name="vCode" id="vCode" value="" />
			</td>
		</tr>
	</table>
	<input type="submit" value="<?php echo JText::_('SUBMIT'); ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>
</div>
