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

<form action="<?php echo EveRoute::_('charwalletjournal', $this->character, $this->character, $this->character); ?>" name="adminForm" method="post">
<div>
	<?php echo JHTML::_('filter.search', $this->listState->get('filter.search')); ?>
	<?php echo JHTML::_('select.genericlist', $this->refTypes, 'refTypeID', 'class="inputbox" onchange="this.form.submit();"', 'refTypeID', 'refTypeName', $this->listState->get('filter.refTypeID')); ?>
</div>
<table>
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
			<th><?php echo JHTML::_('grid.sort',  'Com_Evewalletjournal_Amount', 'wj.amount', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evewalletjournal_Ballance', 'wj.balance', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evewalletjournal_Reason', 'wj.reason', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->items as $item) : ?>
		<tr>
			<td><?php echo $item->refID; ?></td>
			<td><?php echo $this->escape($item->refTypeName); ?></td>
			<td><?php echo JHTML::date($item->date); ?></td>
			<td><?php echo $this->escape($item->ownerName1); ?></td>
			<td><?php echo $this->escape($item->ownerName2); ?></td>
			<td><?php echo $this->getArgument($item); ?></td>
			<td><?php echo number_format($item->amount, 2); ?></td>
			<td><?php echo number_format($item->balance, 2); ?></td>
			<td><?php echo $this->getReason($item); ?></td>
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