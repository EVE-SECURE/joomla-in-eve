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

//accountKey 
?>

<?php if ($pageClass) : ?>
	<div class="<?php echo $pageClass; ?>">
<?php endif; ?>
<?php if ($this->params->get('show_page_title', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
<?php endif; ?>

<form action="<?php echo EveRoute::_('corpmarketorders', $this->corporation, $this->corporation); ?>" name="adminForm" method="post">
<div>
	<?php echo JHTML::_('filter.search', $this->listState->get('filter.search')); ?>
	<?php echo JHTML::_('select.genericlist', $this->accountKeys, 'accountKey', 'class="inputbox" onchange="this.form.submit();"', 'value', 'text', $this->listState->get('filter.accountKey')); ?>
</div>
<table>
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
			<th><?php echo JHTML::_('grid.sort',  'Com_Evemarketorders_Character', 'co.name', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evemarketorders_Account_Key', 'mo.accountKey', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->items as $item) : ?>
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
			<td><?php echo JHTML::_('evelink.character', $item); ?></td>
			<td><?php echo $item->accountKey; ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php echo $this->pagination->getListFooter(); ?>
<input type="hidden" name="filter_order" value="<?php echo $this->listState->get('list.ordering', 'mo.issued'); ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listState->get('list.direction', 'desc'); ?>" />
</form>
<?php if ($pageClass) : ?>
	</div>
<?php endif; ?>