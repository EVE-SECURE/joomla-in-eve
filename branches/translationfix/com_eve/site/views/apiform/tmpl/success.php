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
<?php echo JText::_('COM_EVE_ACCOUNT_REGISTERD'); ?> <br />

<a href="<?php echo JRoute::_(''); ?>"><?php echo JText::_('COM_EVE_RETURN_HOME'); ?></a>
 <?php echo JText::_('COM_EVE_OR_REGISTER'); ?> 
<a href="<?php echo JRoute::_('index.php?option=com_eve&view=apiform'); ?>"><?php echo JText::_('COM_EVE_REGISTER_NEW_ACCOUNT'); ?></a>
</div>  