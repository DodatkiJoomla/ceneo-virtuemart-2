<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class Porownywarki_VM2ControllerCeneo_kategorie extends Porownywarki_VM2Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function display()
    {
        JRequest::setVar('view', 'ceneo_kategorie');
        JRequest::setVar('layout', 'default');
        parent::display();
    }

    function add()
    {
        JRequest::setVar('view', 'ceneo_kategorie_add');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }

    function edit()
    {
        JRequest::setVar('view', 'ceneo_kategorie_edit');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }

    public function ajaxGetCats()
    {
        $model =& $this->getModel("Ceneo_kategorie", "Porownywarki_VM2Model");
        $type = JRequest::getVar("type", "vm");
        $like = JRequest::getVar("like", "");
        $multiple = JRequest::getVar("multiple", 1);

        switch ($type) {
            case "vm":
                $vm_cats = $model->getVmCategories($like);
                if ($multiple) {
                    echo '<select name="vm_cat" multiple="multiple" size="15">';
                } else {
                    echo '<select name="vm_cat[]">';
                }
                foreach ($vm_cats as $vm_cat) {
                    echo "<option value='" . $vm_cat->category_id . "'>[ID:" . $vm_cat->category_id . "] " . $vm_cat->category_name . "</option>";
                }
                echo '</select>';
                break;
            case "ceneo":
                $ceneo_cats = $model->getCeneoCategories($like);
                echo '<select name="ceneo_cat" >';
                echo "<option value=''>-- wybierz kategorię --</option>";
                foreach ($ceneo_cats as $ceneo_cat) {
                    echo "<option value='" . $ceneo_cat->ceneo_cat_id . "'>" . $ceneo_cat->link . "</option>";
                }
                echo '</select>';
                break;
        }

        // zamykamy żeby nie wywalało templaty
        exit();
    }

    public function ajaxGetAttributes()
    {
        $model =& $this->getModel("Ceneo_kategorie", "Porownywarki_VM2Model");
        $parent_id = JRequest::getVar("parent_id", "");

        $ceneo_category_types_all = $model->getCeneoCategoryTypes($parent_id);
        $custom_fields = $model->getCustomFields();
        echo "<table id='fields_and_attribs' border='0' style='float: left; width: auto; margin: 5px 5px 5px 0;'>";
        foreach ($ceneo_category_types_all as $cat_type) {
            echo "<tr><td>" . $cat_type->name . "</td><td>";
            echo "<select name='customF[]'>";
            echo "<option value=''>-- puste --</option>";

            // producent
            $selected_mf = "";
            if ($cat_type->name == "Producent" || $cat_type->name == "Wytwornia" || $cat_type->name == "Wydawnictwo") {
                $selected_mf = " selected='selected' ";
            }

            echo "
			<optgroup label='Producent'>
				<option " . $selected_mf . " value='" . $cat_type->id . "_mfname'>Nazwa producenta</option>
			</optgroup>
			<optgroup label='Produkt'>
				<option value='" . $cat_type->id . "_sku'>SKU produktu</option>
			</optgroup>
			<optgroup label='Pola dodatkowe'>";
            foreach ($custom_fields as $field) {
                echo "<option value='" . $cat_type->id . "_" . $field->virtuemart_custom_id . "'>" . $field->custom_title . "</option>";
            }
            echo "</optgroup></select>";
            $default = ($cat_type->is_default ? 'atrybut domyślny' : 'atrybut własny &nbsp;&nbsp;&nbsp;<a href="JavaScript:void(0);" onclick="deleteAttribute(' . $cat_type->id . ', this);">Usuń atrybut</a>');
            echo "<td>" . $default . "</td></tr>";
        }
        echo "</table>";

        // zamykamy żeby nie wywalało templaty
        exit();
    }

    public function save()
    {
        $vm_cats = JRequest::getVar('vm_cat', 0, 'default', 'array');
        $ceneo_cat = JRequest::getVar('ceneo_cat', 0);
        $custom_fields_req = JRequest::getVar('customF', 0, 'default', 'array');
        $ceneo_category_type = JRequest::getVar('ceneo_category_type', 0);
        $xref_id = JRequest::getVar('xref_id', 0);
        JRequest::setVar('vm_cat', $vm_cats);
        JRequest::setVar('ceneo_cat', $ceneo_cat);

        $ceneo_zakres_produktow = implode(",", JRequest::getVar('ceneo_zakres_produktow', array(), 'default', 'array'));

        $model =& $this->getModel("Ceneo_kategorie", "Porownywarki_VM2Model");

        $custom_fields = array();

        foreach ($custom_fields_req as $field) {
            if (!empty($field)) {
                $field_object = new stdClass();

                $f_array = explode("_", $field);

                // atrybuty
                $attribute_id = $f_array[0];

                // pola dodatkowe
                $custom_field_id = $f_array[1];


                $field_object->attribute_id = $attribute_id;


                $attribute = $model->getCeneoCategoryTypes("", false, $attribute_id);
                if (!empty($attribute)) {
                    $field_object->attribute_name = str_replace("'", " ", $attribute->name);
                }


                if ($custom_field_id == "mfname") {
                    $field_object->custom_field_id = -1;
                    $field_object->custom_field_title = "Producent";
                } else {
                    if ($custom_field_id == "sku") {
                        $field_object->custom_field_id = -2;
                        $field_object->custom_field_title = "SKU";
                    } else {
                        $field_object->custom_field_id = $custom_field_id;
                        $custom_field = $model->getCustomFields($custom_field_id);
                        if (!empty($custom_field)) {
                            $field_object->custom_field_title = str_replace("'", " ", $custom_field->custom_title);
                        }
                    }
                }

                if (!empty($field_object)) {
                    $custom_fields[] = $field_object;
                }
            }
        }

        if (!empty($custom_fields)) {
            $custom_fields = serialize($custom_fields);
        } else {
            unset($custom_fields);
            $custom_fields = "";
        }


        if (empty($vm_cats) || empty($ceneo_cat)) {
            JError::raiseWarning(100,
                'Błąd: Wypełnij wymagane pola - wybierz kategorię Virtuemart i odpowiadającą jej kategorię Ceneo.');
            $this->add();
            return true;
        } else {
            if (!$xref_id) {
                foreach ($vm_cats as $vm_cat) {
                    if (!$model->setXref($vm_cat, $ceneo_cat, $ceneo_category_type, $custom_fields,
                        $ceneo_zakres_produktow)
                    ) {
                        JError::raiseWarning(100, 'Błąd: Wystąpił błąd podczas zapisu do bazy danych.');
                        $this->add();
                        return true;
                    }

                }
            } else {
                $vm_cat = $vm_cats[0];
                if (!$model->updateXref($vm_cat, $ceneo_cat, $ceneo_category_type, $custom_fields,
                    $ceneo_zakres_produktow, $xref_id)
                ) {
                    JError::raiseWarning(100, 'Błąd: Wystąpił błąd podczas zapisu do bazy danych.');
                    $this->add();
                    return true;
                }
            }
            $japp = JFactory::getApplication();
            $japp->redirect("index.php?option=com_porownywarki_vm2&controller=ceneo_kategorie", "Zapisano rekord.");

        }

    }

    public function remove()
    {
        $cid = JRequest::getVar('cid');

        if (count($cid) > 0) {
            $model =& $this->getModel("Ceneo_kategorie", "Porownywarki_VM2Model");
            if (!$model->deleteXref($cid)) {
                JError::raiseWarning(100, 'Błąd: Wystąpił błąd podczas usuwania rekordu.');
                $this->display();
                return true;
            }
            $japp = JFactory::getApplication();
            $japp->redirect("index.php?option=com_porownywarki_vm2&controller=ceneo_kategorie", "Usunięto rekord.");
        }
    }


    function cancel()
    {
        $msg = JText::_('Operacja zakończona');
        $this->setRedirect('index.php?option=com_szpital&controller=edycja', $msg);
    }

    public function save_attribute()
    {
        $additional_field_parent = JRequest::getVar('additional_field_parent', 0);
        $additional_field_name = JRequest::getVar('additional_field_name', '');
        $model = $this->getModel("Ceneo_kategorie", "Porownywarki_VM2Model");
        $result = $model->addNewAttribute($additional_field_parent, $additional_field_name);
        if ($result != 0) {
            echo $result;
        } else {
            echo "Wystąpił błąd podczas zapisywania atrybutu do bazy danych.";
        }

        exit();
    }

    public function delete_attribute()
    {
        $attribute_id = JRequest::getVar('attribute_id', 0);

        $model = $this->getModel("Ceneo_kategorie", "Porownywarki_VM2Model");
        $result = $model->deleteAttribute($attribute_id);
        if ($result) {
            echo "1";
        } else {
            echo "0";
        }

        exit();
    }


}