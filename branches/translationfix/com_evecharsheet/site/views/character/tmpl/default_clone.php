<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<div class="evecharsheet-clone">
	<h2><?php echo JText::_('Com_Evecharsheet_Clone'); ?></h2>
	<?php if ($this->clone): ?>
		<?php echo JHTML::_('evelink.type', array($this->clone, 'clone')); ?>: <?php echo number_format($this->clone->cloneSkillPoints); ?> <?php echo JText::_('Com_Evecharsheet_Skill_Points'); ?>
	<?php else: ?>
		<?php echo JText::_('Com_Evecharsheet_Unknown_Clone')?>
	<?php endif;?>
</div>
