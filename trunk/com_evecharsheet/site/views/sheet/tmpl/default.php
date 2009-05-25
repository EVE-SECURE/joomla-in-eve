<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
<?php if ($this->params->get('show_page_title', 1)) : ?>
<div class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
	<?php echo $this->escape($this->title); ?>
</div>
<?php endif; ?>

<?php if ($this->show_owner): ?>
	<div>
		<?php echo JText::_('Owner of character'); ?>:
			<a href="<?php echo JRoute::_('index.php?option=com_evecharsheet&view=list&layout=owner&owner='.$this->owner->id); ?>">
				<?php echo $this->owner->name; ?>
			</a>
			<br />
		<?php if ($this->owners_chars): ?>
			<?php echo JText::_('Other characters'); ?>: 
			<?php foreach ($this->owners_chars as $character): ?>
				<a href="<?php echo JRoute::_('index.php?option=com_evecharsheet&view=sheet&characterID='.$character->characterID); ?>">
					<?php echo $character->name; ?>
				</a>, 
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
<?php endif; ?>

<div>
	<img src="http://img.eve.is/serv.asp?s=256&c=<?php echo $this->character->characterID; ?>" /> <br />
	<?php echo JText::_('Character Name'); ?>: <?php echo $this->character->name; ?> <br />
	<?php echo JText::_('Race'); ?>: <?php echo $this->character->race; ?> <br />
	<?php echo JText::_('Gender'); ?>: <?php echo $this->character->gender; ?> <br />
	<?php echo JText::_('Blood Line'); ?>: <?php echo $this->character->bloodLine; ?> <br />
	<?php echo JText::_('Ballance'); ?>: <?php echo number_format($this->character->balance); ?> <br />
	<?php echo JText::_('Corporation'); ?>: 
		<a href="<?php echo JRoute::_('index.php?option=com_evecharsheet&view=list&layout=corporation&corporationID='.$this->corporation->corporationID); ?>">
			<?php echo $this->corporation->corporationName; ?> [<?php echo $this->corporation->ticker; ?>]
		</a> <br />
</div>

<div>
	<h3><?php echo JText::_('Skill Queue'); ?></h3>
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
				<img src="<?php echo JURI::base(); ?>components/com_evecharsheet/assets/level<?php echo $skill->level; ?>.gif" border="0" alt="Level <?php echo $skill->level; ?>" title="<?php echo number_format($skill->endSP); ?>" />
			</td>
			<td>
				<?php echo $skill->startTime; ?>
			</td>
			<td>
				<?php echo $skill->endTime; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
</div>

<div>
<?php foreach ($this->groups as $group): ?>
	<h3>
	<?php echo $group->groupName; ?>
	</h3>
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
<?php endforeach; ?>
</div>
