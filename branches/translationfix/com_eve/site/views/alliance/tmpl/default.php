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
	<?php echo JHTML::_('eve.image', 'alliance', $this->alliance, 128); ?>
	<?php echo $this->alliance->name; ?> [<?php echo $this->alliance->shortName; ?>] 
</div>

<div>
	<?php echo JHTML::_('eve.image', 'corporation', array($this->alliance, 'executorCorp'), 64); ?>
	<?php echo JText::_('COM_EVE_EXECUTOR_CORPORATION'); ?>:
		<?php echo JHTML::_('evelink.corporation', array($this->alliance, 'executorCorp')); ?>
</div>
<div>
	<?php echo JText::_('COM_EVE_CORPORATION_MEMBER_COUNT'); ?>: <?php echo $this->alliance->memberCount; ?>
</div>

<?php if ($this->components): ?>
	<div class="eve-component-list">
		<h2><?php echo JText::_('COM_EVE_COMPONENTS');?></h2>
		<?php foreach ($this->components as $component): ?>
			<div>
				<div class="icon-64-<?php echo $component->component; ?> component-icon"></div>
				<p>
					<a href="<?php echo EveRoute::_($component->name, $this->alliance); ?>">
						<?php echo JText::_($component->title); ?>
					</a>
				</p>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>


<div class="eve-item-list">
	<h2><?php echo JText::_('COM_EVE_CORPORATIONS');?></h2>
	<?php foreach ($this->members as $member) : ?>
		<div>
			<a href="<?php echo EveRoute::_('corporation', $this->alliance, $member); ?>">
				<?php echo JHTML::_('eve.image', 'corporation', $member, 64); ?>
			</a>
			<br />
			<p>
				<?php echo JHTML::_('evelink.corporation', $member, $this->alliance); ?>
			</p>
		</div>
	<?php endforeach; ?>
</div>

</div>