<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class Porownywarki_VM2ViewCeneo_kategorie extends JView
{
    function display($tpl = null)
    {
        // menu
        JToolBarHelper::title(JText::_('Wiązanie kategorii VM z kategoriami Ceneo'), 'generic.png');
        JToolBarHelper::addNewX();
        JToolBarHelper::editListX();
        JToolBarHelper::deleteList();
        JToolBarHelper::back('Wróc do menu Ceneo', 'index.php?option=com_porownywarki_vm2&controller=ceneo');

        // model
        $model = $this->getModel();

        // filter
        $search = JRequest::getVar('search', '');
        $this->assignRef('search', $search);

        // powiazanie
        $powiazania = $model->getXrefs($search);
        $this->assignRef('powiazania', $powiazania);

        parent::display($tpl);
    }
}