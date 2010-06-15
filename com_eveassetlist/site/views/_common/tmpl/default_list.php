<?php
/**
 * @version		$Id$
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
<table class="list">
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
			<?php if ($this->state->get('entity') == 'user'): ?>
				<th><?php echo JHTML::_('grid.sort',  'Com_Eveassetlist_Character', 'characterName', 
						$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->items as $item): ?>
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


			<?php if ($this->state->get('entity') == 'user'): 
				$character = $this->characters[$item->entityID];
				?>
				<td>
					<a href="<?php echo EveRoute::_('charassetlist', $character, $character, $character); ?>">
						<?php echo $this->escape($item->characterName); ?>
					</a>
				</td>
			<?php endif; ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>