<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>

<div class="evecharsheet-certificates">
<h3><?php echo JText::_('Certificates'); ?></h3>
<a class="expand-all-certificates" href="#" title="<?php echo JText::_('Expand all certificates'); ?>">
	<?php echo JText::_('Expand all certificates'); ?>
</a> 
| 
<a class="collapse-all-certificates" href="#" title="<?php echo JText::_('Collapse all certificates'); ?>">
	<?php echo JText::_('Collapse all certificates'); ?>
</a>

<?php foreach ($this->categories as $category): ?>
	<h4><?php echo $this->escape($category->categoryName); ?></h4>
	<?php if ($category->certificates): ?>
		<table class="certificate-category">
		<?php foreach ($category->certificates as $certificate): ?>
			<tr>
				<td class="certificate-label" title="<?php echo $certificate->description; ?>" >
					<?php echo $certificate->className; ?>
				</td>
				<td class="certificate-level">
					<img src="<?php echo JURI::base(); ?>components/com_evecharsheet/assets/level<?php echo $certificate->grade; ?>.gif" border="0" alt="Grate <?php echo $certificate->grade; ?>" title="<?php echo number_format($certificate->grade); ?>" />
				</td>
			</tr>
		<?php endforeach; ?>
		</table>
		<div>
			<?php echo JText::sprintf('%s certificates in this category', count($category->certificates)); ?>
		</div>
	<?php else: ?>
		<div class="certificate-category">
		</div>
			<?php echo JText::_('No certificates in this category'); ?>
	<?php endif; ?>
<?php endforeach; ?>
</div>
