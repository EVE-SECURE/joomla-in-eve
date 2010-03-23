<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JHTML::_('behavior.modal');
JHTML::stylesheet('common.css', 'administrator/components/com_eve/assets/');

jimport('joomla.html.pane');
$pane = &JPane::getInstance('sliders', array('allowAllClose' => true));

$allTables = true;
foreach ($this->tables as $tableName => $tableExists) {
	$allTables = $allTables && $tableExists;
}

?>

<div id="eveoverview">
	<?php echo $pane->startPane("eveoverview-pane"); ?>
	<?php echo $pane->startPanel(JText::_('CCP Static Data Dump Info'), 'eveoverview-panel-tables' ); ?>
	<table class="adminlist">
	<?php if (!$allTables): ?>
		<tr>
			<td colspan="2">
				<?php echo JText::_('Com_Eve_Missing_Ccp_Dump'); ?>
				<a href="http://wiki.eve-id.net/CCP_Database_Dump_Resources" target="_blank">
					<?php echo JText::_('Com_Eve_Download_Ccp_Dump'); ?>
				</a>
			</td>
		</tr>
	<?php endif; ?>
		<tr>
			<th class="title">
				<?php echo JText::_('Table'); ?>
			</th>
			<th class="title">
			</th>
		</tr>
	<?php foreach ($this->tables as $tableName => $tableExists): ?>
		<tr>
			<td>
				<?php echo $tableName; ?>
			</td>
			<td>
				<?php echo JHTML::_('image.administrator', $tableExists ? 'tick.png' : 'publish_x.png'); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
	<?php echo $pane->endPanel(); ?>
	<?php echo $pane->endPane(); ?>
</div>

<div id="cpanel">
	<?php foreach ($this->icons as $icon): ?>
		<?php $this->addIcon($icon['icon'], $icon['view'], $icon['caption']); ?>
	<?php endforeach; ?>
	
	<?php $this->addParameters('icon-48-config.png', JText::_('Parameters')); ?>
</div>

