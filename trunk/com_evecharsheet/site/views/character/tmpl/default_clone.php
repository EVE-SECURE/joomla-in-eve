<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<div class="evecharsheet-clone">
	<h3><?php echo JText::_('Clone'); ?></h3>
	<?php echo JHTML::_('evelink.type', array($this->clone, 'clone')); ?>: <?php echo $this->clone->cloneSkillPoints; ?> <?php echo JText::_('Skill Points'); ?>
</div>
