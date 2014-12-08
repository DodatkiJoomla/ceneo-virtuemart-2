<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class Porownywarki_VM2ControllerCeneo_konfiguracja extends Porownywarki_VM2Controller
{
    function __construct()
    {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function display()
    {
        JRequest::setVar('view', 'ceneo_konfiguracja');
        JRequest::setVar('layout', 'form');
        parent::display();
    }

    function edit()
    {
        JRequest::setVar('view', 'ceneo_kategorie_add');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }

    function save()
    {
        $model =& $this->getModel("Ceneo_konfiguracja", "Porownywarki_VM2Model");
        $model->setConfig($_POST['configi']);
        JFactory::getApplication()->enqueueMessage('Zapisano ustawienia');
        $this->display();
    }

    function cancel()
    {
        $msg = JText::_('Operacja zakończona');
        $this->setRedirect('index.php?option=com_szpital&controller=edycja', $msg);
    }
}