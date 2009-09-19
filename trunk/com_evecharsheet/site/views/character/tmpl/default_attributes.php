<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<div class="evecharsheet-attributes">
	<h3><?php echo JText::_('Attributes'); ?></h3>
	<table>
		<?php foreach ($this->attributes as $attribute): ?>
			<tr>
				<td><?php echo $attribute->attributeName; ?></td>
				<td><?php echo $attribute->value; ?> + <?php echo $attribute->augmentatorValue; ?></td>
				<td><?php echo $attribute->augmentatorName; ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
