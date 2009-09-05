<?php

/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Community Builder - Character Sheet
 * @copyright	Copyright (C) 2009 Pavol Kovalik. All rights reserved.
 * @license		GNU/GPL, see http://www.gnu.org/licenses/gpl.html
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * Basic tab extender. Any plugin that needs to display a tab in the user profile
 * needs to have such a class. Also, currently, even plugins that do not display tabs (e.g., auto-welcome plugin)
 * need to have such a class if they are to access plugin parameters (see $this->params statement).
 */

class getEvecharsheetTab extends cbTabHandler
{
	//Construnctor
	public function __construct()
	{
		$this->cbTabHandler();
	}
	
	/**
	* Generates the HTML to display the user profile tab
	* @param object tab reflecting the tab database entry
	* @param object mosUser reflecting the user being displayed
	* @param int 1 for front-end, 2 for back-end
	* @returns mixed : either string HTML for tab content, or false if ErrorMSG generated


	*/
	public function getDisplayTab($tab, $user, $ui)
	{
		JComponentHelper::isEnabled('com_eve', true);
		JComponentHelper::isEnabled('com_evecharsheet', true);
		jimport('joomla.application.component.model');
		JModel::addIncludePath(JPATH_SITE.DS.'components'.DS.'com_evecharsheet'.DS.'models');
		$this->model = JModel::getInstance('Sheet', 'EvecharsheetModel');
		$characters = $this->getCharacters($user);
		//return 'hello';
		
		$result = '';
		switch (count($characters)) {
			case 0:
				break;
			case 1:
				$character = reset($characters);
				$result .= $this->displayCharacter($character);
				break;
			default:
				jimport('joomla.html.pane');
				$tabs	=& JPane::getInstance('tabs');
				$result .= $tabs->startPane("evecharsheet-pane");
				foreach ($characters as $character) {
					$result .= $tabs->startPanel( $character->name, 'character' . $character->characterID );
					$result .= $this->displayCharacter($character);
					$result .= $tabs->endPanel();
				}
				$result .= $tabs->endPane();
				break;
		}
		return $result;
	}
	
	private function getCharacters($user)
	{
		$owner = $user->user_id;
		$dbo = JFactory::getDBO();
		$q = new JQuery($dbo);
		$q->addTable('#__eve_characters', 'ch');
		$q->addJoin('#__eve_accounts', 'ac', 'ac.userID=ch.userID');
		$q->addJoin('#__eve_corporations', 'co', 'co.corporationID=ch.corporationID');
		$q->addJoin('#__eve_alliances', 'al', 'co.allianceID=al.allianceID');
		$q->addJoin('#__users', 'us', 'ac.owner=us.id');
		
		$q->addQuery('ch.*');
		$q->addQuery('co.corporationID', 'co.corporationName', 'co.ticker');
		$q->addQuery('al.allianceID', 'al.name AS allianceName', 'al.shortName');
		$q->addQuery('ac.owner', 'us.name AS ownerName');
		$q->addOrder('name');
		$q->addWhere('ac.owner = %s', intval($owner));
		if (!$this->params->get('listallcharacters', 0)) {
			$corps = EveHelper::getOwnerCoroprationIDs($dbo);
			if (!$corps) {
				return array();
			} else {
				$q->addWhere('ch.corporationID IN (%s)', implode(', ', $corps));
			}
		}
		return $q->loadObjectList();
		
		$pane = JPane::getInstance();
	}
	
	private function displayNone()
	{
		?>
		No characters known
		<?php
	}
	
	private function displayCharacter($character)
	{
		$this->character = $character;
		$this->groups = $this->model->getSkillGroups($character->characterID);
		$this->queue = $this->model->getQueue($character->characterID);
		$this->categories = $this->model->getCertificateCategories($character->characterID);
		$this->attributes = $this->model->getAttributes($character->characterID);
		$this->roles = $this->model->getRoles($character->characterID);
		$this->roleLocations = $this->model->getRoleLocations();
		$this->titles = $this->model->getTitles($character->characterID);
		
		ob_start();
		?>
		<div>
			<img src="http://img.eve.is/serv.asp?s=256&c=<?php echo $this->character->characterID; ?>" /> <br />
			<?php echo JText::_('Character Name'); ?>: <?php echo $this->character->name; ?> <br />
			<?php echo JText::_('Race'); ?>: <?php echo $this->character->race; ?> <br />
			<?php echo JText::_('Gender'); ?>: <?php echo $this->character->gender; ?> <br />
			<?php echo JText::_('Blood Line'); ?>: <?php echo $this->character->bloodLine; ?> <br />
			<?php echo JText::_('Ballance'); ?>: <?php echo number_format($this->character->balance); ?> <br />
			<?php echo JText::_('Corporation'); ?>: 
				<a href="<?php echo JRoute::_('index.php?option=com_evecharsheet&view=list&layout=corporation&corporationID='.$this->character->corporationID); ?>">
					<?php echo $this->character->corporationName; ?> [<?php echo $this->character->ticker; ?>]
				</a> <br />
		</div>
		
		<div>
			<h3><?php echo JText::_('Attributes'); ?></h3>
			<table>
				<?php foreach ($this->attributes as $attribute): ?>
					<tr>
						<td><?php echo $attribute->attributeName; ?></td>
						<td><?php echo $attribute->value; ?> + <?php echo $attribute->augmentatorValue; ?></td>
						<td><?php echo $attribute->augmentatorName; ?></td>
						
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
		
		<div>
			<h3><?php echo JText::_('Skill Queue'); ?></h3>
			<table>
			<?php foreach ($this->queue as $skill): ?>
				<tr>
					<td>
						<?php echo $skill->queuePosition + 1; ?>
					</td>
					<td class="skill-label" title="<?php echo $skill->description; ?>" >
						<?php echo $skill->typeName; ?>
					</td>
					<td class="skill-level">
						<img src="<?php echo JURI::base(); ?>components/com_evecharsheet/assets/level<?php echo $skill->level; ?>.gif" border="0" alt="Level <?php echo $skill->level; ?>" title="<?php echo number_format($skill->endSP); ?>" />
					</td>
					<td>
						<?php echo JHTML::_('date', $skill->startTime, JText::_('DATE_FORMAT_LC2')); ?>
					</td>
					<td>
						<?php echo JHTML::_('date', $skill->endTime, JText::_('DATE_FORMAT_LC2')); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</table>
		</div>
		
		
		<div>
		<h3><?php echo JText::_('Skills'); ?></h3>
		<?php foreach ($this->groups as $group): ?>
			<h4><?php echo $group->groupName; ?></h4>
			<?php if ($group->skills): ?>
				<table class="skill-group">
				<?php foreach ($group->skills as $skill): ?>
					<tr>
						<td class="skill-label" title="<?php echo $skill->description; ?>" >
							<?php echo $skill->typeName; ?>
						</td>
						<td class="skill-level">
							<img src="<?php echo JURI::base(); ?>components/com_evecharsheet/assets/level<?php echo $skill->level; ?>.gif" border="0" alt="Level <?php echo $skill->level; ?>" title="<?php echo number_format($skill->skillpoints); ?>" />
						</td>
					</tr>
				<?php endforeach; ?>
				</table>
				<div>
					<?php echo JText::sprintf('%s skills trained for total of %s skillpoints', $group->skillCount, number_format($group->skillpoints)); ?><br />
					<?php echo JText::sprintf('Skill Cost %s', number_format($group->skillPrice)); ?>
				</div>
			<?php else: ?>
				<?php echo JText::_('No skills in this category'); ?>
			<?php endif; ?>
		<?php endforeach; ?>
		</div>

		
		<div>
		<h3><?php echo JText::_('Certificates'); ?></h3>
		<?php foreach ($this->categories as $category): ?>
			<h4><?php echo $category->categoryName; ?></h4>
			<?php if ($category->certificates): ?>
				<table class="certificate-category">
				<?php foreach ($category->certificates as $certificate): ?>
					<tr>
						<td class="certificate-label" title="<?php echo $certificate->description; ?>" >
							<?php echo $certificate->className; ?>
						</td>
						<td class="certificate-level">
							<img src="<?php echo JURI::base(); ?>components/com_evecharsheet/assets/level<?php echo $certificate->grade; ?>.gif" border="0" alt="Grate <?php echo $certificate->grade; ?>" title="<?php echo number_format($certificate->grade); ?>" />
						</td>
					</tr>
				<?php endforeach; ?>
				</table>
			<?php else: ?>
				<?php echo JText::_('No certificates in this category'); ?>
			<?php endif; ?>
		<?php endforeach; ?>
		</div>
		
		<div>
		<h3><?php echo JText::_('Roles'); ?></h3>
		<table>
			<tr>
				<th></th>
				<?php foreach ($this->roleLocations as $location): ?>
					<th><?php echo JText::_($location); ?></th>
				<?php endforeach ?>
			<tr>
		<?php foreach ($this->roles as $role): ?>
			<tr>
				<td><?php echo $role->roleName; ?></td>
				<?php foreach ($this->roleLocations as $location): ?>
					<td><?php echo $role->$location; ?></td>
				<?php endforeach ?>
			<tr>
			
		<?php endforeach; ?>
		</table>
		</div>
		
		<div>
		<h3><?php echo JText::_('Titles'); ?></h3>
		<table>
		<?php foreach ($this->titles as $title): ?>
			<tr>
				<td><?php echo $title->titleName; ?></td>
			<tr>
			
		<?php endforeach; ?>
		</table>
		</div>
		
		<?php
		return ob_get_clean();
	}
} 
