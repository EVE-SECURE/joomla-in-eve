<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
<form action="<?php echo JRequest::getURI(); ?>" method="post">
<input type="hidden" name="reset" value="1" />
<?php foreach ($this->columns as $col): ?>
	<span style="white-space: nowrap;">
		<input type="checkbox" name="selectedColumns[<?php echo $col; ?>]" onchange="this.form.submit()" value="<?php echo $col; ?>" <?php echo isset($this->selectedColumns[$col]) ? 'checked="checked"' : ''; ?>> <?php echo $col; ?>
	</span>
<?php endforeach; ?>
<table>
	<tr>
		<th>Character Name</th>
		<th>Corporation</th>
		<?php foreach ($this->selectedColumns as $col): ?>
			<th><?php echo JText::_($col); ?>
		<?php endforeach; ?>
	</tr>
	<?php foreach ($this->members as $member): ?>
		<tr>
			<td><?php echo $this->getMemberColumn($member, 'name'); ?></td>
			<td><?php echo $this->getMemberColumn($member, 'corporationID'); ?></td>
			<?php foreach ($this->columns as $col): ?>
				<?php if (isset($this->selectedColumns[$col])): ?>
					<td><?php echo $this->getMemberColumn($member, $col); ?></td>
				<?php endif; ?>
			<?php endforeach; ?>
		</tr>
	<?php endforeach; ?>
</table>
<?php echo $this->pagination->getListFooter(); ?>

<?php if (EveHelperIgb::isIgb()): ?>
	<input type="submit" value="<?php echo JText::_('Submit'); ?>" />
<?php endif; ?>
</form>
