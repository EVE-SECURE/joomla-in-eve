<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::_('behavior.modal');
JHTML::stylesheet('common.css', 'administrator/components/com_eve/assets/');

?>


<div id="cpanel">
	<?php foreach ($this->icons as $icon): ?>
		<?php $this->addIcon($icon['icon'], $icon['view'], $icon['caption']); ?>
	<?php endforeach; ?>
	
	<?php $this->addParameters('icon-48-config.png', JText::_('Parameters')); ?>
</div>

