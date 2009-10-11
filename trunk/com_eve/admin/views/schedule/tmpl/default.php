<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::stylesheet('common.css', 'administrator/components/com_eve/assets/');
JHTML::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'html');
JHTML::_('behavior.tooltip');

?>
<form action="<?php echo JRoute::_('index.php?option=com_eve&view=schedule'); ?>" method="post" name="adminForm">
	<fieldset class="filter">
		<div class="left">
			<?php echo JHTML::_('filter.search', $this->state->get('filter.search')); ?>
		</div>
		<div class="right">
			<?php echo JHTML::_('grid.state', $this->state->get('filter.state'), 'Enabled', 'Disabled'); ?>
			<?php echo JHTML::_('select.genericlist', $this->apiCalls, 'filter_apicall', 
				array('onchange'=>'javascript:this.form.submit()'), 'id', 'typeCall', $this->state->get('filter.apicall')); ?>
		</div>
	</fieldset>
	<?php if (count($this->items)) : ?>
	<table class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th class="title" width="10px"><?php echo JText::_('NUM'); ?></th>
				<th width="10" class="title">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_('API CALL'), 'typeCall', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_('Scheduled time'), 'next', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_('Owner'), 'userName', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_('Character'), 'characterName', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_('Enabled'), 'published', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
			<td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td>
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_eve&task=schedule.edit&cid[]='.$item->id); ?>">
						<b><?php echo $item->typeCall; ?></b>
					</a>
				</td>
				<td>
					<?php echo JHTML::_('date', $item->next, JText::_('DATE_FORMAT_LC2')); ?>
				</td>
				<td>
					<?php if ($item->userID): ?>
						<?php echo $item->userName; ?> (<?php echo $item->userID; ?>)
					<?php endif; ?>
				</td>
				<td>
					<?php if ($item->characterID): ?>
						<?php echo $item->characterName; ?> (<?php echo $item->characterID; ?>)
					<?php endif; ?>
				</td>
				<td align="center">
					<?php echo JHTML::_('grid.published', $item, $i, 'tick.png', 'publish_x.png', $prefix='schedule.'); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php else : ?>
		<?php echo JText::_( 'NO CALLS SCHEDULED' ); ?>
	<?php endif; ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>