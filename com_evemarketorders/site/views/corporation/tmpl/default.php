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

<form action="<?php echo EveRoute::_('corpmarketorders', $this->corporation, $this->corporation); ?>" name="adminForm" method="post">
<div>
	<label for="accountKey">
		<?php echo JText::_('Com_Evemarketorders_Account_Key'); ?>:
	</label>
	<?php echo JHTML::_('select.genericlist', $this->accountKeys, 'accountKey', 'onchange="this.form.submit();"', 'value', 'text', $this->listState->get('filter.accountKey')); ?>
	<?php echo JHTML::_('filter.search', $this->listState->get('filter.search')); ?>
</div>
<table>
	<thead>
		<tr>
			<th><?php echo JHTML::_('grid.sort',  'Com_Evemarketorders_Issued', 'mo.issued', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>

		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->items as $item) : ?>
		<tr>
			<td><?php echo JHTML::date($item->issued); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php echo $this->pagination->getListFooter(); ?>
<input type="hidden" name="filter_order" value="<?php echo $this->listState->get('list.ordering', 'mo.issued'); ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listState->get('list.direction', 'desc'); ?>" />
</form>