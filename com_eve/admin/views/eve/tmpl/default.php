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
	<?php echo $pane->startPanel(JText::_('Owner Corporations'), 'eveoverview-panel-owner-corporations' ); ?>
		<?php if (!$this->ownerCorporations): ?>
			<h4><?php echo JText::_('No owner corporation set'); ?></h4>
			<ol>
				<li>
					<?php echo 
					JText::sprintf('COM_EVE_SET_OWNER_HINT_REGISTER_ACCOUNTS', 
						JRoute::_('index.php?option=com_eve&view=accounts')); ?> 
				</li>
				<li><?php echo 
					JText::sprintf('COM_EVE_SET_OWNER_HINT_LINKS', 
						JRoute::_('index.php?option=com_eve&view=corporations'), 
						JRoute::_('index.php?option=com_eve&view=alliances')); ?> 
				</li>
				<li>
					<?php echo JText::_('COM_EVE_SET_OWNER_HINT_SET_OWNER'); ?>
				</li>
			</ol>
		<?php else: ?>
			<table class="adminlist">
				<tr>
					<th class="title">
						<?php echo JText::_('CORPORATION NAME'); ?>
					</th>
					<th class="title">
						<?php echo JText::_('CEO NAME' ); ?>
					</th>
					<th class="title">
						<?php echo JText::_('API KEY STATUS'); ?>
					</th>
				</tr>
				<?php foreach ($this->ownerCorporations as $item): ?>
					<tr>
						<td>
							<span><?php echo $this->escape($item->corporationName); ?></span> (<?php echo $item->corporationID; ?>)
						</td>
						<td>
							<?php if (!is_null($item->ceoName)): ?>
								<?php echo $this->escape($item->ceoName); ?>
							<?php endif; ?>
						</td>
						<td>
							<?php if (!is_null($item->apiStatus)): ?>
								<span class="apiStatus apiStatus-<?php echo $item->apiStatus; ?>"><?php echo $item->apiStatus; ?></span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php endif; ?>
	<?php echo $pane->endPanel(); ?>
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

