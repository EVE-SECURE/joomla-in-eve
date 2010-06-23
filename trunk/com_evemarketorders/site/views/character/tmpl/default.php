<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'helpers'.DS.'html');
JHTML::_('behavior.mootools');
JHTML::_('eve.contextmenu');
JHTML::stylesheet('component.css', 'media/com_eveassetlist/css/');
?>

<div class="com-evemarketorders<?php echo $this->params->get('pageclass_sfx'); ?>">
<?php if ($this->params->get('show_page_title', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
<?php endif; ?>

<form action="<?php echo EveRoute::_('charmarketorders', $this->character, $this->character, $this->character); ?>" name="adminForm" method="post">
<div>
	<?php echo JHTML::_('filter.search', $this->listState->get('filter.search')); ?>
</div>
<?php if ($this->isUsersCharacter): ?>
<div>
	<a href="<?php echo EveRoute::_('usermarketorders'); ?>">
		<?php echo JText::_('Com_Evemarketorders_Users_Characters_Marker_Orders');?>
	</a>
</div>
<?php endif; ?>

<?php echo $this->loadTemplate('list'); ?>	

<?php echo $this->pagination->getListFooter(); ?>
<input type="hidden" name="filter_order" value="<?php echo $this->listState->get('list.ordering', 'mo.issued'); ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listState->get('list.direction', 'desc'); ?>" />
</form>
</div>
