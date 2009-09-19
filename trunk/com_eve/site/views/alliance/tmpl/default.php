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
	<?php echo $this->alliance->name; ?> &lt;<?php echo $this->alliance->shortName; ?> 
</div>

<div>
	<?php echo JText::_('Executor Corp'); ?>:
		<a href="<?php echo EveRoute::_('', 'corporation', $this->alliance, array($this->alliance, 'executorCorp')); ?>"> 
			<?php echo $this->alliance->executorCorpName; ?> [<?php echo $this->alliance->executorTicker; ?>]
		</a>
</div>
<div>
	<?php echo JText::_('Member Count'); ?>: <?php echo $this->alliance->memberCount; ?>
</div>

<?php echo JText::_('Members'); ?> <br />
<?php foreach ($this->members as $member) : ?>
	<a href="<?php echo EveRoute::_('', 'corporation', $this->alliance, $member); ?>">
		<?php echo $member->corporationName; ?> [<?php echo $member->ticker; ?>]
	</a>
	
	<br />
<?php endforeach; ?>
