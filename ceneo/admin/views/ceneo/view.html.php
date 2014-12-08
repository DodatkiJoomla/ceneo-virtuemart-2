<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class Porownywarki_VM2ViewCeneo extends JView
{
    function display($tpl = null)
    {
        JToolBarHelper::title(JText::_('Integracja z porównywarkami cen dla Virtuemart 2 - Ceneo'), 'generic.png');
        JToolBarHelper::back('Wróc do menu głównego', 'index.php?option=com_porownywarki_vm2');

        // model
        $model = $this->getModel();
        $ceneoTableCount = $model->checkCeneoCatsTable();
        $this->assignRef('ceneoTableCount', $ceneoTableCount);

        parent::display($tpl);
    }
}