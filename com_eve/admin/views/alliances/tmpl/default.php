<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHtml::_('behavior.tooltip');
$user	= &JFactory::getUser();
$userId	= $user->get('id');

JHTML::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'html')
?>
<form action="<?php echo JRoute::_('index.php?option=com_eve&view=alliances'); ?>" method="post" name="adminForm">
	<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JHTML::_('filter.search', $this->state->get('filter_search')); ?>
			</td>
			<td nowrap="nowrap">
				<?php echo JHTML::_('filter.owner', $this->state->get('filter.owner'), 'something'); ?>
			</td>
		</tr>
	</table>
	<?php if (count($this->items)) : ?>
	<table class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th class="title" width="10px"><?php echo JText::_( 'NUM' ); ?></th>
				<th width="10" class="title">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_('ALLIANCE NAME'), 'al.name', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_('ALLIANCE TAG'), 'al.shortName', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
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
					<?php echo JHtml::_('grid.checkedout', $item, $i, 'allianceID'); ?>
				</td>
				<td>
					<?php if (JTable::isCheckedOut($userId, $item->checked_out)) : ?>
						<?php echo $item->name; ?> (<?php echo $item->allianceID; ?>)
					<?php else : ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_('Edit item');?>::<?php echo $item->name; ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_eve&task=alliance.edit&allianceID='.$item->allianceID); ?>">
							<?php echo $item->name; ?></a></span> (<?php echo $item->allianceID; ?>)
					<?php endif; ?>
				</td>
				<td>
					<?php echo $item->shortName; ?>
				</td>
				<td align="center">
					<?php if ( $item->owner == 1 ) : ?>
						<div class="icon-16-star-yellow" style="width: 16px; height: 16px;">&nbsp;</div>
					<?php else : ?>
						&nbsp;
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php else : ?>
		<?php echo JText::_( 'NO ALLIANCES REGISTERED' ); ?>
	<?php endif; ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>