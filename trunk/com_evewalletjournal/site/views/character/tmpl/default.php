<?php
?>
<table>
<?php foreach ($this->items as $item) : ?>
	<tr>
		<td><?php echo $item->refID; ?></td>
		<td><?php echo JHTML::date($item->date); ?></td>
		<td><?php echo $item->amount; ?></td>
		<td><?php echo $item->balance; ?></td>
	</tr>
<?php endforeach; ?>
</table>
<?php echo $this->pagination->getListFooter(); ?>