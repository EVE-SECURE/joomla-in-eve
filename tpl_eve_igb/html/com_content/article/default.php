<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$canEdit	= ($this->user->authorize('com_content', 'edit', 'content', 'all') || $this->user->authorize('com_content', 'edit', 'content', 'own'));
?>
<?php if ($canEdit || $this->params->get('show_title') || $this->params->get('show_pdf_icon') || $this->params->get('show_print_icon') || $this->params->get('show_email_icon')) : ?>
	<?php if ($this->params->get('show_title')) : ?>
	<div class="contentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>" width="100%">
		<?php if ($this->params->get('link_titles') && $this->article->readmore_link != '') : ?>
		<a href="<?php echo $this->article->readmore_link; ?>" class="contentpagetitle<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
			<?php echo $this->escape($this->article->title); ?></a>
		<?php else : ?>
			<?php echo $this->escape($this->article->title); ?>
		<?php endif; ?>
	</div>
	<?php endif; ?>
<?php endif; ?>

<?php  if (!$this->params->get('show_intro')) :
	echo $this->article->event->afterDisplayTitle;
endif; ?>
<?php echo $this->article->event->beforeDisplayContent; ?>
<table class="contentpaneopen<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<?php if (($this->params->get('show_section') && $this->article->sectionid) || ($this->params->get('show_category') && $this->article->catid)) : ?>
<tr>
	<td>
		<?php if ($this->params->get('show_section') && $this->article->sectionid && isset($this->article->section)) : ?>
		<span>
			<?php if ($this->params->get('link_section')) : ?>
				<?php echo '<a href="'.JRoute::_(ContentHelperRoute::getSectionRoute($this->article->sectionid)).'">'; ?>
			<?php endif; ?>
			<?php echo $this->article->section; ?>
			<?php if ($this->params->get('link_section')) : ?>
				<?php echo '</a>'; ?>
			<?php endif; ?>
				<?php if ($this->params->get('show_category')) : ?>
				<?php echo ' - '; ?>
			<?php endif; ?>
		</span>
		<?php endif; ?>
		<?php if ($this->params->get('show_category') && $this->article->catid) : ?>
		<span>
			<?php if ($this->params->get('link_category')) : ?>
				<?php echo '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->article->catslug, $this->article->sectionid)).'">'; ?>
			<?php endif; ?>
			<?php echo $this->article->category; ?>
			<?php if ($this->params->get('link_category')) : ?>
				<?php echo '</a>'; ?>
			<?php endif; ?>
		</span>
		<?php endif; ?>
	</td>
</tr>
<?php endif; ?>
<?php if (($this->params->get('show_author')) && ($this->article->author != "")) : ?>
<tr>
	<td width="70%"  valign="top" colspan="2">
		<span class="small">
			<?php JText::printf( 'Written by', ($this->article->created_by_alias ? $this->article->created_by_alias : $this->article->author) ); ?>
		</span>
		&nbsp;&nbsp;
	</td>
</tr>
<?php endif; ?>

<?php if ($this->params->get('show_create_date')) : ?>
<tr>
	<td valign="top" colspan="2" class="createdate">
		<?php echo JHTML::_('date', $this->article->created, JText::_('DATE_FORMAT_LC2')) ?>
	</td>
</tr>
<?php endif; ?>

<?php if ($this->params->get('show_url') && $this->article->urls) : ?>
<tr>
	<td valign="top" colspan="2">
		<a href="http://<?php echo $this->article->urls ; ?>" target="_blank">
			<?php echo $this->article->urls; ?></a>
	</td>
</tr>
<?php endif; ?>

<tr>
<td valign="top" colspan="2">
<?php if (isset ($this->article->toc)) : ?>
	<?php echo $this->article->toc; ?>
<?php endif; ?>
<?php echo $this->article->text; ?>
</td>
</tr>

<?php if ( intval($this->article->modified) !=0 && $this->params->get('show_modify_date')) : ?>
<tr>
	<td colspan="2"  class="modifydate">
		<?php echo JText::_( 'Last Updated' ); ?> ( <?php echo JHTML::_('date', $this->article->modified, JText::_('DATE_FORMAT_LC2')); ?> )
	</td>
</tr>
<?php endif; ?>
</table>
<span class="article_separator">&nbsp;</span>
<?php echo $this->article->event->afterDisplayContent; ?>