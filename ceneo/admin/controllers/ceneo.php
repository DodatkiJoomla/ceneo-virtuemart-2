<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
require_once(JPATH_COMPONENT . DS . 'classes' . DS . 'ceneoCategory.class.php');

class Porownywarki_VM2ControllerCeneo extends Porownywarki_VM2Controller
{
    function __construct()
    {
        parent::__construct();

        // Rejestruj dodatkowe zadania
        $this->registerTask('add', 'edit');
    }

    function display()
    {
        JRequest::setVar('view', 'ceneo');
        JRequest::setVar('layout', 'default');
        parent::display();
    }

    public function updateDB()
    {
        $model =& $this->getModel('ceneo');
        $model->updateCeneoCategories();
        $this->display();
    }

    public function ajaxGetCats()
    {
        $model =& $this->getModel("Add", "CeneoModel");
        $type = JRequest::getVar("type", "vm");
        $like = JRequest::getVar("like", "");
        switch ($type) {
            case "vm":
                $vm_cats = $model->getVmCategories($like);
                echo '<select name="vm_cat">';
                foreach ($vm_cats as $vm_cat) {
                    echo "<option value='" . $vm_cat->category_id . "'>[ID:" . $vm_cat->category_id . "] " . $vm_cat->category_name . "</option>";
                }
                echo '</select>';
                break;
            case "ceneo":
                $ceneo_cats = $model->getCeneoCategories($like);
                echo '<select name="ceneo_cat">';
                foreach ($ceneo_cats as $ceneo_cat) {
                    echo "<option value='" . $ceneo_cat->ceneo_cat_id . "'>" . $ceneo_cat->link . "</option>";
                }
                echo '</select>';
                break;
        }
    }

    public function createXML()
    {
        require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'getProducts.php');
        $products = getProducts();
        if (!empty($products)) {
            require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'ceneoXML.php');
        } else {
            $japp = JFactory::getApplication();
            $japp->redirect("index.php?option=com_porownywarki_vm2&controller=ceneo",
                "Nie można pobrać listy produktów z bazy danych..", "error");
        }
    }
}