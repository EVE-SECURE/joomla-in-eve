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
			<th><?php echo JHTML::_('grid.sort',  'Com_Evewalletjournal_Ref_ID', 'wj.refID', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evewalletjournal_Ref_Type', 'rt.refTypeName', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evewalletjournal_Date', 'wj.date', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evewalletjournal_Owner1', 'wj.ownerName1', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evewalletjournal_Owner2', 'wj.ownerName2', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evewalletjournal_Argument', 'wj.argName1', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th class="number"><?php echo JHTML::_('grid.sort',  'Com_Evewalletjournal_Amount', 'wj.amount', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th class="number"><?php echo JHTML::_('grid.sort',  'Com_Evewalletjournal_Ballance', 'wj.balance', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evewalletjournal_Reason', 'wj.reason', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
					
			<?php if ($this->state->get('entity') == 'user'): ?>
				<th><?php echo JHTML::_('grid.sort',  'Com_Everesearch_Character', 'characterName', 
						$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->items as $item): ?>
		<tr>
			<td><?php echo $item->refID; ?></td>
			<td><?php echo $this->escape($item->refTypeName); ?></td>
			<td><?php echo JHTML::date($item->date); ?></td>
			<td><?php echo $this->escape($item->ownerName1); ?></td>
			<td><?php echo $this->escape($item->ownerName2); ?></td>
			<td><?php echo $this->getArgument($item); ?></td>
			<td class="number"><?php echo number_format($item->amount, 2); ?></td>
			<td class="number"><?php echo number_format($item->balance, 2); ?></td>
			<td><?php echo $this->getReason($item); ?></td>

			<?php if ($this->state->get('entity') == 'user'): 
				$character = $this->characters[$item->entityID];
				?>
				<td>
					<a href="<?php echo EveRoute::_('charwalletjournal', $character, $character, $character); ?>">
						<?php echo $this->escape($item->characterName); ?>
					</a>
				</td>
			<?php endif; ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>