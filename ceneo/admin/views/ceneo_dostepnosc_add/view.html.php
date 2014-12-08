<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class Porownywarki_VM2ViewCeneo_dostepnosc_add extends JView
{
    function display($tpl = null)
    {
        // menu
        JToolBarHelper::title(JText::_('Nowe powiązanie kategorii'), 'generic.png');
        JToolBarHelper::save();
        JToolBarHelper::back('Wróc do listy powiązań',
            'index.php?option=com_porownywarki_vm2&controller=ceneo_dostepnosc');

        // model
        $model = $this->getModel();

        $task = JRequest::getVar('task', '');
        $vm_avails_selected = JRequest::getVar('vm_avails_selected', array(), 'default', 'ARRAY');
        $ceneo_avail_selected = JRequest::getVar('ceneo_avail_selected', 0);
        $vm_avails = $model->getVmAvails();
        $ceneo_avails = $model->getCeneoAvails();

        $this->assignRef('task', $task);
        $this->assignRef('vm_avails', $vm_avails);
        $this->assignRef('ceneo_avails', $ceneo_avails);
        $this->assignRef('vm_avails_selected', $vm_avails_selected);
        $this->assignRef('ceneo_avail_selected', $ceneo_avail_selected);


        parent::display($tpl);
    }
}