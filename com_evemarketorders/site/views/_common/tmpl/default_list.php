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
			<th><?php echo JHTML::_('grid.sort',  'Com_Evemarketorders_Order_ID', 'mo.orderID', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evemarketorders_Item_Type', 'inv.typeName', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evemarketorders_Volume_Entered', 'mo.volEntered', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evemarketorders_Volume_Remaining', 'mo.volRemaining', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evemarketorders_Volume_Min', 'mo.minVolume', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evemarketorders_Price', 'mo.price', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evemarketorders_Escrow', 'mo.escrow', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evemarketorders_Order_State', 'mo.orderState', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evemarketorders_Bid_Type', 'mo.bid', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evemarketorders_Station_Name', 'sta.stationName', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evemarketorders_Range', 'mo.range', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evemarketorders_Duration', 'mo.duration', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evemarketorders_Issued', 'mo.issued', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<?php if ($this->state->get('entity') == 'user'): ?>
				<th><?php echo JHTML::_('grid.sort',  'Com_Evemarketorders_Character', 'characterName', 
						$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->items as $item): ?>
		<tr>
			<td><?php echo $item->orderID; ?></td>
			<td><?php echo JHTML::_('evelink.type', $item); ?>
			<td><?php echo number_format($item->volEntered, 0); ?>
			<td><?php echo number_format($item->volRemaining, 0); ?>
			<td><?php echo number_format($item->minVolume, 0); ?>
			<td><?php echo number_format($item->price, 2); ?>
			<td><?php echo number_format($item->escrow, 2); ?>
			<td><?php echo $this->orderStateName($item->orderState); ?>
			<td><?php echo $this->bidName($item->bid); ?>
			<td><?php echo JHTML::_('evelink.station', $item); ?>
			<td><?php echo $this->rangeName($item->range); ?>
			<td><?php echo $item->duration; ?>
			<td><?php echo JHTML::date($item->issued); ?></td>

			<?php if ($this->state->get('entity') == 'user'): 
				$character = $this->characters[$item->entityID];
				?>
				<td>
					<a href="<?php echo EveRoute::_('charmarketorders', $character, $character, $character); ?>">
						<?php echo $this->escape($item->characterName); ?>
					</a>
				</td>
			<?php endif; ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>