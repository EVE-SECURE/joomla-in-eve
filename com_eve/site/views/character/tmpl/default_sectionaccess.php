<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>

<div class="sectionaccess">
	<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="characterSectionaccessForm">
		<fieldset>
		<legend><?php echo JText::_('COM_EVE_CHARACTER_SECTION_ACCESS'); ?></legend>
		<table>
			<tr>
				<th class="title">
					<?php echo JText::_('COM_EVE_SECTION_ACCESS_NAME'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('COM_EVE_SECTION_ACCESS_LEVEL'); ?>
				</th>
			</tr>
			<?php foreach ($this->sectionaccess as $i => $item): ?>
				<tr>
					<td>
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
		<input type="hidden" name="option" value="com_eve" />
		<input type="hidden" name="task" value="character.sectionaccess" />
		<input type="hidden" name="characterID" value="<?php echo $this->character->characterID; ?>" />
		<input type="submit" class="button" value="<?php echo JText::_('Submit'); ?>" />
		<?php echo JHTML::_('form.token'); ?>
		</fieldset>
	</form>
</div>