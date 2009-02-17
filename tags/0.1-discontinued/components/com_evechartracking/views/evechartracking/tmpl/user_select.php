<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
<div>
	<form action="<?php echo JRoute::_('index.php?option=com_evechartracking&view=evechartracking&layout=user'); ?>" method="post" >
	<input type="hidden" name="limitstart" value="0" />
	<?php echo $this->html_users; ?>
	<?php if (EveHelperIgb::isIgb()): ?>
		<input type="submit" value="<?php echo JText::_('Submit'); ?>" />
	<?php endif; ?>
	</form>
</div>