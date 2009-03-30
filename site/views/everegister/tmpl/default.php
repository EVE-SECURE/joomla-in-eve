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
<h1>Vallid Corporation ID:<?php echo $this->v_corpID; ?></h1>
<h1>Vallid Alliance ID:<?php echo $this->v_allianceID; ?></h1> 
<h2>Registration allowed for <?php echo $this->params->get('everegister')?> members</h2>
<div>
<form action="<?php echo JRoute::_('index.php?option=com_everegister'); ?>" method="post">
	<input type="hidden" name="task" value="chkAPI" />
	<table>
		<tr>
			<td>
				<label for="APIUser"><?php echo JText::_('API User ID'); ?></label>
			</td>
			<td>
				<input name="APIUser" id="userID" value="" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="APIKey"><?php echo JText::_('API Key'); ?></label>
			</td>
			<td>
				<input name="APIKey" value="" />
			</td>
		</tr>
	</table>
	<input type="submit" value="<?php echo JText::_('Next'); ?>" />
	<input type="reset" value="<?php echo JText::_('Cancel'); ?>" />
</form>
</div>
