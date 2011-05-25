<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'helpers'.DS.'html');
JHTML::_('behavior.mootools');
JHTML::_('eve.contextmenu');
JHTML::stylesheet('component.css', 'media/com_eveindustryjobs/css/');
?>

<div class="com-eveindustryjobs<?php echo $this->params->get('pageclass_sfx'); ?>">
<?php if ($this->params->get('show_page_title', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
<?php endif; ?>

<form action="<?php echo EveRoute::_('charindustryjobs', $this->character, $this->character, $this->character); ?>" name="adminForm" method="post">
<div class="filter">
	<?php echo JHTML::_('filter.search', $this->listState->get('filter.search')); ?>
</div>
<?php if ($this->isUsersCharacter): ?>
<div>
	<a href="<?php echo EveRoute::_('userindustryjobs'); ?>">
		<?php echo JText::_('Com_Eveindustryjobs_Users_Characters_Industry_Jobs');?>
	</a>
</div>
<?php endif; ?>

<?php echo $this->loadTemplate('list'); ?>

<?php echo $this->pagination->getListFooter(); ?>
<input type="hidden" name="filter_order" value="<?php echo $this->listState->get('list.ordering', 'al.itemID'); ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listState->get('list.direction', 'asc'); ?>" />
</form>
</div>
