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

<?php echo $this->corporation->corporationName; ?> [<?php echo $this->corporation->ticker; ?>]

<div>
<?php if ($this->corporation->allianceID) : ?>
	<a href="<?php echo EveRoute::_('index.php?option=com_eve&view=alliance', $this->corporation); ?>">
		<?php echo $this->corporation->allianceName; ?> &lt;<?php echo $this->corporation->allianceShortName; ?>&gt;
	</a>
<?php endif; ?>
</div>

<div>
	<?php echo JText::_('CEO'); ?>: 
	<a href="<?php echo EveRoute::_('index.php?option=com_eve&view=corporation', 
			$this->corporation, $this->corporation, array($this->corporation, 'ceo')); ?>">
		<?php echo $this->corporation->ceoName; ?>
	</a>
</div>
<div>
	<?php echo JText::_('Member Count'); ?>: <?php echo $this->corporation->memberCount; ?>
</div>

<div>
	<?php echo JText::_('Head Quarters Station'); ?>: <?php echo $this->corporation->stationName; ?>
</div>

<div>
	<?php echo JText::_('Tax Rate'); ?>: <?php echo $this->corporation->taxRate * 100; ?>%
</div>

<div>
	<?php echo JText::_('Shares'); ?>: <?php echo $this->corporation->shares; ?>
</div>

<div>
	<?php echo $this->corporation->description; ?>
</div>

<?php echo JText::_('Members'); ?> <br />
<?php foreach ($this->members as $member) : ?>
	<a href="<?php echo EveRoute::_('index.php?option=com_eve&view=corporation', $this->corporation, $this->corporation, $member); ?>">
		<?php echo $member->name; ?>
	</a> 
	<br />
<?php endforeach; ?>

