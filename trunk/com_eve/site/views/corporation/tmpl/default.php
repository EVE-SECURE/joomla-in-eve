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
	<?php echo $this->corporation->allianceName; ?> &lt;<?php echo $this->corporation->allianceShortName; ?>&gt;
<?php endif; ?>
</div>

<div>
	<?php echo JText::_('CEO'); ?>: <?php echo $this->corporation->ceoName; ?>
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
	<a href="<?php echo JRoute::_('index.php?option=com_eve&view=character&&characterID='.$member->characterID.':'.$member->name); ?>">
		<?php echo $member->name; ?>
	</a> 
	<br />
<?php endforeach; ?>

