<?php
/**
 * @version		$Id$
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<table class="list">
	<thead>
		<tr>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Job_ID', 'ij.jobID', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Installed_Item_Type_Name', 'installedItemTypeName', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Output_Item_Type_Name', 'outputItemTypeName', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Installed_In_Solar_System_Name', 'installedInSolarSystemName', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Runs', 'ij.runs', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Installed_Item_Licensed_Production_Runs_Remaining', 'ij.installedItemLicensedProductionRunsRemaining', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Licensed_Production_Runs', 'ij.licensedProductionRuns', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Installer_Name', 'installerName', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Completed', 'ij.completed', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Installed_Item_Productivity_Level', 'ij.installedItemProductivityLevel', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Installed_Item_Material_Level', 'ij.installedItemMaterialLevel', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Material_Multiplier', 'ij.materialMultiplier', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Char_Material_Multiplier', 'ij.charMaterialMultiplier', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Time_Multiplier', 'ij.timeMultiplier', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Char_Time_Multiplier', 'ij.charTimeMultiplier', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>

			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Install_Time', 'ij.installTime', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Begin_Production_Time', 'ij.beginProductionTime', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_End_Production_Time', 'ij.endProductionTime', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>

			<?php /*
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Output_Flag_Name', 'outputFlagName', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Installed_Item_Flag_Name', 'installedItemFlagName', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Activity_Name', 'activityName', 
					$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			*/ ?>

			<?php if ($this->state->get('entity') == 'user'): ?>
				<th><?php echo JHTML::_('grid.sort',  'Com_Eveindustryjobs_Character', 'characterName', 
						$this->listState->get('list.direction'), $this->listState->get('list.ordering')); ?></th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->items as $item): ?>
		<tr>
			<td><?php echo $item->jobID; ?></td>
			<td>
				<?php echo JHTML::_('evelink.type', array($item, 'installedItemType')); ?>
				<?php if ($item->installedItemCopy): ?>
					(<?php echo JText::_('Com_Eveindustryjobs_Blueprint_Copy'); ?>)
				<?php else: ?>
					(<?php echo JText::_('Com_Eveindustryjobs_Blueprint_Original'); ?>)
				<?php endif; ?>
			</td>
			<td><?php echo JHTML::_('evelink.type', array($item, 'outputType')); ?></td>
			<td><?php echo JHTML::_('evelink.solarSystem', array($item, 'installedInSolarSystem')); ?></td>
			<td class="number"><?php echo number_format($item->runs); ?></td>
			<td class="number">
				<?php if ($item->installedItemLicensedProductionRunsRemaining < 0): ?>
					<?php echo JText::_('Com_Eveindustryjobs_Unlimited_Runs'); ?>
				<?php else: ?>
					<?php echo number_format($item->installedItemLicensedProductionRunsRemaining); ?>
				<?php endif; ?>
			</td>
			<td class="number"><?php echo number_format($item->licensedProductionRuns); ?></td>
			<td><?php echo $this->escape($item->installerName); ?></td>
			<td>
			<?php
				if ($item->completed) {
					if ($item->completedSuccessfully == 0) {
						echo JText::_('Com_Eveindustryjobs_Completed_Status_Failed');
					} elseif ($item->completedSuccessfully == 1) {
						echo JText::_('Com_Eveindustryjobs_Completed_Status_Delivered');
					} elseif ($item->completedSuccessfully == 2) {
						echo JText::_('Com_Eveindustryjobs_Completed_Status_Aborted');
					} elseif ($item->completedSuccessfully == 3) {
						echo JText::_('Com_Eveindustryjobs_Completed_Status_GM_Aborted');
					} elseif ($item->completedSuccessfully == 4) {
						echo JText::_('Com_Eveindustryjobs_Completed_Status_Inflight_Unanchored');
					} elseif ($item->completedSuccessfully == 5) {
						echo JText::_('Com_Eveindustryjobs_Completed_Status_Destroyed');
					}
				} else {
					echo JText::_('Com_Eveindustryjobs_Completed_Status_No');
				}
			?>
			</td>
			
			<td class="number"><?php echo number_format($item->installedItemProductivityLevel); ?></td>
			<td class="number"><?php echo number_format($item->installedItemMaterialLevel); ?></td>
			<td class="number"><?php echo number_format($item->materialMultiplier, 2); ?></td>
			<td class="number"><?php echo number_format($item->charMaterialMultiplier, 2); ?></td>
			<td class="number"><?php echo number_format($item->timeMultiplier, 2); ?></td>
			<td class="number"><?php echo number_format($item->charTimeMultiplier, 2); ?></td>
			
			<td><?php echo JHTML::date($item->installTime, JText::_('Com_Eveindustryjobs_Format_Datetime_List')); ?></td>
			<td><?php echo JHTML::date($item->beginProductionTime, JText::_('Com_Eveindustryjobs_Format_Datetime_List')); ?></td>
			<td>
				<?php if ($item->pauseProductionTime == '0001-01-01 00:00:00'): ?>
					<?php echo JHTML::date($item->endProductionTime, JText::_('Com_Eveindustryjobs_Format_Datetime_List')); ?>
				<?php else: ?>
					<span class="paused">
						<?php echo JHTML::date($item->pauseProductionTime, JText::_('Com_Eveindustryjobs_Format_Datetime_List')); ?>
						(<?php echo JText::_('Com_Eveindustryjobs_Job_Paused'); ?>)
					</span>
				<?php endif; ?>
			</td>
			
			<?php /*
			<td><?php echo $this->escape($item->outputFlagName); ?></td>
			<td><?php echo $this->escape($item->installedItemFlagName); ?></td>
			<td><?php echo $this->escape($item->activityName); ?></td>
			*/ ?>

			<?php if ($this->state->get('entity') == 'user'): 
				$character = $this->characters[$item->entityID];
				?>
				<td>
					<a href="<?php echo EveRoute::_('charindustryjobs', $character, $character, $character); ?>">
						<?php echo $this->escape($item->characterName); ?>
					</a>
				</td>
			<?php endif; ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>