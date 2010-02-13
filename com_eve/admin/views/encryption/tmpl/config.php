<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::stylesheet('common.css', 'administrator/components/com_eve/assets/');

?>
<form action="<?php //FIXME: JRoute::_('index.php?option=com_eve'); ?>" method="post" name="adminForm">
		<table class="adminform">
			<tbody>
				<tr>
					<th>
						<label for="jforcipher"><?php echo JText::_('Please, create a following file'); ?></label>
					</th>
				</tr>
				<tr>
					<td>
						<input type="text" value="<?php echo $this->escape($this->path); ?>" readonly="readonly"  style="width: 100%;">
					</td>
				</tr>
				<tr>
					<td>
						<textarea rows="25" cols="110" class="inputbox" style="width: 100%; height: 500px;"><?php echo $this->escape($this->config); ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>
