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
JHTML::stylesheet('component.css', 'media/com_everesearch/css/');

?>

<?php if ($pageClass) : ?>
	<div class="<?php echo $pageClass; ?>">
<?php endif; ?>
<?php if ($this->params->get('show_page_title', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
<?php endif; ?>

<form action="<?php echo EveRoute::_('charresearch'); ?>" name="adminForm" method="post">
<div>
	<?php echo JHTML::_('filter.search', $this->listState->get('filter.search')); ?>
</div>
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
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<?php echo $this->pagination->getListFooter(); ?>
<input type="hidden" name="filter_order" value="<?php echo $this->listState->get('list.ordering', 'agentName'); ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listState->get('list.direction', 'asc'); ?>" />
</form>

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
<?php if ($pageClass) : ?>
	</div>
<?php endif; ?>