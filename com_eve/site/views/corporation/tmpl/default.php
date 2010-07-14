<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_eve'.DS.'helpers'.DS.'html');
JHTML::_('eve.contextmenu');
JHTML::stylesheet('component.css', 'media/com_eve/css/');
foreach ($this->components as $component) {
	JHTML::stylesheet('component.css', 'media/com_eve'.$component->component.'/css/');
}
$pageClass = $this->params->get('pageclass_sfx');
?>

<div class="com-evecharsheet<?php echo $pageClass ? ' '.$pageClass : ''; ?>">
<?php if ($this->params->get('show_page_title', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
<?php endif; ?>

<?php echo $this->corporation->corporationName; ?> [<?php echo $this->corporation->ticker; ?>]

<div>
<?php if ($this->corporation->allianceID) : ?>
	<?php echo JHTML::_('evelink.alliance', $this->corporation); ?>
<?php endif; ?>
</div>

<div>
	<?php echo JText::_('CEO'); ?>: 
	<?php echo JHTML::_('evelink.character', array($this->corporation, 'ceo'), $this->corporation); ?>
</div>

<div>
	<?php echo JText::_('Member Count'); ?>: <?php echo $this->corporation->memberCount; ?>
</div>

<div>
	<?php echo JText::_('Head Quarters Station'); ?>: <?php echo $this->corporation->stationName; ?>
</div>

<div>
	<?php echo JText::_('Tax Rate'); ?>: <?php echo $this->corporation->taxRate; ?>%
</div>

<div>
	<?php echo JText::_('Shares'); ?>: <?php echo $this->corporation->shares; ?>
</div>

<div>
	<?php echo $this->corporation->description; ?>
</div>

<div class="eve-component-list">
	<h2><?php echo JText::_('Com_Eve_Components');?></h2>
	<?php foreach ($this->components as $component): ?>
		<div>
			<div class="icon-64-<?php echo $component->component; ?> component-icon"></div>
			<p>
				<a href="<?php echo EveRoute::_($component->name, $this->corporation, $this->corporation); ?>">
					<?php echo JText::_($component->title); ?>
				</a>
			</p>
		</div>
	<?php endforeach; ?>
</div>


<div>
	<?php echo JText::_('Members'); ?> <br />
	<?php foreach ($this->members as $member) : ?>
		<?php echo JHTML::_('evelink.character', $member, $this->corporation); ?> <br />
	<?php endforeach; ?>
</div>

</div>
