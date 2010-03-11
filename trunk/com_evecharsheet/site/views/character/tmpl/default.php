<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'helpers'.DS.'html');
JHTML::stylesheet('component.css', 'media/com_evecharsheet/css/');
JHTML::script('collapsibles.js', 'media/com_evecharsheet/js/');
JHTML::_('behavior.mootools');
JHTML::_('eve.contextmenu');
$pageClass = $this->params->get('pageclass_sfx');
?>

<?php if ($pageClass) : ?>
	<div class="<?php echo $pageClass; ?>">
<?php endif; ?>
<?php if ($this->params->get('show_page_title', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
<?php endif; ?>

<div class="evecharsheet-heading">
	<img src="http://img.eve.is/serv.asp?s=<?php echo $this->params->get('portraitsize', 256); ?>&c=<?php echo $this->character->characterID; ?>" /> <br />
	<span><?php echo JText::_('Character Name'); ?>:</span> 
		<?php echo $this->character->name; ?> <br />
	<span><?php echo JText::_('Race'); ?>:</span> 
		<?php echo $this->character->race; ?> <br />
	<span><?php echo JText::_('Gender'); ?>:</span>
		<?php echo $this->character->gender; ?> <br />
	<span><?php echo JText::_('Blood Line'); ?>:</span>
		<?php echo $this->character->bloodLine; ?> <br />
	<?php if ($this->show('balance')): ?>
		<span><?php echo JText::_('Balance'); ?>:</span>
			<?php echo number_format($this->character->balance); ?> <br />
	<?php endif; ?>
	<span><?php echo JText::_('Corporation'); ?>:</span>
		<?php echo JHTML::_('evelink.corporation', $this->character); ?> <br />
	
	<?php if ($this->character->allianceID) : ?>
		<span><?php echo JText::_('Alliance'); ?>:</span>
			<?php echo JHTML::_('evelink.alliance', $this->character); ?> <br />
	<?php endif; ?>
</div>


<?php if ($this->show('attributes')): ?>
	<?php echo $this->loadTemplate('attributes'); ?>
<?php endif; ?>

<?php if ($this->show('skillqueue')): ?>
	<?php echo $this->loadTemplate('skillqueue'); ?>
<?php endif; ?>

<?php if ($this->show('skills')): ?>
	<?php echo $this->loadTemplate('skills'); ?>
<?php endif; ?>

<?php if ($this->show('certificates')): ?>
	<?php echo $this->loadTemplate('certificates'); ?>
<?php endif; ?>

<?php if ($this->show('roles')): ?>
	<?php echo $this->loadTemplate('roles'); ?>
<?php endif; ?>

<?php if ($this->show('titles')): ?>
	<?php echo $this->loadTemplate('titles'); ?>
<?php endif; ?>

<?php if ($pageClass) : ?>
	</div>
<?php endif; ?>
