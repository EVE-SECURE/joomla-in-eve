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
<form action="<?php echo JRoute::_('index.php?option=com_eve&view=access'); ?>" method="post" name="adminForm">
	<fieldset class="filter">
		<div class="left">
			<?php echo JHTML::_('filter.search', $this->state->get('filter.search')); ?>
		</div>
		<div class="right">
		</div>
	</fieldset>
	<table class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th class="title" width="10px"><?php echo JText::_( 'NUM' ); ?></th>
				<th width="10" class="title">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', JText::_('Name'), 'se.name', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', JText::_('Title'), 'se.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', JText::_('Alias'), 'se.alias', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th>
					<?php echo JText::_('Access Level') ?>
				</th>
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
					<?php echo JHTML::_('grid.id', $i, $item->id ); ?>
					<input type="hidden" name="access[<?php echo $i; ?>][id]" value="<?php echo $item->id; ?>" />
				</td>
				<td>
					<?php echo $item->name; ?>
				</td>
				<td>
					<?php echo $item->title; ?>
				</td>
				<td>
					<?php echo $item->alias; ?>
				</td>
				
				<td>
					<?php if ($item->entity == 'character'): ?>
						<?php echo JHTML::_('select.genericlist', $this->characterGroups, 'access['.$i.'][access]', '', 'value', 'text', $item->access, 'access_'.$i.'_access', true); ?>
					<?php elseif ($item->entity == 'corporation'): ?>
						<?php echo JHTML::_('select.genericlist', $this->corporationGroups, 'access['.$i.'][access]', '', 'value', 'text', $item->access, 'access_'.$i.'_access', true); ?>
					<?php else: ?>
						<?php echo JHTML::_('select.genericlist', $this->groups, 'access['.$i.'][access]', '', 'value', 'text', $item->access, 'access_'.$i.'_access', true); ?>
					<?php endif; ?>
					<?php if ($item->entity == 'corporation'): ?>
						<input type="button" class="" onclick="listItemTask('cb<?php echo $i; ?>', 'roles.editsection')" value="<?php echo JText::_('Edit Roles'); ?>"/>
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
	<?php echo JHTML::_('form.token'); ?>
</form>