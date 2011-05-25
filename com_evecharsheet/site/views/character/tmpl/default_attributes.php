<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<div class="evecharsheet-attributes">
	<h2><?php echo JText::_('Com_Evecharsheet_Attributes'); ?></h2>
	<table>
		<?php foreach ($this->attributes as $attribute): ?>
			<tr>
				<td><?php echo $attribute->attributeName; ?></td>
				<td><?php echo $attribute->value + $attribute->augmentatorValue; ?></td>
				<td>(<?php echo $attribute->value; ?> + <?php echo $attribute->augmentatorValue; ?>)</td>
				<td><?php echo JHTML::_('evelink.type', array($attribute, 'augmentator')); ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
