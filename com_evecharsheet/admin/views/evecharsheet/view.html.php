<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class EvecharsheetViewEvecharsheet extends JView {

	function display($tmpl = null) {
		global $mainframe;
		$template = $mainframe->getTemplate();

		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/com_eve/assets/common.css');
		$document->addStyleDeclaration('.icon-32-refresh { background-image: url(templates/'.$template.'/images/toolbar/icon-32-refresh.png); }');

		$title = JText::_('Com_Evecharsheet_Eve_Character_Sheet_Title');
		JToolBarHelper::title($title, 'character');
		JToolBarHelper::preferences('com_evecharsheet', 480, 640);
		JToolBarHelper::custom('', 'refresh', 'refresh', 'Com_Evecharsheet_Task_Refresh', false);


		$model = $this->getModel();
		$tables = $model->getTableCheck();


		$this->assignRef('tables', $tables);

		parent::display($tmpl);

	}
}