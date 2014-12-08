<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class Porownywarki_VM2ControllerCeneo_dostepnosc extends Porownywarki_VM2Controller
{
    function __construct()
    {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function display()
    {
        JRequest::setVar('view', 'ceneo_dostepnosc');
        JRequest::setVar('layout', 'default');
        parent::display();
    }


    function edit()
    {
        JRequest::setVar('view', 'ceneo_dostepnosc_add');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }


    public function save()
    {

        $vm_avails_selected = JRequest::getVar('vm_avails_selected', array(), 'default', 'ARRAY');
        $ceneo_avail_selected = JRequest::getVar('ceneo_avail_selected', 0);

        $model =& $this->getModel("Ceneo_dostepnosc_add", "Porownywarki_VM2Model");


        if (empty($vm_avails_selected) || empty($ceneo_avail_selected)) {
            JError::raiseWarning(100,
                'Błąd: Wypełnij wymagane pola - wybierz dostępności Virtuemart i przypisz do nich dostępność Ceneo.');
            $this->edit();
            return true;
        } else {
            foreach ($vm_avails_selected as $vm_avail_selected) {
                if (!$model->setXref($vm_avail_selected, $ceneo_avail_selected)) {
                    JError::raiseWarning(100, 'Błąd: Wystąpił błąd podczas zapisu do bazy danych.');
                    $this->edit();
                    return true;
                }

            }
            $japp = JFactory::getApplication();
            $japp->redirect("index.php?option=com_porownywarki_vm2&controller=ceneo_dostepnosc", "Zapisano rekord/y.");

        }

    }

    public function remove()
    {
        $cid = JRequest::getVar('cid');

        if (count($cid) > 0) {
            $model =& $this->getModel("Ceneo_dostepnosc_add", "Porownywarki_VM2Model");
            if (!$model->deleteXref($cid)) {
                JError::raiseWarning(100, 'Błąd: Wystąpił błąd podczas usuwania rekordu.');
                $this->display();
                return true;
            }
            $japp = JFactory::getApplication();
            $japp->redirect("index.php?option=com_porownywarki_vm2&controller=ceneo_dostepnosc", "Usunięto rekord.");
        }
    }
}