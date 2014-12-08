<?php
defined('_JEXEC') or die('Restricted access');

// config
$db = JFactory::getDBO();
$db->setQuery("SELECT config FROM #__porownywarki_ceneo_config WHERE id=1 LIMIT 1");
$cf = unserialize($db->loadResult());


// waliduj po uniqid
if (!isset($_GET['u']) || $_GET['u'] != $cf->uniqid) {
    exit();
}

// config
if (!class_exists('VmConfig')) {
    require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'config.php');
}
VmConfig::loadConfig();

// classes
if (!class_exists('calculationHelper')) {
    require_once(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
}


if (!class_exists('VirtueMartModelProduct')) {
    require_once(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'product.php');
}

// stałe
define('LIMITED', $cf->limited);
define('UNIQID', $cf->uniqid);
define('FILE_NAME', "ceneo_" . str_replace(".xml", "", $cf->file_name) . ".xml");
define('DESC_TYPE', $cf->desc_type);
define('DOSTEPNOSC', $cf->dostepnosc);
define('ALSO_WITHOUT_MFS', $cf->also_without_mfs ? ' LEFT ' : '');
define('ALSO_WITHOUT_LINKED_CATS', $cf->also_without_linked_cats ? ' LEFT ' : '');
define('DUPLICATES_MODE', $cf->duplicates_mode);
define('BUY_CENEO_BASKET', $cf->buy_ceneo_basket);

// DJ 2013-07-16 Ustawiam domyślnie na 0, żeby przy aktualizacji zaciągało te dane.
$rabaty_kategorie = (!isset($cf->rabaty_kategorie) ? '0' : $cf->rabaty_kategorie);
// Jeśli wybrano generowanie dla różnych kategorii - ustawioam domyślnie opcję 1, czyli generuję rabaty tylko dla określonej kategorii związanej w XMLu.
if ($cf->also_without_linked_cats) {
    $rabaty_kategorie = '1';
}
define('RABATY_KATEGORIE', $rabaty_kategorie);
//define('RABATY_KATEGORIE_DODATKOWA', $cf->rabaty_kategorie_dodatkowa);

// 2013-05-21 ograniczenie dodawanych produktów z danej kategorii
$db = JFactory::getDBO();
$q = "
SELECT
	PVCVCX.virtuemart_cat_id, PVCVCX.ceneo_zakres_produktow
FROM
	#__porownywarki_vm2_ceneo_vm_cats_xref AS PVCVCX
WHERE
	PVCVCX.ceneo_zakres_produktow <> ''
";
$db->setQuery($q);
$category_products = $db->loadObjectList();


// Właściwe nowe WHERE na ograniczenia - dopiero tutaj, ponieważ nie chcę zmieniać SQLi w aktualnie działających sklepach.
$where_query_products_in_category = "";

if (!empty($category_products)) {
    $exploded_excluded_cats = (trim($cf->excluded_cats) != "" ? explode(",", $cf->excluded_cats) : array());
    $exploded_excluded_prods = (trim($cf->excluded_prods) != "" ? explode(",", $cf->excluded_prods) : array());

    $where_query_products_in_category_temp = array();

    foreach ($category_products as $kategoria) {
        if (!in_array($kategoria->virtuemart_cat_id, $exploded_excluded_cats)) {
            $exploded_excluded_cats[] = $kategoria->virtuemart_cat_id;
        }
    }


    foreach ($category_products as $kategoria) {
        $ograniczone_id_prodoktow = explode(",", $kategoria->ceneo_zakres_produktow);

        if (!empty($ograniczone_id_prodoktow) && !in_array($kategoria->virtuemart_cat_id, $exploded_excluded_cats)) {
            foreach ($ograniczone_id_prodoktow as $produkt_id) {
                if (!in_array($produkt_id, $exploded_excluded_prods)) {
                    $where_query_products_in_category_temp[] = " ( cats.virtuemart_category_id = " . $kategoria->virtuemart_cat_id . " AND prods.virtuemart_product_id = " . $produkt_id . "  ) ";
                }
            }
        }
    }

    $cf->excluded_cats = (!empty($exploded_excluded_cats) ? implode(",", $exploded_excluded_cats) : "");

    if (!empty($where_query_products_in_category_temp)) {
        $where_query_products_in_category = " " . implode(" OR ", $where_query_products_in_category_temp) . " ";
    }

}

$where_query = array();
if ($cf->only_published) {
    $where_query[] = ' prods.published = 1 ';
}
if (!empty($cf->excluded_cats)) {
    if ($where_query_products_in_category != "") {
        $where_query[] = ' ( cats.virtuemart_category_id NOT IN (' . $cf->excluded_cats . ') OR  ' . $where_query_products_in_category . ') ';
    } else {
        $where_query[] = ' cats.virtuemart_category_id NOT IN (' . $cf->excluded_cats . ') ';
    }
}
if (!empty($cf->excluded_prods)) {
    $where_query[] = ' prods.virtuemart_product_id NOT IN (' . $cf->excluded_prods . ') ';
}

$where = "";
if (count($where_query) > 0) {
    $where = " WHERE " . implode(" AND ", $where_query);
}

// main ceneo query
$main_query = "SELECT prods.virtuemart_product_id, prods.product_sku, prods.product_weight, prods.product_availability, prods.product_in_stock, trans.product_name, trans.product_s_desc, trans.product_desc, cats.virtuemart_category_id, trans2.category_name,  mfs.virtuemart_manufacturer_id, trans3.mf_name, cats_xref.ceneo_category_type, cats_xref.ceneo_category_type_name, cats_xref.custom_fields, ceneo_cats.link FROM #__virtuemart_products as prods
JOIN #__virtuemart_products_pl_pl as trans ON prods.virtuemart_product_id=trans.virtuemart_product_id
JOIN #__virtuemart_product_categories as cats ON prods.virtuemart_product_id=cats.virtuemart_product_id
JOIN #__virtuemart_categories_pl_pl as trans2 ON cats.virtuemart_category_id=trans2.virtuemart_category_id
" . ALSO_WITHOUT_MFS . " JOIN #__virtuemart_product_manufacturers as mfs ON prods.virtuemart_product_id=mfs.virtuemart_product_id
" . ALSO_WITHOUT_MFS . " JOIN #__virtuemart_manufacturers_pl_pl as trans3 ON mfs.virtuemart_manufacturer_id=trans3.virtuemart_manufacturer_id
" . ALSO_WITHOUT_LINKED_CATS . " JOIN #__porownywarki_vm2_ceneo_vm_cats_xref as cats_xref ON cats.virtuemart_category_id=cats_xref.virtuemart_cat_id
" . ALSO_WITHOUT_LINKED_CATS . " JOIN #__porownywarki_vm2_ceneo_categories as ceneo_cats ON ceneo_cats.ceneo_cat_id=cats_xref.ceneo_cat_id
" . $where . " ORDER BY cats_xref.ceneo_category_type";

// number of products
$pattern1 = "/SELECT .+ FROM/";
$pattern2 = "/ORDER BY .+/";
$count_query = preg_replace(array($pattern1, $pattern2), array('SELECT count(*) FROM ', ''), $main_query);
$db->setQuery($count_query);
$table_size = $db->loadResult();
define('TABLE_SIZE', $table_size);

// db
function getProducts($main_query, $limits = "")
{
    $db = JFactory::getDBO();
    $q = $main_query . ' ' . $limits;
    $db->setQuery($q);
    $products = $db->loadObjectList();
    return $products;
}