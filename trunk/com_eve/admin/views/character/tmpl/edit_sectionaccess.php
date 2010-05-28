<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>

<div class="sectionaccess">
	<fieldset>
		<legend><?php echo JText::_('Com_Eve_Character_Section_Access'); ?></legend>
		<table class="admintable">
			<tr>
				<th class="title key">
					<?php echo JText::_('Com_Eve_Section_Access_Name'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('Com_Eve_Section_Access_Level'); ?>
				</th>
			</tr>
			<?php foreach ($this->sectionaccess as $i => $item): ?>
				<tr>
					<td class="key">
						<label for="sectionaccess_<?php echo $i; ?>_access">
							<?php echo $this->escape($item->title); ?>
						</label>
					</td>
					<td>
						<input type="hidden" name="sectionaccess[<?php echo $i; ?>][section]" 
							value="<?php echo $item->section; ?>">
						<?php echo JHTML::_('select.genericlist', $this->groups, 'sectionaccess['.$i.'][access]', '', 'value', 'text', 
							$item->access, 'sectionaccess_'.$i.'_access_', 1); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
	</fieldset>
</div>