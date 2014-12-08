<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.html.pagination');

require_once(JPATH_COMPONENT_ADMINISTRATOR . '/models/ceneo_konfiguracja.php');


class Porownywarki_VM2ControllerCeneo_wykluczone_produkty extends Porownywarki_VM2Controller
{
    function __construct()
    {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function display()
    {
        JRequest::setVar('view', 'ceneo_wykluczone_produkty');
        JRequest::setVar('layout', 'default');
        parent::display();
    }


    function edit()
    {
        JRequest::setVar('view', 'ceneo_wykluczone_produkty_add');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }


    public function save()
    {
        $jinput = JFactory::getApplication()->input;
        $excluded_prod = $jinput->get('excluded_prod', array(), 'ARRAY');
        $model = $this->getModel("Ceneo_wykluczone_produkty", "Porownywarki_VM2Model");

        if (!count($excluded_prod) || (count($excluded_prod) == 1 && $excluded_prod[0] == 0)) {
            JError::raiseWarning(100, 'Błąd: Wybierz produkt do wykluczenia.');
            $this->edit();
            return true;
        } else {
            $new_config = $model->addExcludedItemAndReturnConfig($excluded_prod);

            if (!$new_config) {
                JError::raiseWarning(100,
                    'Błąd: Wystąpił błąd podczas dodawania rekordu/ów "' . implode(", ", $cid) . '".');
                $this->display();
                return true;
            }

            // Zapisuję i ignoruję operacje używane w zakładce konfiguracja
            if (($new_config instanceof stdClass) && !($set_result = Porownywarki_VM2ModelCeneo_konfiguracja::setConfig($new_config,
                    true))
            ) {
                JError::raiseWarning(100, 'Błąd: Wystąpił błąd podczas zapisywania nowej konfiguracji.');
                $this->display();
                return true;
            }

            $japp = JFactory::getApplication();
            $japp->redirect("index.php?option=com_porownywarki_vm2&controller=ceneo_wykluczone_produkty",
                "Zapisano rekord/y.");

        }

    }

    public function remove()
    {
        $cid = JRequest::getVar('cid');

        if (count($cid) > 0) {
            $model = $this->getModel("Ceneo_wykluczone_produkty", "Porownywarki_VM2Model");

            $new_config = $model->deleteExcludedCatsAndReturnConfig($cid);

            if (!$new_config) {
                JError::raiseWarning(100,
                    'Błąd: Wystąpił błąd podczas usuwania rekordu/ów "' . implode(", ", $cid) . '".');
                $this->display();
                return true;
            }

            // Zapisuję i ignorujęoperacje używane w zakładce konfiguracja
            if (($new_config instanceof stdClass) && !($set_result = Porownywarki_VM2ModelCeneo_konfiguracja::setConfig($new_config,
                    true))
            ) {
                JError::raiseWarning(100, 'Błąd: Wystąpił błąd podczas zapisywania nowej konfiguracji.');
                $this->display();
                return true;
            }


            $japp = JFactory::getApplication();
            $japp->redirect("index.php?option=com_porownywarki_vm2&controller=ceneo_wykluczone_produkty",
                "Usunięto rekord/y.");
        }

    }


}