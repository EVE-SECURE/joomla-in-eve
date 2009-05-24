<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<form action="index.php?option=com_eve&amp;control=schedule" method="post" name="adminForm">
	<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JHTML::_('filter.search', $this->filter_search); ?>
			</td>
			<td nowrap="nowrap">
				<?php echo JHTML::_('grid.state', $this->filter_state, 'Enabled', 'Disabled'); ?>
				<?php echo JHTML::_('select.genericlist', $this->apicalls, 'filter_apicall', array('onchange'=>'javascript:this.form.submit()'), 'id', 'typeCall', $this->filter_apicall); ?>
			</td>
		</tr>
	</table>
	<?php if (count($this->items)) : ?>
	<table class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th class="title" width="10px"><?php echo JText::_('NUM'); ?></th>
				<th width="10" class="title">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_('API CALL'), 'typeCall', $this->filter_order_dir, $this->filter_order); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_('Scheduled time'), 'next', $this->filter_order_dir, $this->filter_order); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_('Owner'), 'userName', $this->filter_order_dir, $this->filter_order); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_('Character'), 'characterName', $this->filter_order_dir, $this->filter_order); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_('Enabled'), 'published', $this->filter_order_dir, $this->filter_order); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
			<td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php for ($i=0, $n=count($this->items), $rc=0; $i < $n; $i++, $rc = 1 - $rc) : ?>
			<?php
				$this->loadItem($i);
				echo $this->loadTemplate('item');
			?>
		<?php endfor; ?>
		</tbody>
	</table>
	<?php else : ?>
		<?php echo JText::_( 'NO CALLS SCHEDULED' ); ?>
	<?php endif; ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filter_order;?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_dir; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>