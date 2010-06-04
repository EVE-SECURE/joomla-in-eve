<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();


JHTML::stylesheet('common.css', 'administrator/components/com_eve/assets/');

$app = JFactory::getApplication();
$template = $app->getTemplate();
$document =& JFactory::getDocument();
$document->addStyleDeclaration('.icon-32-refresh { background-image: url(templates/'.$template.'/images/toolbar/icon-32-refresh.png); }');

?>
<form action="<?php echo JRoute::_('index.php?option=com_evechartracking'); ?>" method="post" name="adminForm">
<?php foreach($this->tables as $table => $result): ?>
	<?php if ($result): ?>
		<?php printf(JText::_('Com_Evechartracking_Table_Found'), $table); ?><br />
	<?php else: ?>
		<?php printf(JText::_('Com_Evechartracking_Table_Missing'), $table); ?><br />
	<?php endif; ?>

<?php endforeach; ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>