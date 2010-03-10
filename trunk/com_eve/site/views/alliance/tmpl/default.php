<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'helpers'.DS.'html');

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
		<?php echo JHTML::_('evelink.corporation', array($this->alliance, 'executorCorp')); ?>
</div>
<div>
	<?php echo JText::_('Member Count'); ?>: <?php echo $this->alliance->memberCount; ?>
</div>

<div>
	<?php foreach ($this->components as $component): ?>
		<a href="<?php echo EveRoute::_($component->name, $this->alliance); ?>">
			<?php echo JText::_($component->title); ?>
		</a> <br />
	<?php endforeach; ?>
</div>

<div>
	<?php echo JText::_('Members'); ?> <br />
	<?php foreach ($this->members as $member) : ?>
		<?php echo JHTML::_('evelink.corporation', $member, $this->alliance); ?> <br />
	<?php endforeach; ?>
</div>