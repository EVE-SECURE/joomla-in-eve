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
		<input type="checkbox" id="cb<?php echo $this->item->index;?>" name="cid[<?php echo $this->item->index; ?>]" value="<?php echo $this->item->corporationID; ?>" onclick="isChecked(this.checked);" />
	</td>
	<td>
		<a href="<?php echo $this->item->url; ?>">
			<b><?php echo $this->item->corporationName; ?></b>
		</a>
	</td>
	<td align="center">
		<?php echo $this->item->ticker; ?>
	</td>
	<td>
		<?php echo $this->item->name; ?>
	</td>
	<td>
		<?php echo $this->item->derived_standings; ?>
	</td>
	<td align="center">
		<?php if ( $this->item->owner == 1 ) : ?>
		<div class="icon-16-star-yellow" style="width: 16px; height: 16px;">&nbsp;</div>
		<?php elseif ( $this->item->derived_owner == 1 ) : ?>
		<div class="icon-16-star-blue" style="width: 16px; height: 16px;">&nbsp;</div>
		<?php else : ?>
		&nbsp;
		<?php endif; ?>
	</td>
	
</tr>
