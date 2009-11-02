<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::_('behavior.tooltip');
JHTML::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'html');
$user = JFactory::getUser();


JHTML::stylesheet('administrator.css', '../media/com_cron/');

JHTML::_('behavior.keepalive');

?>
<script type="text/javascript" language="javascript">
<!--
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	// check we aren't cancelling
	if (pressbutton == 'job.cancel') {
		// no need to validate, we are cancelling
		submitform(pressbutton);
		return;
	}
	// get text

	submitform( pressbutton );
}

//-->
</script>


<form action="<?php JRoute::_('index.php?option=com_cron'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" >
	<input type="hidden" name="task" value="" /> 
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>"> 
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>"> <?php echo JHTML::_('form.token'); ?>

	<div class="col100">

		<fieldset class="adminform">
			<legend>
				<?php echo JText::_('Cron Job'); ?>
			</legend>
		
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key">
							<label> <?php echo JText::_('Pattern'); ?>:</label>
						</td>
						<td>
							<input type="text" name="jform[pattern]" id="jformpattern" size="100" value="<?php echo $this->item->pattern; ?>" />
						</td>
					</tr>
					<tr>
						<td class="key">
							<label> <?php echo JText::_('Type'); ?>:</label>
						</td>
						<td>
							<?php echo JHTML::_('job.type', 'jform[type]', null, $this->item->type); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<label> <?php echo JText::_('Plugin'); ?>:</label>
						</td>
						<td>
							<input type="text" name="jform[plugin]" id="jformplugin" size="25" value="<?php echo $this->item->plugin; ?>" />
						</td>
					</tr>
					<tr>
						<td class="key">
							<label> <?php echo JText::_('Event'); ?>:</label>
						</td>
						<td>
							<input type="text" name="jform[event]" id="jformevent" size="25" value="<?php echo $this->item->event; ?>" />
						</td>
					</tr>
					
					<tr>
						<td class="key">
							<label> <?php echo JText::_('Enabled'); ?>:</label>
						</td>
						<td>
							<?php echo JHTML::_('select.booleanlist', 'jform[state]', null, $this->item->state); ?>
						</td>
					</tr>
					<!-- tr>
						<td class="key">
							<label> <?php echo JText::_('Next Run'); ?>:</label>
						</td>
						<td>
							<?php //echo JHTML::_('calendar', $this->item->next, 'jform[next]', 'jformnext', '%Y-%m-%d %H:%M'); ?>
						</td>
					
					</tr -->
					
				</tbody>
			</table>
		</fieldset>
		
	</div>
	
	<div class="clr"></div>
</form>
