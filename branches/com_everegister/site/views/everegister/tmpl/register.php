<?php
/**
 * @version		$Id$
 */
 
// No direct access
defined('_JEXEC') or die('Restricted access'); ?>

<?php if ($this->params->get('show_page_title')) : ?>
<div class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
	<?php echo $this->escape($this->params->get('page_title')); ?>
</div>
<?php endif; ?>
<h1>You are registered!</h1>
<h3>sorta...</h3>