<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class Porownywarki_VM2ModelCeneo_konfiguracja extends JModel
{
    public static function getConfig()
    {
        $db = JFactory::getDBO();
        $q = "SELECT config FROM #__porownywarki_ceneo_config WHERE id=1 ";
        $db->setQuery($q);
        $result = $db->loadResult();
        if (empty($result) && count($result) != 0) {
            return false;
        }
        return unserialize($result);
    }

    public static function setConfig($config_array, $ignore_array_keys = false)
    {
        $db = JFactory::getDBO();

        if ($ignore_array_keys == false) {
            if (!isset($config_array['excluded_cats'])) {
                //$config_array['excluded_cats'] = array();
            }

            if (!isset($config_array['excluded_prods'])) {
                //$config_array['excluded_prods'] = array();
            }

            $config_string = self::getConfig();

            foreach ($config_array as $k => $config) {

                switch ($k) {
                    case 'excluded_cats':
                    case 'excluded_prods':
                        $config = implode(",", $config);

                        break;
                    case 'file_name':
                        $config = trim(str_replace(array(".xml", "."), "", $config));
                        break;
                }

                $config_string->$k = $config;
            }
        } // przekazywany parametr jest już klasu stdClass
        else {
            $config_string = $config_array;
        }

        $q = "UPDATE #__porownywarki_ceneo_config SET config = '" . serialize($config_string) . "' WHERE id=1 ";

        $db->setQuery($q);
        $result = $db->query();

        if ($result == false) {
            return false;
        } else {
            return true;
        }
    }

    public static function getCats($ids = "", $exclude = false)
    {
        $db = JFactory::getDBO();
        $q =
            "
        SELECT
            cats.virtuemart_category_id,
            cats_pl.category_name
        FROM
            #__virtuemart_categories as cats
            JOIN #__virtuemart_categories_pl_pl as cats_pl ON cats.virtuemart_category_id=cats_pl.virtuemart_category_id
        ";


        if (trim($ids) != "") {
            $q .= " WHERE cats.virtuemart_category_id " . ($exclude ? 'NOT' : '') . " IN (" . $ids . ") ";
        }

        $q .= "
        ORDER BY
            cats_pl.category_name
        ";

        $db->setQuery($q);
        $result = $db->loadObjectList();
        if ($result == null) {
            return false;
        } else {
            return $result;
        }

    }

    public static function getProds($ids = "", $exclude = false)
    {
        $db = JFactory::getDBO();
        $q =
            "
        SELECT
            virtuemart_product_id,
            product_name
        FROM
            #__virtuemart_products as prods
            JOIN #__virtuemart_products_pl_pl using(virtuemart_product_id)
        ";

        if (trim($ids) != "") {
            $q .= " WHERE prods.virtuemart_product_id " . ($exclude ? 'NOT' : '') . " IN (" . $ids . ") ";
        }

        $q .= "
        ORDER BY
            product_name
        ";

        $db->setQuery($q);
        $result = $db->loadObjectList();
        if ($result == null) {
            return false;
        } else {
            return $result;
        }
    }
}