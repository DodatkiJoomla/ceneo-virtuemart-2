<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class Porownywarki_VM2ViewCeneo_kategorie_edit extends JView
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

        $cid = JRequest::getVar('cid', array());
        if (count($cid) == 1 && $cid[0] > 0) {
            $xref_id = $cid[0];
            $record = $model->getXref($xref_id);

            if ($record != false) {
                $vm_categories = $model->getVmCategories("", $record->virtuemart_cat_id);
                $ceneo_categories = $model->getCeneoCategories();
                $vm_cat = $record->virtuemart_cat_id;
                $ceneo_cat = $record->ceneo_cat_id;
                $ceneo_category_types = $model->getCeneoCategoryTypes("", true);
                $ceneo_category_types_selected = $record->ceneo_category_type;
                $ceneo_category_attribs = $model->getCeneoCategoryTypes($ceneo_category_types_selected);
                $custom_fields = $model->getCustomFields();
                $selected_custom_fields = unserialize($record->custom_fields);
                $selected_custom_fields_codes = array();
                foreach ($selected_custom_fields as $custom_field_obj) {
                    if ($custom_field_obj->custom_field_id == "-1") {
                        $custom_field_obj->custom_field_id = "mfname";
                    } else {
                        if ($custom_field_obj->custom_field_id == "-2") {
                            $custom_field_obj->custom_field_id = "sku";
                        }
                    }
                    $selected_custom_fields_codes[] = $custom_field_obj->attribute_id . "_" . $custom_field_obj->custom_field_id;
                }

                // 20.05.2013 Dodaję produkty z kategrorii VM do ograniczenia wyników
                $vm_category_products = $model->getVMCategoryProducts($record->virtuemart_cat_id);

                $this->assignRef('vm_categories', $vm_categories);
                $this->assignRef('vm_categories', $vm_categories);
                $this->assignRef('ceneo_categories', $ceneo_categories);
                $this->assignRef('vm_cat', $vm_cat);
                $this->assignRef('ceneo_cat', $ceneo_cat);
                $this->assignRef('ceneo_category_types', $ceneo_category_types);
                $this->assignRef('ceneo_category_types_selected', $ceneo_category_types_selected);
                $this->assignRef('ceneo_category_attribs', $ceneo_category_attribs);
                $this->assignRef('custom_fields', $custom_fields);
                $this->assignRef('selected_custom_fields_codes', $selected_custom_fields_codes);
                $this->assignRef('xref_id', $xref_id);
                $this->assignRef('vm_category_products', $vm_category_products);
                $this->assignRef('vm_category_products_selected', explode(",", $record->ceneo_zakres_produktow));

                parent::display($tpl);
            } else {
                JError::raiseWarning(100, 'Nie można pobrać rekordu o takim ID.');
                return false;
            }
        } else {
            JError::raiseWarning(100, 'Wybierz pojedyńczy rekord do edycji.');
            return false;
        }
    }
}