<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>

<div class="evecharsheet-skills">
<h2><?php echo JText::_('Com_Evecharsheet_Skills'); ?></h2>

<a class="expand-all-skills" href="#" title="<?php echo JText::_('Com_Evecharsheet_Expand_All_Skills'); ?>">
	<?php echo JText::_('Com_Evecharsheet_Expand_All_Skills'); ?>
</a> 
| 
<a class="collapse-all-skills" href="#" title="<?php echo JText::_('Com_Evecharsheet_Collapse_All_Skills'); ?>">
	<?php echo JText::_('Com_Evecharsheet_Collapse_All_Skills'); ?>
</a>


<?php foreach ($this->groups as $group): ?>
	<div class="heading <?php echo preg_replace('/[^a-z]/', '', strtolower($group->groupName)); ?>">
		<h3><?php echo $this->escape($group->groupName); ?><h3>
	</div>
	<?php if ($group->skills): ?>
		<table class="skill-group">
		<?php foreach ($group->skills as $skill): ?>
			<tr>
				<td class="skill-label" title="<?php echo $skill->description; ?>" >
					<?php echo $skill->typeName; ?>
				</td>
				<td class="skill-level">
					<img src="<?php echo JURI::base(); ?>media/com_evecharsheet/images/level<?php echo $skill->level; ?>.gif" border="0" alt="Level <?php echo $skill->level; ?>" title="<?php echo number_format($skill->skillpoints); ?>" />
				</td>
			</tr>
		<?php endforeach; ?>
		</table>
		<div>
			<?php echo JText::sprintf('Com_Evecharsheet_N_Skills_M_Skillpoints', $group->skillCount, number_format($group->skillpoints)); ?><br />
			<?php echo JText::sprintf('Com_Evecharsheet_Skill_Cost_N', number_format($group->skillPrice)); ?>
		</div>
	<?php else: ?>
		<div class="skill-group">
		</div>
		<div>
			<?php echo JText::_('Com_Evecharsheet_No_Skills_In_Category'); ?>
		</div>
	<?php endif; ?>
<?php endforeach; ?>
</div>