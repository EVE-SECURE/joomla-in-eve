<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.html.pane');

$this->something = $this->params->get('showskills') == 2;
$pane = JPane::getInstance('sliders', array('allowAllClose' => 'true'));

?>
<div class="evecharsheet-skills">
<h3><?php echo JText::_('Skills'); ?></h3>
<?php if ($this->something): ?>
	<?php echo $pane->startPane("skill-pane"); ?>
<?php endif; ?>
<?php foreach ($this->groups as $group): ?>
	<?php if ($this->something): ?>
		<?php echo $pane->startPanel($this->escape($group->groupName), "skill-group-".$group->groupID); ?>
	<?php else: ?>
	<h4><?php echo $this->escape($group->groupName); ?></h4>
	<?php endif; ?>
	<?php if ($group->skills): ?>
		<table class="skill-group">
		<?php foreach ($group->skills as $skill): ?>
			<tr>
				<td class="skill-label" title="<?php echo $skill->description; ?>" >
					<?php echo $skill->typeName; ?>
				</td>
				<td class="skill-level">
					<img src="<?php echo JURI::base(); ?>components/com_evecharsheet/assets/level<?php echo $skill->level; ?>.gif" border="0" alt="Level <?php echo $skill->level; ?>" title="<?php echo number_format($skill->skillpoints); ?>" />
				</td>
			</tr>
		<?php endforeach; ?>
		</table>
		<div>
			<?php echo JText::sprintf('%s skills trained for total of %s skillpoints', $group->skillCount, number_format($group->skillpoints)); ?><br />
			<?php echo JText::sprintf('Skill Cost %s', number_format($group->skillPrice)); ?>
		</div>
	<?php else: ?>
		<?php echo JText::_('No skills in this category'); ?>
	<?php endif; ?>
	<?php if ($this->something): ?>
		<?php echo $pane->endPanel(); ?>
	<?php endif; ?>
<?php endforeach; ?>
<?php if ($this->something): ?>
	<?php echo $pane->endPane(); ?>
<?php endif; ?>
</div>