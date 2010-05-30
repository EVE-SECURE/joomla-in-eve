<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

$name = $this->itemName;
$value = $this->itemValue; 
?>
<td class="key">
	<label for="role<?php echo $name; ?>"><?php echo JText::_(/*'com_eve_role_'.*/$name); ?></label>
</td>
<td>
	<input type="checkbox" name="jform[roles][<?php echo $name; ?>]" id="role<?php echo $name; ?>" value="<?php echo $value; ?>" 
		<?php echo $this->acl->hasRole($value, $this->item->roles) ? 'checked="checked"' : ''; ?> />
</td>
