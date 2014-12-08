<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');


class Porownywarki_VM2ModelCeneo_dostepnosc extends JModel
{
    private $table = "#__porownywarki_vm2_dostepnosci_xref";


    public function getAvailXrefs($search = "")
    {
        $db = JFactory::getDBO();
        $q = "SELECT * FROM #__porownywarki_ceneo_avail_xref";
        $db->setQuery($q);
        $results = $db->loadObjectList();
        if (empty($results) && count($results) != 0) {
            return false;
        } else {
            $ceneoAvails = array_flip($this->getCeneoAvails());
            foreach ($results as &$result) {
                $result->ceneo_avail = $ceneoAvails[$result->ceneo_avail];
            }
            return $results;
        }
    }

    public function getCeneoAvails()
    {
        $ceneoAvails = array();
        $ceneoAvails['dostępny, sklep posiada produkt'] = 1;
        $ceneoAvails['sklep będzie posiadał produkt do 3 dni'] = 3;
        $ceneoAvails['sklep będzie posiadał produkt do 7 dni'] = 7;
        $ceneoAvails['sklep będzie posiadał produkt do 14 dni'] = 14;
        $ceneoAvails['informacja na stronie sklepu (podstrona produktu)'] = 99;
        return $ceneoAvails;
    }
}

