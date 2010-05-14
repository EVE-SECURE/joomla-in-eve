<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'helpers'.DS.'html');
JHTML::_('behavior.mootools');
JHTML::_('eve.contextmenu');
$pageClass = $this->params->get('pageclass_sfx');
?>

<?php if ($pageClass) : ?>
	<div class="<?php echo $pageClass; ?>">
<?php endif; ?>
<?php if ($this->params->get('show_page_title', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
<?php endif; ?>

<form action="<?php echo EveRoute::_('charassetlist', $this->character, $this->character, $this->character); ?>" name="adminForm" method="post">
<table>
	<thead>
		<tr>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveassetlist_Item_ID', 'al.itemID', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveassetlist_Item_Type', 'inv.typeName', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveassetlist_Quantity', 'al.quantity', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveassetlist_Location', 'locationName', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveassetlist_Flag', 'fla.flagText', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveassetlist_Containter', 'containerTypeName', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->items as $item) : ?>
		<tr>
			<td><?php echo $item->itemID; ?></td>
			<td><?php echo JHTML::_('evelink.type', $item); ?>
			<td>
				<?php if ($item->singleton): ?>
					<?php echo JText::_('Com_Eveassetlist_Singleton'); ?>
				<?php else: ?>
					<?php echo number_format($item->quantity); ?>
				<?php endif; ?>
			</td>
			<td><?php echo $item->locationName; ?></td>
			<td><?php echo $item->flagText; ?></td>
			<td>
				<?php if ($item->containerID): ?>
					<?php echo JHTML::_('evelink.type', array($item, 'containerType')); ?>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php echo $this->pagination->getListFooter(); ?>
<input type="hidden" name="filter_order" value="<?php echo $this->listState->get('list.ordering', 'wj.refID'); ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listState->get('list.direction', 'desc'); ?>" />
</form>
<?php if ($pageClass) : ?>
	</div>
<?php endif; ?>