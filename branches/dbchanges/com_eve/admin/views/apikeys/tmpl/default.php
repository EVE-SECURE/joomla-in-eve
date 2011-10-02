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
<form action="<?php echo JRoute::_('index.php?option=com_eve&view=apikeys'); ?>" method="post" name="adminForm">
	<fieldset if="filter-bar">
		<div class="filter-search fltlft">
			<?php echo JHTML::_('filter.search', $this->state->get('filter.search')); ?>
		</div>
		<div class="filter-select fltrt">
		</div>
	</fieldset>
	<table class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th class="title" width="10px"><?php echo JText::_( 'NUM' ); ?></th>
				<th width="10" class="title">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'Com_Eve_Key_Id' ), 'u.keyID', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'Com_Eve_User' ), 'userName', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'Com_Eve_Api_Key_Status' ), 'status', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
				<th class="title"><?php echo JText::_('Com_Eve_Api_Key_Entities'); ?></th>
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
					<?php echo JHtml::_('grid.checkedout', $item, $i, 'keyID'); ?>
				</td>
				<td>
					<?php if (JTable::isCheckedOut($userId, $item->checked_out)) : ?>
						<?php echo $item->keyID; ?>
					<?php else : ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_('Edit item');?>::<?php echo $item->keyID; ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_eve&task=account.edit&keyID='.$item->keyID); ?>">
							<?php echo $item->keyID; ?></a></span>
					<?php endif; ?>
				</td>
				<td align="center">
					<?php echo $this->escape($item->userName); ?>
				</td>
				<td>
					<span class="apiStatus apiStatus-<?php echo $item->status; ?>"><?php echo $item->status; ?></span>
				</td>
				<td>
					<?php echo $this->escape($item->characters); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>