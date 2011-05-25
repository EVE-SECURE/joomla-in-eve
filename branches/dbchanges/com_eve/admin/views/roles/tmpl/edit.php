<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::stylesheet('common.css', 'administrator/components/com_eve/assets/');
JHTML::script('roles.js', 'administrator/components/com_eve/assets/js/');
JHTML::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'html');

$i = 0;
?>
<form action="<?php //FIXME: JRoute::_('index.php?option=com_eve'); ?>" method="post" name="adminForm">
	<div style="width: 28%; float: left;">
	<fieldset>
		<legend><?php echo JText::_('General Roles'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
				<?php foreach ($this->acl->getRoles('general') as $name => $value): ?>
					<?php
					echo $this->displayItem($name, $value);
					if ($i % 2) {
						echo '</tr><tr>';
					}
					$i += 1;
					?>
				<?php endforeach; ?>
					<td></td>
				<?php $i += 1; ?>
				</tr>
		</table>
	</fieldset>
	</div>
	<div style="width: 28%; float: left;">
	<fieldset>
		<legend><?php echo JText::_('Account Access'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key">
						<label for="selectAllAccounts"><?php echo JText::_('Select All'); ?></label>
					</td>
					<td colspan="3">
						<input id="selectAllAccounts" type="checkbox">
					</td>
				</tr>
				<tr>
				<?php foreach ($this->acl->getRoles('account') as $name => $value): ?>
					<?php
					echo $this->displayItem($name, $value);
					if ($i % 2) {
						echo '</tr><tr>';
					}
					$i += 1;
					?>
				<?php endforeach; ?>
				</tr>
			</tbody>
		</table>
	</fieldset>
	</div>
	<div style="width: 28%; float: left;">
	<fieldset>
		<legend><?php echo JText::_('Hangar Access'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key">
						<label for="selectAllHangars"><?php echo JText::_('Select All'); ?></label>
					</td>
					<td colspan="3">
						<input id="selectAllHangars" type="checkbox">
					</td>
				</tr>
				<tr>
				<?php foreach ($this->acl->getRoles('hangar') as $name => $value): ?>
					<?php
					echo $this->displayItem($name, $value);
					if ($i % 2) {
						echo '</tr><tr>';
					}
					$i += 1;
					?>
				<?php endforeach; ?>
				</tr>
			</tbody>
		</table>
	</fieldset>
	</div>
	<div style="width: 16%; float: left;">
	<fieldset>
		<legend><?php echo JText::_('Container Access'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key">
						<label for="selectAllContainers"><?php echo JText::_('Select All'); ?></label>
					</td>
					<td>
						<input id="selectAllContainers" type="checkbox">
					</td>
				</tr>
			<?php foreach ($this->acl->getRoles('container') as $name => $value): ?>
				<tr>
					<?php echo $this->displayItem($name, $value); ?>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</fieldset>
	</div>
			
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="" />
	<input type="hidden" name="jform[id]" value="<?php echo $this->state->get('section.id'); ?>"/>
	<input type="hidden" name="jform[section]" value="<?php echo $this->state->get('sectionCorporation.section'); ?>"/>
	<input type="hidden" name="jform[corporationID]" value="<?php echo $this->state->get('sectionCorporation.corporationID'); ?>"/>
	<?php echo JHTML::_('form.token'); ?>
</form>
