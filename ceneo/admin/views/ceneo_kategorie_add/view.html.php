<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class Porownywarki_VM2ViewCeneo_kategorie_add extends JView
{
    function display($tpl = null)
    {
        // menu
        JToolBarHelper::title(JText::_('Nowe powiązanie kategorii'), 'generic.png');
        JToolBarHelper::save();
        JToolBarHelper::back('Wróc do listy powiązań',
            'index.php?option=com_porownywarki_vm2&controller=ceneo_kategorie');


        // model
        $model = JModel::getInstance("Ceneo_kategorie", "Porownywarki_VM2Model");

        $vm_categories = $model->getVmCategories();
        $ceneo_categories = $model->getCeneoCategories();
        $vm_cat = JRequest::getVar('vm_cat', 0, 'default', 'ARRAY');
        $ceneo_cat = JRequest::getVar('ceneo_cat', 0);
        $ceneo_category_types = $model->getCeneoCategoryTypes("", true);
        $ceneo_category_types_selected = JRequest::getVar('ceneo_category_type', 0);
        $ceneo_category_types2 = $model->getCeneoCategoryTypes($ceneo_category_types_selected);
        $custom_fields = $model->getCustomFields();


        $this->assignRef('vm_categories', $vm_categories);
        $this->assignRef('ceneo_categories', $ceneo_categories);
        $this->assignRef('vm_cat', $vm_cat);
        $this->assignRef('ceneo_cat', $ceneo_cat);
        $this->assignRef('ceneo_category_types', $ceneo_category_types);
        $this->assignRef('ceneo_category_types_selected', $ceneo_category_types_selected);
        $this->assignRef('ceneo_category_types2', $ceneo_category_types2);
        $this->assignRef('custom_fields', $custom_fields);
        $this->assignRef('selected_custom_fields_codes', $selected_custom_fields_codes);


        parent::display($tpl);
    }
}