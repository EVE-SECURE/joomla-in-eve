<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'helpers'.DS.'html');
JHTML::_('eve.contextmenu');
?>

<?php if ($this->params->get('show_page_title')) : ?>
<div class="componentheading<?php echo $this->params->get('pageclass_sfx'); ?>">
	<?php echo $this->escape($this->params->get('page_title')); ?>
</div>
<?php endif; ?>



<div>
	<?php foreach ($this->components as $component): ?>
		<a href="<?php echo EveRoute::_($component->name); ?>">
			<?php echo JText::_($component->title); ?>
		</a> <br />
	<?php endforeach; ?>
</div>

<div>
	<?php echo JText::_('Characters'); ?> <br />
	<?php foreach ($this->characters as $character) : ?>
		<?php echo JHTML::_('evelink.character', $character); ?> <br />
	<?php endforeach; ?>
</div>
