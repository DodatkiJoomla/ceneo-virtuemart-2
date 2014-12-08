<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class Porownywarki_VM2ModelCeneo_wykluczone_kategorie extends JModel
{
    // nazwa tabeli
    protected $table = "#__virtuemart_categories_pl_pl";

    // nazwa właściwości
    protected $attr = "excluded_cats";

    // kolumna id nazwa
    protected $col_id_name = "virtuemart_category_id";

    // kolumna id nazwa
    protected $col_name_name = "category_name";

    // nazwa metody pobierającej niewykluczone rekordy
    protected $method_name = "getCats";


    public function getExcludedCats($start = 0, $limit = 100, $search = "", $search_get_all_cats = true)
    {
        if (!($config = Porownywarki_VM2ModelCeneo_konfiguracja::getConfig())) {
            return false;
        }

        $attr = $this->attr;
        $col_id_name = $this->col_id_name;
        $col_name_name = $this->col_name_name;

        $db = JFactory::getDBO();

        $excluded_cats_array = array();

        if (isset($config->$attr) && $config->$attr != "") {
            $excluded_cats_array = explode(",", trim($config->$attr,","));
        }

        // Jeśli wprowadzono wyszukiwanie - sprawdzam czy taka kategoria istnieje
        if (trim($search) != "") {
            $q = "
				SELECT
					" . $col_id_name . ",
					" . $col_name_name . "
				FROM
					" . $this->table . "
				WHERE
					" . $col_name_name . " LIKE '%" . $search . "%' ";

            $db->setQuery($q);
            $result = $db->loadObjectList();

            if ($result == false) {
                return false;
            } else {
                if (is_array($result) && count($result) == 0) {
                    return array();
                }
            }

            $sliced_excluded_cats_array = array();

            // pętla po znalezionych rekordach
            foreach ($result as $row) {
                if (in_array($row->$col_id_name, $excluded_cats_array)) {
                    $sliced_excluded_cats_array[] = $row->$col_id_name;
                }
            }

            // slice do $limit - tylko jeśli ustawiony jest parametr $search_get_all_cats
            if ($search_get_all_cats) {
                $sliced_excluded_cats_array = array_slice($sliced_excluded_cats_array, $start, $limit);
            }
        } else {
            // slice do $limit
            $sliced_excluded_cats_array = array_slice($excluded_cats_array, $start, $limit);

            if ((is_array($sliced_excluded_cats_array) && count($sliced_excluded_cats_array) == 0) || !is_array($sliced_excluded_cats_array)) {
                return array();
            }
        }

        // Pobieram polskie nazwy
        $q = "
			SELECT
				" . $col_id_name . ",
				" . $col_name_name . "
			FROM
				" . $this->table . "
			WHERE
				" . $col_id_name . " IN ( " . trim(implode(",", $sliced_excluded_cats_array),",") . " ) ";
        $db->setQuery($q);
        $result = $db->loadObjectList();
        if ($result == false) {
            return false;
        }

        return $result;
    }

    public function deleteExcludedCatsAndReturnConfig(array $ids)
    {
        if (!count($ids)) {
            return true;
        }

        if (!($config = Porownywarki_VM2ModelCeneo_konfiguracja::getConfig())) {
            return false;
        }

        $attr = $this->attr;

        if (!isset($config->$attr) || trim($config->$attr) == "") {
            return true;
        }

        $excluded_cats_result_array = array();
        $excluded_cats_result_string = "";

        $excluded_cats_array = explode(",", trim($config->$attr,","));

        foreach ($excluded_cats_array as $excluded_cat) {
            if (!in_array($excluded_cat, $ids)) {
                $excluded_cats_result_array[] = $excluded_cat;
            }
        }

        if (is_array($excluded_cats_result_array) && count($excluded_cats_result_array)) {
            $excluded_cats_result_string = trim(implode(",", $excluded_cats_result_array),",");
        }

        $config->$attr = $excluded_cats_result_string;

        return $config;
    }

    public function getNumerOfExcludes()
    {
        if (!($config = Porownywarki_VM2ModelCeneo_konfiguracja::getConfig())) {
            return 0;
        }

        $attr = $this->attr;

        $number = substr_count($config->$attr, ",");

        if ($number > 0 && is_numeric($number)) {
            return $number;
        }

        return 0;
    }


    public function addExcludedItemAndReturnConfig($cat_ids)
    {
        if (!is_array($cat_ids) || (is_array($cat_ids) && !count($cat_ids))) {
            return false;
        }

        if (!($config = Porownywarki_VM2ModelCeneo_konfiguracja::getConfig())) {
            return false;
        }

        $attr = $this->attr;

        $excluded_cats_array = explode(",", trim($config->$attr,","));

        // dodaję nowy wpis
        foreach ($cat_ids as $cat_id) {
            if ($cat_id > 0) {
                $excluded_cats_array[] = $cat_id;
            }
        }

        $config->$attr = trim(implode(",", $excluded_cats_array),",");

        return $config;
    }

    public function getUnexcludedItems()
    {
        if (!($config = Porownywarki_VM2ModelCeneo_konfiguracja::getConfig())) {
            return false;
        }

        $attr = $this->attr;
        $method_name = $this->method_name;

        if (!isset($config->$attr)) {
            return true;
        }

        $cats = Porownywarki_VM2ModelCeneo_konfiguracja::$method_name($config->$attr, true);
        return $cats;
    }

}
