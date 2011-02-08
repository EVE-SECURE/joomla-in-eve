<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::_('behavior.tooltip');
JHTML::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'html');
$user = JFactory::getUser();


$n = count($this->items);
$ordering	= ($this->state->get('list.filter_order') == 'jobs.ordering');
$ordering_disabled = $ordering ?  '' : 'disabled="disabled"';

JHTML::stylesheet('administrator.css', '../media/com_cron/');

?>
<form action="<?php echo JRoute::_('index.php?option=com_cron&view=jobs'); ?>" method="post" name="adminForm" id="adminForm">
	<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_( 'Filter' ); ?>:
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_('Go'); ?></button>
				<button onclick="document.getElementById('filter_search').value='';this.form.submit();"><?php echo JText::_('Reset'); ?></button>
			</td>
			<td nowrap="nowrap">
				<!-- put select filters here -->
				<?php echo JHTML::_('filter.state', 'filter_state', $this->state->get('filter.state'), 'onchange="this.form.submit();"'); ?>
			</td>
		</tr>
	</table>
	<table class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th class="title" width="10px"><?php echo JText::_( 'NUM' ); ?></th>
				<th width="10" class="title">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('Event'), 'jobs.event', $this->state->get('list.filter_order_Dir'), $this->state->get('list.filter_order')); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('Pattern'), 'jobs.pattern', $this->state->get('list.filter_order_Dir'), $this->state->get('list.filter_order')); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('Type'), 'jobs.type', $this->state->get('list.filter_order_Dir'), $this->state->get('list.filter_order')); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'State', 'jobs.state', $this->state->get('list.filter_order_Dir'), $this->state->get('list.filter_order')); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'Order', 'jobs.ordering', $this->state->get('list.filter_order_Dir'), $this->state->get('list.filter_order')); ?>
					<?php echo JHTML::_('grid.order',  $this->items, 'filesave.png', 'jobs.saveorder'); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort',   'ID', 'jobs.id', $this->state->get('list.filter_order_Dir'), $this->state->get('list.filter_order')); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="11"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
		
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td>
					<?php echo JHTML::_('grid.checkedout', $item, $i, 'id' ); ?>
				</td>
				<td>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit' );?>::<?php echo $item->title; ?>">
						<?php if ( JTable::isCheckedOut($user->get('id'), $item->checked_out ) ): ?>
							<?php echo $item->event; ?>
						<?php else: ?>
							<a href="<?php echo JRoute::_('index.php?option=com_cron&task=job.edit&id='.$item->id); ?>">
								<?php echo $item->event; ?>
							</a>
						<?php endif; ?>
					</span>
				</td>
				<td>
					<?php echo $item->pattern; ?>
				</td>
				<td>
					<?php echo $item->type; ?>
				</td>
				<td align="center">
					<?php echo JHTML::_('jobs.state', $item, $i); ?>
				</td>
				<td class="order">
					<span><?php echo $this->pagination->orderUpIcon($i, true,'jobs.orderup', 'JGrid_Move_Up', $ordering); ?></span>
					<span><?php echo $this->pagination->orderDownIcon($i, $n, true, 'jobs.orderdown', 'JGrid_Move_Down', $ordering); ?></span>
					<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $ordering_disabled ?> class="text_area" style="text-align: center" />
				</td>
				<td align="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.filter_order') ;?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.filter_order_Dir'); ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>