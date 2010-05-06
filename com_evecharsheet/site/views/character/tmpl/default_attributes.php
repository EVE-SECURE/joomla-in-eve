<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<div class="evecharsheet-attributes">
	<h2><?php echo JText::_('Attributes'); ?></h2>
	<table>
		<?php foreach ($this->attributes as $attribute): ?>
			<tr>
				<td><?php echo $attribute->attributeName; ?></td>
				<td><?php echo ($attribute->value + $attribute->skillValue + $attribute->augmentatorValue) * $attribute->skillMultiplier; ?></td>
				<td><?php echo $attribute->skillMultiplier; ?> x (<?php echo $attribute->value; ?> + <?php echo $attribute->skillValue; ?> + <?php echo $attribute->augmentatorValue; ?>)</td>
				<td><?php echo JHTML::_('evelink.type', array($attribute, 'augmentator')); ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
