<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
require_once(JPATH_COMPONENT . DS . 'classes' . DS . 'ceneoCategory.class.php');

class Porownywarki_VM2ControllerCeneo_generujxml extends Porownywarki_VM2Controller
{
    function __construct()
    {
        parent::__construct();

        // Rejestruj dodatkowe zadania
        $this->registerTask('add', 'edit');
    }

    function display()
    {
        JRequest::setVar('view', 'ceneo_generujxml');
        JRequest::setVar('layout', 'default');
        parent::display();
    }
}