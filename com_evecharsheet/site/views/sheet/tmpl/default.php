<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
<?php if ($this->params->get('show_page_title')) : ?>
<div class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
	<?php echo $this->escape($this->params->get('page_title')); ?>
</div>
<?php endif; ?>

<div>
	<img src="http://img.eve.is/serv.asp?s=256&c=<?php echo $this->character->characterID; ?>" /> <br />
	<?php echo JText::_('Character Name'); ?>: <?php echo $this->character->name; ?> <br />
	<?php echo JText::_('Race'); ?>: <?php echo $this->character->race; ?> <br />
	<?php echo JText::_('Gender'); ?>: <?php echo $this->character->gender; ?> <br />
	<?php echo JText::_('Blood Line'); ?>: <?php echo $this->character->bloodLine; ?> <br />
	<?php echo JText::_('Ballance'); ?>: <?php echo number_format($this->character->balance); ?> <br />
	<?php echo JText::_('Corporation'); ?>: <?php echo $this->corporation->corporationName; ?>
		 [<?php echo $this->corporation->ticker; ?>] <br />
</div>

<div>
<?php foreach ($this->groups as $group): ?>
	<h3>
	<?php echo $group->groupName; ?>
	</h3>
	<table class="skill-group">
	<?php foreach ($group->skills as $skill): ?>
		<tr>
			<td class="skill-label">
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
	<?php endforeach; ?>
</div>
