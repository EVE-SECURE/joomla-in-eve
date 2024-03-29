<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'helpers'.DS.'html');
JHTML::_('eve.contextmenu');
$pageClass = $this->params->get('pageclass_sfx');
?>

<?php if ($pageClass) : ?>
	<div class="<?php echo $pageClass; ?>">
<?php endif; ?>
<?php if ($this->params->get('show_page_title', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
<?php endif; ?>

<form action="<?php echo JRequest::getURI(); ?>" method="post">
<input type="hidden" name="reset" value="1" />
<?php foreach ($this->columns as $col): ?>
	<span style="white-space: nowrap;">
		<input type="checkbox" name="selectedColumns[<?php echo $col; ?>]" 
			onchange="this.form.submit()" value="<?php echo $col; ?>" 
			<?php echo isset($this->selectedColumns[$col]) ? 'checked="checked"' : ''; ?>> 
		<?php echo JText::_('Com_Evechartracking_'.$col); ?>
	</span>
<?php endforeach; ?>
</form>

<table>
	<tr>
		<th><?php echo JText::_('Com_Evechartracking_Character_Name'); ?></th>
		<?php foreach ($this->selectedColumns as $col): ?>
			<th><?php echo JText::_('Com_Evechartracking_'.$col); ?>
		<?php endforeach; ?>
	</tr>
	<?php foreach ($this->members as $member): ?>
		<tr>
			<td><?php echo $this->getMemberColumn($member, 'name'); ?></td>
			<?php foreach ($this->columns as $col): ?>
				<?php if (isset($this->selectedColumns[$col])): ?>
					<td><?php echo $this->getMemberColumn($member, $col); ?></td>
				<?php endif; ?>
			<?php endforeach; ?>
		</tr>
	<?php endforeach; ?>
</table>


<?php if ($pageClass) : ?>
	</div>
<?php endif; ?>
