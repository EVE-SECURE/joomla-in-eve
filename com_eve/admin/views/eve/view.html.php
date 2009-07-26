<?php
/**
 * @version		$Id$
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Core
 * @copyright	Copyright (C) 2008 Pavol Kovalik. All rights reserved.
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

jimport( 'joomla.application.component.view');

class EveViewEve extends JView {
	public $item;
	
	public function display($tpl = null) {
		/*
		$item = $this->get('Item');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->assignRef('item', $item);
		*/		

		parent::display($tpl);
		$this->_setToolbar();
	}
	
	public function addIcon($image , $view, $text) {
		$link = JRoute::_('index.php?option=com_eve&view='.$view);
	?>
		<div style="float:left;">
			<div class="icon">
				<a href="<?php echo $link; ?>">
					<?php echo JHTML::_('image', 'administrator/components/com_eve/assets/' . $image , NULL, NULL, $text ); ?>
					<span><?php echo $text; ?></span></a>
			</div>
		</div>
	<?php
	}
	
	public function addParameters($image, $text ) {
		$app = JFactory::getApplication();
		$template = $app->getTemplate();
		
		$link = JRoute::_('index.php?option=com_config&controller=component&component=com_eve&path=');
	?>
		<div style="float:left;">
			<div class="icon">
				<a href="<?php echo $link; ?>" rel="{handler: 'iframe', size: {x: 640, y: 480}}" class="modal">
					<?php echo JHTML::_('image.site',  $image, '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
					<span><?php echo $text; ?></span></a>
			</div>
		</div>
	<?php
	}

	
	protected function _setToolbar() {
		$title = JText::_('Joomla! in EVE');
		JToolBarHelper::title($title, 'eve');
		
		/*
		JRequest::setVar('hidemainmenu', 1);

		if ($this->item->characterID > 0) {
		} else {
			$title = JText::_('NEW CHARACTER');
		}
		
		JToolBarHelper::apply('character.apply');
		JToolBarHelper::save('character.save');
		JToolBarHelper::addNew('character.save2new', 'Save and new');
		if ($this->item->userID > 0) {
			JToolBarHelper::cancel('character.cancel');
		} else {
			JToolBarHelper::cancel('character.cancel', 'Close');
		}
		*/
	}
	
}