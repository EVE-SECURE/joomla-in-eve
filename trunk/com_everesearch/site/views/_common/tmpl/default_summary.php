<?php
/**
 * @version		$Id$
 */
?>
<table class="summary">
	<caption><?php echo JText::_('Com_Everesearch_Summary'); ?></caption>
	<thead>
		<tr>
			<th><?php echo JText::_('Com_Everesearch_Research_Field'); ?></th>
			<th class="number"><?php echo JText::_('Com_Everesearch_Current_Points'); ?></th>
			<th class="number"><?php echo JText::_('Com_Everesearch_Points_Per_Day'); ?></th>
			<th class="number"><?php echo JText::_('Com_Everesearch_Current_Datacores'); ?></th>
			<th class="number"><?php echo JText::_('Com_Everesearch_Datacores_Per_Day'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->summary as $item) : ?>
		<tr>
			<td><?php echo $this->escape($item->skillTypeName); ?></td>
			<td class="number"><?php echo number_format($item->currentPoints, 2); ?></td>
			<td class="number"><?php echo number_format($item->pointsPerDay, 2); ?></td>
			<td class="number"><?php echo number_format($item->currentDatacores, 2); ?></td>
			<td class="number"><?php echo number_format($item->datacoresPerDay, 2); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
