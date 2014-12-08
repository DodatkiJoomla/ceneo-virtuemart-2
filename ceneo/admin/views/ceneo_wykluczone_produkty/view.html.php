<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class Porownywarki_VM2ViewCeneo_wykluczone_produkty extends JView
{
    function display($tpl = null)
    {
        // menu
        JToolBarHelper::title(JText::_('Wykluczone produkty z XMLa'), 'generic.png');
        JToolBarHelper::addNewX();
        JToolBarHelper::deleteList();
        JToolBarHelper::back('Wróc do menu Ceneo', 'index.php?option=com_porownywarki_vm2&controller=ceneo');

        // model
        $model = $this->getModel();

        // request
        $search = JRequest::getVar('search', '');
        if (trim($search) != "" && strlen(trim($search)) <= 3) {
            echo JError::raiseNotice(100, 'Wpisz minimum 4 litery początkowe');
        }
        $number_of_excludes = $model->getNumerOfExcludes();
        $limitstart = JRequest::getVar('limitstart', 0);
        $limit = JRequest::getVar('limit', 100);

        // powiazania
        $excluded_cats_list = $model->getExcludedCats($limitstart, $limit, $search);

        // liczba wszystkich powiązań dla frazy - dla paginacji
        $number_of_excludes_in_search = $number_of_excludes;
        if (trim($search) != "" && strlen(trim($search)) > 3) {
            $number_of_excludes_in_search = $model->getExcludedCats($limitstart, $limit, $search, false);
            $number_of_excludes_in_search = count($number_of_excludes_in_search);
        }

        // paginacja
        if ($number_of_excludes == $number_of_excludes_in_search) {
            $paginacja = new JPagination($number_of_excludes, $limitstart, $limit);
        } else {
            $paginacja = new JPagination($number_of_excludes_in_search, $limitstart, $limit);
        }

        if (is_array($excluded_cats_list) && count($excluded_cats_list)) {
            $this->assignRef('excluded_cats_list', $excluded_cats_list);
        } else {
            $array = array();
            $this->assignRef('excluded_cats_list', $array);
        }
        $this->assignRef('search', $search);
        $this->assignRef('pagination', $paginacja->getListFooter());


        parent::display($tpl);
    }
}