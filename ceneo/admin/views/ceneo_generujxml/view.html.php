<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class Porownywarki_VM2ViewCeneo_generujxml extends JView
{
    function display($tpl = null)
    {
        JToolBarHelper::title(JText::_('Integracja z porównywarkami cen dla Virtuemart 2 - Ceneo'), 'generic.png');
        JToolBarHelper::back('Wróc do menu Ceneo', 'index.php?option=com_porownywarki_vm2&controller=ceneo');

        // model
        $model = $this->getModel();
        $model->getConfig();

        if (ALSO_WITHOUT_LINKED_CATS == ' LEFT ') {
            JError::raiseNotice(100, 'UWAGA - opcja w konfiguracji pluginu "Generuj XML dla produktów bez powiązanych kategorii między kategoriami sklepu a kategoriami Ceneo" jest ustawiona na <b>TAK</b>, co może skutkować błędym wyświetlaniem cen produktów w XMLu dla Ceneo (dla produktów w których ustawiono rabaty/reguły obliczeniowe).<br /><br />Jeśli używasz rabatów/reguł obliczeniowych w VM2:
				<ol>
					<li>ustaw wspominaną powyżej opcję na "NIE"</li>
					<li>połącz swoje kategorie VM2 z kategoriami Ceneo</li>
					<li>ustaw odpowiednią dla siebie wartość dla opcji "Rabaty" w konfiguracji pluginu </li>
				</ol>');
        }

        parent::display($tpl);
    }
}