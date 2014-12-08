<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class Porownywarki_VM2ViewCeneo_konfiguracja extends JView
{
    function display($tpl = null)
    {
        // menu
        JToolBarHelper::title(JText::_('Konfiguracja modułu Ceneo'), 'generic.png');
        JToolBarHelper::save();
        JToolBarHelper::back('Wróć do menu Ceneo', 'index.php?option=com_porownywarki_vm2&controller=ceneo');

        // model
        $model = $this->getModel();
        $config = $model->getConfig();

        $this->assignRef('config', $config);

        parent::display($tpl);
    }
}