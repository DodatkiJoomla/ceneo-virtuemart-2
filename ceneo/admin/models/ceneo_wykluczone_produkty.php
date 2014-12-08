<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

// pobieram moduł
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/models/ceneo_wykluczone_kategorie.php');

class Porownywarki_VM2ModelCeneo_wykluczone_produkty extends Porownywarki_VM2ModelCeneo_wykluczone_kategorie
{
    // nazwa tabeli
    protected $table = "#__virtuemart_products_pl_pl";

    // nazwa właściwości
    protected $attr = "excluded_prods";

    // kolumna id nazwa
    protected $col_id_name = "virtuemart_product_id";

    // kolumna id nazwa
    protected $col_name_name = "product_name";

    // nazwa metody pobierającej niewykluczone rekordy
    protected $method_name = "getProds";

}