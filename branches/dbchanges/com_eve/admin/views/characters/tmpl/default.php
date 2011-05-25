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
<form action="<?php echo JRoute::_('index.php?option=com_eve&view=characters'); ?>" method="post" name="adminForm">
	<fieldset if="filter-bar">
		<div class="filter-search fltlft">
			<?php echo JHTML::_('filter.search', $this->state->get('filter.search')); ?>
		</div>
		<div class="filter-select fltrt">
			<?php echo JHTML::_('filter.membersof', $this->state->get('filter.membersof')); ?>
		</div>
	</fieldset>
	<table class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th class="title" width="10px"><?php echo JText::_( 'NUM' ); ?></th>
				<th width="10" class="title">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'CHARACTER NAME' ), 'c.name', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'OWNER' ), 'userName', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'CORPORATION' ), 'co.corporationName', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'ALLIANCE' ), 'allianceName', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'API KEY STATUS' ), 'apiStatus', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td>
					<?php echo JHtml::_('grid.checkedout', $item, $i, 'characterID'); ?>
				</td>
				<td>
					<?php if (JTable::isCheckedOut($userId, $item->checked_out)) : ?>
						<?php echo $item->name; ?> (<?php echo $item->characterID; ?>)
					<?php else : ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_('Edit item');?>::<?php echo $item->name; ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_eve&task=character.edit&characterID='.$item->characterID); ?>">
							<?php echo $item->name; ?></a></span> (<?php echo $item->characterID; ?>)
					<?php endif; ?>
				</td>
				<td align="center">
					<?php echo $item->userName; ?>
				</td>
				<td>
					<?php echo $item->corporationName; ?>
				</td>
				<td>
					<?php echo $item->allianceName; ?>
				</td>
				<td>
					<?php if (!is_null($item->apiStatus)): ?>
						<span class="apiStatus apiStatus-<?php echo $item->apiStatus; ?>"><?php echo $item->apiStatus; ?></span>
					<?php endif; ?>
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