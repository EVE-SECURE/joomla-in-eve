<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<form action="<?php echo JRoute::_('index.php?option=com_evecharsheet'); ?>" method="post" name="adminForm">
<?php foreach($this->tables as $table => $result): ?>
	<?php if ($result): ?>
		<?php printf(JText::_('Com_Evecharsheet_Table_Found'), $table); ?><br />
	<?php else: ?>
		<?php printf(JText::_('Com_Evecharsheet_Table_Missing'), $table); ?><br />
	<?php endif; ?>

<?php endforeach; ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>