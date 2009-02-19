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
<div>
<?php foreach ($this->groups as $group): ?>
	<tr>
		<td colspan="2">
			<?php echo JText::_($group->groupName); ?>
		</td>
	</tr>
<?php endforeach; ?>
<div>