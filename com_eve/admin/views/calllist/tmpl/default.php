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
<form
	action="<?php echo JRoute::_('index.php?option=com_eve&view=calllist'); ?>"
	method="post" name="adminForm">
<fieldset if="filter-bar">
<div class="filter-search fltlft"><?php echo JHTML::_('filter.search', $this->state->get('filter.search')); ?>
</div>
<div class="filter-select fltrt"></div>
</fieldset>
<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th class="title" width="10px"><?php echo JText::_( 'NUM' ); ?></th>
			
			<?php /*<th width="10" class="title"><input type="checkbox" name="toggle"
				value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
			</th>*/?>
			<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'Com_Eve_Call_Type' ), 'typeName', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'Com_Eve_Call_Name' ), 'cl.name', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'Com_Eve_Access_Mask' ), 'cl.accessMask', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
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
			<?php /*<td><?php echo JHtml::_('grid.checkedout', $item, $i, 'id'); ?></td>*/ ?>
			<td><?php echo $this->escape($item->type); ?></td>
			<td><?php echo $this->escape($item->name); ?></td>
			<td><?php echo $this->escape($item->accessMask); ?></td>
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
