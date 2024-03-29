<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'helpers'.DS.'html');
JHTML::_('eve.contextmenu');
JHTML::stylesheet('component.css', 'media/com_eve/css/');
foreach ($this->components as $component) {
	JHTML::stylesheet('component.css', 'media/com_eve'.$component->component.'/css/');
}
$pageClass = $this->params->get('pageclass_sfx');
?>

<div class="com-eve<?php echo $pageClass ? ' '.$pageClass : ''; ?>">
<?php if ($this->params->get('show_page_title', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
<?php endif; ?>

<div>
	<?php echo JHTML::_('eve.image', 'character', $this->character, 128); ?>
	<?php echo JText::_('Character Name'); ?>: <?php echo $this->character->name; ?> <br />
	<?php echo JText::_('Race'); ?>: <?php echo $this->character->race; ?> <br />
	<?php echo JText::_('Gender'); ?>: <?php echo $this->character->gender; ?> <br />
	<?php echo JText::_('Blood Line'); ?>: <?php echo $this->character->bloodLine; ?> <br />
</div>

<div>
	<?php echo JHTML::_('eve.image', 'corporation', $this->character, 64); ?>
	<?php echo JText::_('Corporation'); ?>:
		<?php echo JHTML::_('evelink.corporation', $this->character); ?>
</div>

<?php if ($this->character->allianceID) : ?>
	<div>
		<?php echo JHTML::_('eve.image', 'alliance', $this->character, 64); ?>
		<?php echo JText::_('Alliance'); ?>:
			<?php echo JHTML::_('evelink.alliance', $this->character); ?>
	</div>
<?php endif; ?>

<?php if ($this->components): ?>
	<div class="eve-component-list">
		<h2><?php echo JText::_('Com_Eve_Components');?></h2>
		<?php foreach ($this->components as $component): ?>
			<div>
				<div class="icon-64-<?php echo $component->component; ?> component-icon"></div>
				<p>
					<a href="<?php echo EveRoute::_($component->name, $this->character, $this->character, $this->character); ?>">
						<?php echo JText::_($component->title); ?>
					</a>
				</p>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

<?php if (is_array($this->apischedule) && $this->apischedule): ?>
	<?php echo $this->loadTemplate('apischedule'); ?>
<?php endif; ?>

<?php if (is_array($this->sectionaccess) && $this->sectionaccess): ?>
	<?php echo $this->loadTemplate('sectionaccess'); ?>
<?php endif; ?>

</div>