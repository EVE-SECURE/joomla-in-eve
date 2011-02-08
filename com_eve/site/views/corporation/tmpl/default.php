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

<div class="com-eve<?php echo $pageClass ? ' '.$pageClass : ''; ?>">
<?php if ($this->params->get('show_page_title', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
<?php endif; ?>

<div>
	<?php echo JHTML::_('eve.image', 'corporation', $this->corporation, 128); ?>
	<?php echo $this->corporation->corporationName; ?> [<?php echo $this->corporation->ticker; ?>]
</div>

<?php if ($this->corporation->allianceID) : ?>
	<div>
		<?php echo JHTML::_('eve.image', 'alliance', $this->corporation, 64); ?>
		<?php echo JHTML::_('evelink.alliance', $this->corporation); ?>
	</div>
<?php endif; ?>

<div>
	<?php echo JText::_('COM_EVE_CORPORATION_CEO'); ?>: 
	<?php echo JHTML::_('evelink.character', array($this->corporation, 'ceo'), $this->corporation); ?>
</div>

<div>
	<?php echo JText::_('COM_EVE_CORPORATION_MEMBER_COUNT'); ?>: <?php echo $this->corporation->memberCount; ?>
</div>

<div>
	<?php echo JText::_('COM_EVE_CORPORATION_HEADQUARTERS_STATION'); ?>: <?php echo $this->corporation->stationName; ?>
</div>

<div>
	<?php echo JText::_('COM_EVE_CORPORATION_TAX_RATE'); ?>: <?php echo $this->corporation->taxRate; ?>%
</div>

<div>
	<?php echo JText::_('COM_EVE_CORPORATION_SHARES'); ?>: <?php echo $this->corporation->shares; ?>
</div>

<div>
	<?php echo $this->corporation->description; ?>
</div>

<?php if ($this->components): ?>
	<div class="eve-component-list">
		<h2><?php echo JText::_('COM_EVE_COMPONENTS');?></h2>
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
<?php endif; ?>


<div class="eve-item-list">
	<h2><?php echo JText::_('COM_EVE_CORPORATION_MEMBERS'); ?></h2>
	<?php foreach ($this->members as $member) : ?>
		<?php echo JHTML::_('evelink.character', $member, $this->corporation); ?> <br />
	<?php endforeach; ?>
</div>

</div>
