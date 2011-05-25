<?php defined( '_JEXEC' ) or die( 'Restricted Access' ); ?>
<?php if ($this->params->get('anonymous_access') || $this->user->id > 0) { ?>
<form id="postForm" name="postForm" method="post" action="<?php echo JRoute::_('index.php?option=com_simplestforum&view=postlist'); ?>" />
	<?php echo $this->_parentId?JText::_('POST A RESPONSE'):JText::_('POST A NEW MESSAGE'); ?>
	<?php echo JText::_('SUBJECT'); ?>
	<input type="text" id="subject" name="subject" size="50" maxlength="100" value="<?php echo $this->subject; ?>" /><br />
	<?php echo JText::_('MESSAGE'); ?><br />
	<textarea id="message" name="message" rows="10" cols="50"><?php echo $this->message; ?></textarea>
	<input type="hidden" name="task" value="addPost" />
	<input type="hidden" name="parentId" value="<?php echo $this->parent->id; ?>" />
	<input type="hidden" name="forumId" value="<?php echo $this->forum->id; ?>" />
	<input type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
	<input type="submit" class="button" value="<?php echo JText::_('SUBMIT'); ?>" />
</form>
<?php } ?>
