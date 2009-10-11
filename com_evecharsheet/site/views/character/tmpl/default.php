<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::_('stylesheet', 'character.css', 'components/com_evecharsheet/assets/');

$pageClass = $this->params->get('pageclass_sfx');
?>

<?php if ($pageClass) : ?>
	<div class="<?php echo $pageClass; ?>">
<?php endif; ?>
<?php if ($this->params->get('show_page_title', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
<?php endif; ?>

<div class="evecharsheet-heading">
	<img src="http://img.eve.is/serv.asp?s=256&c=<?php echo $this->character->characterID; ?>" /> <br />
	<span><?php echo JText::_('Character Name'); ?>:</span> 
		<?php echo $this->character->name; ?> <br />
	<span><?php echo JText::_('Race'); ?>:</span> 
		<?php echo $this->character->race; ?> <br />
	<span><?php echo JText::_('Gender'); ?>:</span>
		<?php echo $this->character->gender; ?> <br />
	<span><?php echo JText::_('Blood Line'); ?>:</span>
		<?php echo $this->character->bloodLine; ?> <br />
	<span><?php echo JText::_('Ballance'); ?>:</span>
		<?php echo number_format($this->character->balance); ?> <br />
	<span><?php echo JText::_('Corporation'); ?>:</span>
		<a href="<?php echo EveRoute::_('corporation', $this->character, $this->character); ?>">
			<?php echo  $this->character->corporationName; ?> [<?php echo  $this->character->corporationTicker; ?>]
		</a> <br />
	
	<?php if ($this->character->allianceID) : ?>
		<span><?php echo JText::_('Alliance'); ?>:</span>
			<a href="<?php echo EveRoute::_('alliance', $this->character); ?>">
				<?php echo $this->character->allianceName; ?> &lt;<?php echo $this->character->allianceShortName; ?>&gt;
			</a> <br />
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
