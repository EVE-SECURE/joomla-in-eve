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
		<legend><?php echo JText::_('Com_Eve_Character_Section_Access'); ?></legend>
		<table>
			<tr>
				<th class="title">
					<?php echo JText::_('Com_Eve_Api_Call_Name'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('Com_Eve_Api_Call_Published'); ?>
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
						<input type="text" class="inputbox" name="sectionaccess[<?php echo $i; ?>][access]" 
							id="sectionaccess_<?php echo $i; ?>_access"
							value="<?php echo $item->access; ?>"  />
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