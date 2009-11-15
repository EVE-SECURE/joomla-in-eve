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
	<?php $this->addIcon('icon-48-character.png', 'characters', JText::_('Characters')); ?>
	<?php $this->addIcon('icon-48-corporation.png', 'corporations', JText::_('Corporations')); ?>
	<?php $this->addIcon('icon-48-alliance.png', 'alliances', JText::_('Alliances')); ?>
	<?php $this->addIcon('icon-48-account.png', 'accounts', JText::_('Accounts')); ?>
	<?php $this->addIcon('icon-48-schedule.png', 'schedule', JText::_('Schedule')); ?>
	<?php $this->addIcon('icon-48-encryption.png', 'encryption', JText::_('API Key Encryption')); ?>
	<?php $this->addParameters('icon-48-config.png', JText::_('Parameters')); ?>
</div>

