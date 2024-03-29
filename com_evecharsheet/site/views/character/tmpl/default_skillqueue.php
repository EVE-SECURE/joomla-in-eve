<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<div class="evecharsheet-skillqueue">
	<h2><?php echo JText::_('Com_Evecharsheet_Skill_Queue'); ?></h2>
	<table>
	<?php foreach ($this->queue as $skill): ?>
		<tr>
			<td>
				<?php echo $skill->queuePosition + 1; ?>
			</td>
			<td class="skill-label" title="<?php echo $skill->description; ?>" >
				<?php echo $skill->typeName; ?>
			</td>
			<td class="skill-level">
				<?php echo JHTML::image('media/com_evecharsheet/images/level'.$skill->level.'.gif', 'Level '.$skill->level, 'title="'.number_format($skill->endSP).'"'); ?>
			</td>
			<td>
				<?php echo JHTML::_('date', $skill->startTime, JText::_('DATE_FORMAT_LC2')); ?>
			</td>
			<td>
				<?php echo JHTML::_('date', $skill->endTime, JText::_('DATE_FORMAT_LC2')); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
</div>
