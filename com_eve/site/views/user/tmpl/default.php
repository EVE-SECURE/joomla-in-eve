<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'helpers'.DS.'html');
JHTML::stylesheet('component.css', 'media/com_eve/css/');
JHTML::_('eve.contextmenu');
$pageClass = $this->params->get('pageclass_sfx');

foreach ($this->components as $component) {
	JHTML::stylesheet('component.css', 'media/com_eve'.$component->component.'/css/');
}
?>

<div class="com-evecharsheet<?php echo $pageClass ? ' '.$pageClass : ''; ?>">
<?php if ($this->params->get('show_page_title', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
<?php endif; ?>

<div class="eve-component-list">
	<h2><?php echo JText::_('Com_Eve_Components');?></h2>
	<?php foreach ($this->components as $component): ?>
		<div>
			<div class="icon-64-<?php echo $component->component; ?> component-icon"></div>
			<p>
			<a href="<?php echo EveRoute::_($component->name); ?>">
				<?php echo JText::_($component->title); ?>
			</a>
			</p>
		</div>
	<?php endforeach; ?>
</div>

<div class="eve-character-list">
	<h2><?php echo JText::_('Com_Eve_Characters');?></h2>
	<?php foreach ($this->characters as $character) : ?>
		<div>
			<a href="<?php echo EveRoute::_('character', $character, $character, $character); ?>">
				<?php echo JHTML::_('image', 'http://img.eve.is/serv.asp?s=64&c='.$character->characterID, $character->name); ?>
			</a>
			<br />
			<p>
				<a href="<?php echo EveRoute::_('charsheet', $character, $character, $character); ?>">
					<?php echo JHTML::_('evelink.character', $character); ?>
				</a>
			</p>
		</div>
	<?php endforeach; ?>
</div>
</div>