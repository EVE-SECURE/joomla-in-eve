<?php
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
?>
<?php if ($this->params->get('show_page_title')) : ?>
	<div class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<?php echo $this->escape($this->title); ?>
	</div>
<?php endif; ?>

<ol start="<?php echo $this->pagination->limitstart +1; ?>">
	<?php foreach ($this->characters as $character): ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_evecharsheet&view=sheet&characterID='.$character->characterID); ?>">
				<?php echo $character->characterName; ?>
			</a>
			<?php if ($character->owner): ?>
				[<a href="<?php echo JRoute::_('index.php?option=com_evecharsheet&view=list&layout=owner&owner='.$character->owner); ?>">
					<?php echo $character->ownerName; ?>
				</a>]
			<?php endif; ?>	
		</li>
	<?php endforeach; ?>
</ol>
<?php echo $this->pagination->getListFooter(); ?>
