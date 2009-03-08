<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<?php if ($this->params->get('show_page_title')) : ?>
<div class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
	<?php echo $this->escape($this->params->get('page_title')); ?>
</div>
<?php endif; ?>
<div>
<?php echo JText::_('ACCOUNT REGISTRATION FAILED'); ?> <br />
<a href="<?php echo JRoute::_(''); ?>"><?php echo JText::_('RETURN HOME'); ?></a>
 <?php echo JText::_('OR'); ?> 
<a href="<?php echo JRoute::_('index.php?option=com_eve&view=apiform'); ?>"><?php echo JText::_('REGISTER NEW ACCOUNT'); ?></a>
</div>  