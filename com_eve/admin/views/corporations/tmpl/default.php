<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::stylesheet('common.css', 'administrator/components/com_eve/assets/');
JHTML::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'html');
JHTML::_('behavior.tooltip');
$user	= &JFactory::getUser();
$userId	= $user->get('id');
?>
<form action="<?php echo JRoute::_('index.php?option=com_eve&view=corporations'); ?>" method="post" name="adminForm">
	<fieldset class="filter">
		<div class="left">
			<?php echo JHTML::_('filter.search', $this->state->get('filter.search')); ?>
		</div>
		<div class="right">
			<?php echo JHTML::_('filter.owner', $this->state->get('filter.owner'), 'Owner corporations only'); ?>
		</div>
	</fieldset>
	<?php if (count($this->items)) : ?>
	<table class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th class="title" width="10px"><?php echo JText::_( 'NUM' ); ?></th>
				<th width="10" class="title">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'CORPORATION NAME' ), 'co.corporationName', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'CORPORATION TAG' ), 'co.ticker', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'ALLIANCE' ), 'al.name', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
				<th class="title"><?php echo JText::_('OWNER'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
			<td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td>
					<?php echo JHtml::_('grid.checkedout', $item, $i, 'corporationID'); ?>
				</td>
				<td>
					<?php if (JTable::isCheckedOut($userId, $item->checked_out)) : ?>
						<?php echo $this->escape($item->corporationName); ?> (<?php echo $item->corporationID; ?>)
					<?php else : ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_('Edit item');?>::<?php echo $item->corporationName; ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_eve&task=corporation.edit&corporationID='.$item->corporationID); ?>">
							<?php echo $this->escape($item->corporationName); ?></a></span> (<?php echo $item->corporationID; ?>)
					<?php endif; ?>
				</td>
				<td align="center">
					<?php echo $this->escape($item->ticker); ?>
				</td>
				<td>
					<?php echo $this->escape($item->name); ?>
				</td>
				<td align="center">
					<?php if ( $item->owner == 1 ) : ?>
						<div class="icon-16-star-yellow" style="width: 16px; height: 16px;">&nbsp;</div>
					<?php elseif ( $item->derived_owner == 1 ) : ?>
						<div class="icon-16-star-blue" style="width: 16px; height: 16px;">&nbsp;</div>
					<?php else : ?>
						&nbsp;
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php else : ?>
		<?php echo JText::_( 'NO CORPORATIONS REGISTERED' ); ?>
	<?php endif; ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>