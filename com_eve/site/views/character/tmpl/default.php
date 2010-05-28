<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'helpers'.DS.'html');
JHTML::_('eve.contextmenu');
?>

<?php if ($this->params->get('show_page_title')) : ?>
<div class="componentheading<?php echo $this->params->get('pageclass_sfx'); ?>">
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
</div>

<div>
	<?php echo JText::_('Corporation'); ?>:
		<?php echo JHTML::_('evelink.corporation', $this->character); ?>
</div>

<?php if ($this->character->allianceID) : ?>
	<div>
		<?php echo JText::_('Alliance'); ?>:
			<?php echo JHTML::_('evelink.alliance', $this->character); ?>
	</div>
<?php endif; ?>

<div>
	<?php foreach ($this->components as $component): ?>
		<a href="<?php echo EveRoute::_($component->name, $this->character, $this->character, $this->character); ?>">
			<?php echo JText::_($component->title); ?>
		</a> <br />
	<?php endforeach; ?>
</div>

<?php if (is_array($this->apischedule) && $this->apischedule): ?>
	<?php echo $this->loadTemplate('apischedule'); ?>
<?php endif; ?>

<?php if (is_array($this->sectionaccess) && $this->sectionaccess): ?>
	<?php echo $this->loadTemplate('sectionaccess'); ?>
<?php endif; ?>