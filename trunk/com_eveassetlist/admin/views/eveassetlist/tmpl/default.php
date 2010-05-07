<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<form action="<?php echo JRoute::_('index.php?option=com_eveassetlist'); ?>" method="post" name="adminForm">
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
