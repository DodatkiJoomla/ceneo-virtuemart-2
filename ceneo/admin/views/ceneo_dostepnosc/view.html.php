<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class Porownywarki_VM2ViewCeneo_dostepnosc extends JView
{
    function display($tpl = null)
    {
        // menu
        JToolBarHelper::title(JText::_('Wiązanie dostępności produktów między VM 2 i Ceneo'), 'generic.png');
        JToolBarHelper::addNewX();
        JToolBarHelper::deleteList();
        JToolBarHelper::back('Wróc do menu Ceneo', 'index.php?option=com_porownywarki_vm2&controller=ceneo');


        // model
        $model = $this->getModel();

        // powiazania
        $powiazania = $model->getAvailXrefs();
        $this->assignRef('powiazania', $powiazania);

        parent::display($tpl);
    }
}