<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<tr class="<?php echo "row".$this->item->index % 2; ?>">
	<td><?php echo $this->pagination->getRowOffset( $this->item->index ); ?></td>
	<td>
		<input type="checkbox" id="cb<?php echo $this->item->index;?>" name="cid[<?php echo $this->item->index; ?>]" value="<?php echo $this->item->id; ?>" onclick="isChecked(this.checked);" />
	</td>
	<td>
		<a href="<?php echo $this->item->url; ?>">
			<b><?php echo $this->item->typeCall; ?></b>
		</a>
	</td>
	<td>
		<?php echo $this->item->next; ?>
	</td>
	<td>
		<?php if ($this->item->userID): ?>
			<?php echo $this->item->userName; ?> (<?php echo $this->item->userID; ?>)
		<?php endif; ?>
	</td>
	<td>
		<?php if ($this->item->characterID): ?>
			<?php echo $this->item->characterName; ?> (<?php echo $this->item->characterID; ?>)
		<?php endif; ?>
	</td>
	<td align="center">
		<?php echo JHTML::_('grid.published', $this->item, $this->item->index); ?>
	</td>
</tr>
