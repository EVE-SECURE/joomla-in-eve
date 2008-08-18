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
		<b><?php echo $this->item->name; ?></b>
	</a>
	</td>
	<td align="center">
		<?php echo $this->item->userName; ?>
	</td>
	<td>
		<?php echo $this->item->corporationName; ?>
	</td>
	<td>
		<?php echo $this->item->allianceName; ?>
	</td>
</tr>
