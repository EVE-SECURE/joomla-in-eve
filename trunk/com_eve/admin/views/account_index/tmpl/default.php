<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<form action="index.php?option=com_eve&amp;control=account" method="post" name="adminForm">
	<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JHTML::_('filter.search', $this->filter_search); ?>
			</td>
			<td nowrap="nowrap">
				<?php
				?>
			</td>
		</tr>
	</table>
	<?php if (count($this->items)) : ?>
	<table class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th class="title" width="10px"><?php echo JText::_( 'NUM' ); ?></th>
				<th width="10" class="title">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'USER ID' ), 'u.userID', $this->filter_order_dir, $this->filter_order); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'OWNER' ), 'nameName', $this->filter_order_dir, $this->filter_order); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_( 'API KEY STATUS' ), 'apiStatus', $this->filter_order_dir, $this->filter_order); ?></th>
				<th class="title"><?php echo JText::_('CHARACTERS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php for ($i=0, $n=count($this->items), $rc=0; $i < $n; $i++, $rc = 1 - $rc) : ?>
			<?php
				$this->loadItem($i);
				echo $this->loadTemplate('item');
			?>
		<?php endfor; ?>
		</tbody>
	</table>
	<?php else : ?>
		<?php echo JText::_( 'NO USERS REGISTERED' ); ?>
	<?php endif; ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filter_order;?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_dir; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>