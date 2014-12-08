<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');


class Porownywarki_VM2ModelCeneo_dostepnosc_add extends JModel
{
    private $table = "#__porownywarki_ceneo_avail_xref";


    public function setXref($vm_avail, $ceneo_avail)
    {

        $db = JFactory::getDBO();
        $q = "INSERT INTO " . $this->table . " (product_availability, ceneo_avail) VALUES('" . $vm_avail . "'," . $ceneo_avail . ")  ";
        $db->setQuery($q);
        $result = $db->query();
        if (empty($result)) {
            return false;
        }
        return $result;
    }

    public function deleteXref($cid)
    {
        $db = JFactory::getDBO();
        $q = "DELETE FROM " . $this->table . " WHERE id IN (" . implode(", ", $cid) . ")";
        $db->setQuery($q);
        $result = $db->query();
        if (empty($result)) {
            return false;
        }
        return $result;
    }

    public function getVmAvails($availability_id = "")
    {
        $db = JFactory::getDBO();

        $selected_id_sql = "";

        $q = "SELECT product_availability FROM `#__virtuemart_products` WHERE NOT product_availability IS NULL AND NOT product_availability IN (SELECT product_availability FROM " . $this->table . ") " . $selected_id_sql . " GROUP BY product_availability ";
        $db->setQuery($q);
        $result = $db->loadObjectList();
        if (empty($result) && count($result) != 0) {
            return false;
        }
        return $result;
    }

    public function getSelectedVmAvails($id)
    {
        $db = JFactory::getDBO();
        $q = "SELECT product_availability FROM " . $this->table . " WHERE id IN (" . implode(",", $id) . ") ";
        echo $q;
        $db->setQuery($q);
        $result = $db->loadObjectList();
        if (empty($result)) {
            return array();
        }
        return $result;
    }

    public function getSelectedCeneoAvails($id)
    {
        $db = JFactory::getDBO();
        $q = "SELECT ceneo_avail FROM " . $this->table . " WHERE id IN (" . implode(",", $id) . ") ";
        echo $q;
        $db->setQuery($q);
        $result = $db->loadResult();
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


