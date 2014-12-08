<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');


class Porownywarki_VM2ViewCeneo_wykluczone_produkty_add extends JView
{
    function display($tpl = null)
    {
        // menu
        JToolBarHelper::title(JText::_('Nowy wykluczony produkt'), 'generic.png');
        JToolBarHelper::save();
        JToolBarHelper::back('Wróc do listy kategorii',
            'index.php?option=com_porownywarki_vm2&controller=ceneo_wykluczone_produkty');

        // model
        require_once(JPATH_COMPONENT_ADMINISTRATOR . '/models/ceneo_wykluczone_produkty.php');
        $model = new Porownywarki_VM2ModelCeneo_wykluczone_produkty();
        $cats = $model->getUnexcludedItems();

        $this->assignRef('cats', $cats);

        parent::display($tpl);
    }
}