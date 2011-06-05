<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'helpers'.DS.'html');
JHTML::stylesheet('component.css', 'media/com_evecharsheet/css/');
JHTML::_('eve.contextmenu');
$pageClass = $this->params->get('pageclass_sfx');
?>

<div class="com-evecharsheet<?php echo $pageClass ? ' '.$pageClass : ''; ?>">
<?php if ($this->params->get('show_page_title', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
<?php endif; ?>

<div class="evecharsheet-list">
	<?php foreach ($this->characters as $character) : ?>
		<div>
			<a href="<?php echo EveRoute::_('charsheet', $character, $character, $character); ?>">
				<?php echo JHTML::_('image', 'http://image.eveonline.com/Character/'.$character->characterID.'_64.jpg', $character->name); ?>
			</a>
			<br />
			<p>
			<a href="<?php echo EveRoute::_('charsheet', $character, $character, $character); ?>">
				<?php echo $this->escape($character->name);?>
			</a>
			</p>
		</div>
	<?php endforeach; ?>
</div>
</div>