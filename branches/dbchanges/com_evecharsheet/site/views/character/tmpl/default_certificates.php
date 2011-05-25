<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>

<div class="evecharsheet-certificates">
<h2><?php echo JText::_('Com_Evecharsheet_Certificates'); ?></h2>
<a class="expand-all-certificates" href="#" title="<?php echo JText::_('Com_Evecharsheet_Expand_All_Certificates'); ?>">
	<?php echo JText::_('Com_Evecharsheet_Expand_All_Certificates'); ?>
</a> 
| 
<a class="collapse-all-certificates" href="#" title="<?php echo JText::_('Com_Evecharsheet_Collapse_All_Certificates'); ?>">
	<?php echo JText::_('Com_Evecharsheet_Collapse_All_Certificates'); ?>
</a>

<?php foreach ($this->categories as $category): ?>
	<div class="heading <?php echo preg_replace('/[^a-z]/', '', strtolower($category->categoryName)); ?>">
		<h3><?php echo $this->escape($category->categoryName); ?></h3>
	</div>
	<?php if ($category->certificates): ?>
		<table class="certificate-category">
		<?php foreach ($category->certificates as $certificate): ?>
			<tr>
				<td class="certificate-label hasTip" title="<?php echo $certificate->description; ?>" >
					<?php echo $certificate->className; ?>
				</td>
				<td class="certificate-level">
					<?php echo JHTML::image('media/com_evecharsheet/images/level'.$certificate->grade.'.gif', 'Grade '.$certificate->grade, 'title="'.number_format($certificate->grade).'"'); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</table>
		<div>
			<?php echo JText::sprintf('Com_Evecharsheet_N_Certificates_In_Category', count($category->certificates)); ?>
		</div>
	<?php else: ?>
		<div class="certificate-category">
		</div>
			<?php echo JText::_('Com_Evecharsheet_0_Certificates_In_Category'); ?>
	<?php endif; ?>
<?php endforeach; ?>
</div>
