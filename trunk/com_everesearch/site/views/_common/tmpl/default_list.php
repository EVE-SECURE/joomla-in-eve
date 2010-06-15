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
			<th><?php echo JHTML::_('grid.sort',  'Com_Everesearch_Agent_Name', 'agentName', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Everesearch_Research_Field', 'skillTypeName', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th class="number"><?php echo JHTML::_('grid.sort',  'Com_Everesearch_Current_Points', 'currentPoints', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th class="number"><?php echo JHTML::_('grid.sort',  'Com_Everesearch_Points_Per_Day', 're.pointsPerDay', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th class="number"><?php echo JHTML::_('grid.sort',  'Com_Everesearch_Current_Datacores', 'currentDatacores', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th class="number"><?php echo JHTML::_('grid.sort',  'Com_Everesearch_Datacores_Per_Day', 'datacoresPerDay', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th class="number"><?php echo JHTML::_('grid.sort',  'Com_Everesearch_Agent_Level', 'agt.level', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th class="number"><?php echo JHTML::_('grid.sort',  'Com_Everesearch_Agent_Quality', 'agt.quality', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Everesearch_Agent_Location', 'sta.stationName', 
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
			<td><?php echo $this->escape($item->agentName); ?></td>
			<td><?php echo $this->escape($item->skillTypeName); ?></td>
			<td class="number"><?php echo number_format($item->currentPoints, 2); ?></td>
			<td class="number"><?php echo number_format($item->pointsPerDay, 2); ?></td>
			<td class="number"><?php echo number_format($item->currentDatacores, 2); ?></td>
			<td class="number"><?php echo number_format($item->datacoresPerDay, 2); ?></td>
			<td class="number"><?php echo $item->level; ?></td>
			<td class="number"><?php echo $item->quality; ?></td>
			<td><?php echo $item->stationName; ?></td>

			<?php if ($this->state->get('entity') == 'user'): 
				$character = $this->characters[$item->characterID];
				?>
				<td>
					<a href="<?php echo EveRoute::_('charresearch', $character, $character, $character); ?>">
						<?php echo $this->escape($item->characterName); ?>
					</a>
				</td>
			<?php endif; ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>