<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

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
	<a href="<?php echo EveRoute::_('', 'corporation', $this->character, $this->character); ?>">
		<?php echo  $this->character->corporationName; ?> [<?php echo  $this->character->corporationTicker; ?>]
	</a>
</div>

<div>
<?php if ($this->character->allianceID) : ?>
	<?php echo JText::_('Alliance'); ?>:
	<a href="<?php echo EveRoute::_('', 'alliance', $this->character); ?>">
		<?php echo $this->character->allianceName; ?> &lt;<?php echo $this->character->allianceShortName; ?>&gt;
	</a>
<?php endif; ?>
</div>

<div>
	<?php foreach ($this->links as $link): ?>
		<a href="<?php echo EveRoute::_($link->component, $link->entity, $this->character, $this->character, $this->character); ?>">
			<?php echo JText::_($link->name); ?>
		</a>
	<?php endforeach; ?>
</div>