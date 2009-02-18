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
		<?php echo JHTML::_('grid.id',$this->item->index, $this->item->allianceID ); ?>
	</td>
	<td>		
		<a href="<?php echo $this->item->url; ?>">
			<b><?php echo $this->item->name; ?></b>
		</a>
	</td>
	<td>
		<?php echo $this->item->shortName; ?>
	</td>
	<td>
		<?php echo $this->item->standings; ?>
	</td>
	<td align="center">
		<?php if ( $this->item->owner == 1 ) : ?>
		<div class="icon-16-star-yellow" style="width: 16px; height: 16px;">&nbsp;</div>
		<?php else : ?>
		&nbsp;
		<?php endif; ?>
	</td>
</tr>
