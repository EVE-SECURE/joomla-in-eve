<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHtml::_('behavior.tooltip');
$user	= &JFactory::getUser();
$userId	= $user->get('id');
?>
<form action="<?php echo JRoute::_('index.php?option=com_eve&view=accounts'); ?>" method="post" name="adminForm">
	<fieldset class="filter">
		<div class="left">
			<?php echo JHTML::_('filter.search', $this->state->get('filter.search')); ?>
		</div>
		<div class="right">
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
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'USER ID' ), 'u.userID', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'OWNER' ), 'userName', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'API KEY STATUS' ), 'apiStatus', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
				<th class="title"><?php echo JText::_('CHARACTERS'); ?></th>
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
					<?php echo JHtml::_('grid.checkedout', $item, $i, 'userID'); ?>
				</td>
				<td>
					<?php if (JTable::isCheckedOut($userId, $item->checked_out)) : ?>
						<?php echo $item->userID; ?>
					<?php else : ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_('Edit item');?>::<?php echo $item->userID; ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_eve&task=account.edit&userID='.$item->userID); ?>">
							<?php echo $item->userID; ?></a></span>
					<?php endif; ?>
				</td>
				<td align="center">
					<?php echo $item->userName; ?>
				</td>
				<td>
					<span class="apiStatus apiStatus-<?php echo $item->apiStatus; ?>"><?php echo $item->apiStatus; ?></span>
					
				</td>
				<td>
					<?php echo $item->characters; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php else : ?>
		<?php echo JText::_( 'NO USERS REGISTERED' ); ?>
	<?php endif; ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>