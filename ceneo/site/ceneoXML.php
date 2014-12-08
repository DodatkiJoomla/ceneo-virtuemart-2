<?php
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT . DS . 'ceneoGetProducts.php');

function createXML($products, $start, $end = false, $old_group = "")
{

    $url = JURI::getInstance(JURI::root());
    $host = $url->getHost($url);
    $calculator = calculationHelper::getInstance();

    // DJ 2013-07-16 Na razie nie używam RABATY_KATEGORIE_DODATKOWA, ponieważ nieopublikowana kategoria VM2, która ma przypisany rabat i która jest
    // przypisana także do produktu X, wpływa na cenę tego produktu (mimo że kat. jest nieopublikowana [nielogiczne?]).
    $products_categories_xref = array();

    if (RABATY_KATEGORIE == 0) {
        //$dodatkowa_kategoria_rabatowa = RABATY_KATEGORIE_DODATKOWA;
        $dodatkowa_kategoria_rabatowa = 0;
        $products_categories_xref = getProductsCats($dodatkowa_kategoria_rabatowa);
    }

    // tworzenie pliku z ofertami
    if ($start == true) {
        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\r\n";
        $xml .= '<offers xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="1">' . "\r\n";
        $handle = fopen(JPATH_SITE . DS . FILE_NAME, "a");
        fwrite($handle, $xml);
        unset($xml);
        fclose($handle);
    }


    $k = 0;
    if ($products != null) {
        if (DUPLICATES_MODE != 1) {
            // delete duplicates
            $duplicated_product_id = null;
            $products_for_delete = array();
            $products_for_restore = array();
            $items = array();
            $products_groups_to_readd = array();

            // copy products ids to new table
            foreach ($products as $m => $prod) {
                $items[$m] = $prod->virtuemart_product_id;
            }

            // if number of values in items table is higher than 1, then add key to new duplicated_products_ids array
            $duplicated_products_ids = array();
            foreach (array_count_values($items) as $key => $item) {
                if ($item > 1) {
                    $duplicated_products_ids[] = $key;
                }
            }

            // store duplicated prodyucts in another new array ($products_groups_to_readd)
            foreach ($products as $key => $product) {
                if (in_array($product->virtuemart_product_id, $duplicated_products_ids)) {
                    $category_info = getCatLevel($product->virtuemart_category_id);
                    $product->highest_parent_id = $category_info->highest_parent_id;
                    $product->level = $category_info->level;
                    $product->key = $key;
                    $products_for_delete[] = $key;
                    $products_groups_to_readd[$product->virtuemart_product_id][$category_info->highest_parent_id][$key] = $product;
                }
            }


            foreach ($products_groups_to_readd as $key => $products_to_readd) {
                $max_lower_level_product = null;
                foreach ($products_to_readd as $key2 => $products_parent_categories) {
                    $levels = array();
                    $lower_level_product = null;
                    foreach ($products_parent_categories as $products_table_key => $vm_product) {
                        if ($lower_level_product == null) {
                            $lower_level_product = $vm_product;
                        } else {
                            if ($vm_product->level > $lower_level_product->level) {
                                $lower_level_product = $vm_product;
                            }
                        }
                    }

                    if (DUPLICATES_MODE == 2 || DUPLICATES_MODE == 3) {
                        if (DUPLICATES_MODE == 2) {
                            $products_for_restore[] = $lower_level_product->key;
                        } else {
                            if (DUPLICATES_MODE == 3 && $lower_level_product->level != 1) {
                                $products_for_restore[] = $lower_level_product->key;
                            }
                        }
                    } else {
                        if (DUPLICATES_MODE == 0) {
                            if ($max_lower_level_product == null) {
                                $max_lower_level_product = $lower_level_product;
                            } else {
                                if ($lower_level_product->level > $max_lower_level_product->level) {
                                    $max_lower_level_product = $lower_level_product;
                                }
                            }
                        }
                    }
                }

                if (DUPLICATES_MODE == 0) {
                    $products_for_restore[] = $max_lower_level_product->key;
                }
            }

            foreach ($products_for_delete as $product_for_delete) {
                if (!in_array($product_for_delete, $products_for_restore)) {
                    unset($products[$product_for_delete]);
                }
            }
        }

        $prods_size = count($products) - 1;

        foreach ($products as $p) {

            $xml = "";
            if (!empty($p)) {

                if ($p->ceneo_category_type_name == "" || $p->ceneo_category_type_name == null) {
                    $p->ceneo_category_type_name = "other";
                }
                if ($p->ceneo_category_type == "" || $p->ceneo_category_type == null) {
                    $p->ceneo_category_type = "0";
                }

                if ($k == 0 && $start == true) {
                    $old_group = $p->ceneo_category_type;
                }

                if (($k == 0 && $start == true) || $p->ceneo_category_type != $old_group) {
                    if ($p->ceneo_category_type != $old_group) {
                        $xml .= '</group>' . "\r\n";
                    }
                    $xml .= '<group name="' . $p->ceneo_category_type_name . '">' . "\r\n";
                    $old_group = $p->ceneo_category_type;
                }

                if ($k == 1000) {
                    flush();
                }

                $p = getProductPriceRow($p);

                // DJ 2013-07-10 Ustawiam kategorię produktu - żeby obliczenia rabatów dobrze się wyliczały.
                // DJ 2013-07-16 Dodaję opcję z konfiga, albo wszystkie kategorie produktu, albo tylko 1 - ta przypisana we wpisie XML.
                if (RABATY_KATEGORIE == 0) {
                    $p->categories = array();
                    if (!empty($products_categories_xref) && isset($products_categories_xref[$p->virtuemart_category_id])) {
                        $p->categories = $products_categories_xref[$p->virtuemart_category_id];
                    }
                } else {
                    $p->categories = array($p->virtuemart_category_id);
                }
                $prices = $calculator->getProductPrices($p, 0, 0, 0, true, false);

                $medias = linkedMedia($p->virtuemart_product_id);
                $attrs = "";
                if (trim($p->custom_fields) != "") {
                    $attr = unserialize($p->custom_fields);
                    foreach ($attr as $a) {
                        if ($a->custom_field_id == -1 && $p->mf_name != null) {
                            $attrs .= '<a name="' . $a->attribute_name . '"><![CDATA[' . $p->mf_name . ']]></a>' . "\r\n";
                        } else {
                            if ($a->custom_field_id == -2 && !empty($p->product_sku)) {
                                $attrs .= '<a name="' . $a->attribute_name . '"><![CDATA[' . $p->product_sku . ']]></a>' . "\r\n";
                            } else {
                                if ($a->custom_field_id == -1 && $p->mf_name == null) {
                                } else {
                                    $field_value = getCustomFieldValue($p->virtuemart_product_id, $a->custom_field_id);
                                    if (trim($field_value) != "") {
                                        $attrs .= '<a name="' . $a->attribute_name . '"><![CDATA[' . $field_value . ']]></a>' . "\r\n";
                                    }
                                    unset($field_value);
                                }
                            }
                        }
                    }
                    unset($attr);
                }

                $weight = number_format($p->product_weight, 2, ".", "");

                $xml .= '<o';
                $xml .= ' id="' . $p->virtuemart_product_id . '" ';
                $xml .= ' url="http://' . $host . JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $p->virtuemart_product_id . '&virtuemart_category_id=' . $p->virtuemart_category_id) . '" ';
                $xml .= ' price="' . number_format($prices['salesPrice'], 2, ".", "") . '" ';
                unset($prices);
                if ($weight > 0) {
                    $xml .= ' weight="' . $weight . '" ';
                }

                // kup na ceneo
                $xml .= ' basket="' . BUY_CENEO_BASKET . '" ';

                // dostępność
                switch (DOSTEPNOSC) {
                    case 0:
                        if ($p->product_in_stock > 0) {
                            $xml .= ' avail="1" ';
                            // na stanie
                            $xml .= ' stock="' . $p->product_in_stock . '" ';
                        } else {
                            $ceneo_avail = getCeneoAvail($p->product_availability);
                            if ($ceneo_avail == "") {
                                $xml .= ' avail="99" ';
                            } else {
                                $xml .= ' avail="' . $ceneo_avail . '" ';
                            }
                        }
                        break;

                    case 1:
                        $xml .= ' avail="1" ';
                        break;

                    case 2:
                        $xml .= ' avail="99" ';
                        break;
                }


                $xml .= '>' . "\r\n";

                $xml .= '<name><![CDATA[' . $p->product_name . ']]></name>' . "\r\n";

                $xml .= '<cat><![CDATA[';
                if (!empty($p->link)) {
                    $xml .= $p->link;
                } else {
                    $xml .= $p->category_name;
                }
                $xml .= ']]></cat>' . "\r\n";

                if (!empty($medias)) {
                    $xml .= '<imgs>';
                    if (!empty($medias->file_url)) {
                        $xml .= '<main url="' . JURI::root() . $medias->file_url . '" />' . "\r\n";
                    }
                    if (!empty($medias->file_url_thumb)) {
                        $xml .= '<i url="' . JURI::root() . $medias->file_url_thumb . '" />' . "\r\n";
                    }
                    $xml .= '</imgs>' . "\r\n";
                }
                unset($medias);

                $desc_type = DESC_TYPE;
                if (!empty($p->$desc_type)) {
                    $xml .= '<desc><![CDATA[' . strip_tags($p->$desc_type) . ']]></desc>' . "\r\n";
                }

                // atrybuty
                if (trim($attrs) != "") {
                    $xml .= '<attrs>' . "\r\n";
                    $xml .= $attrs;
                    $xml .= '</attrs>' . "\r\n";
                }

                $xml .= '</o>';

                if ($k == $prods_size && $end == true) {
                    $xml .= '</group>' . "\r\n";
                }


                $handle = fopen(JPATH_SITE . DS . FILE_NAME, "a");
                fwrite($handle, $xml);
                unset($xml);
                fclose($handle);
            }

            $k++;
        }
    }
    unset($products);


    if ($end) {
        $xml = '</offers>' . "\r\n";

        $handle = fopen(JPATH_SITE . DS . FILE_NAME, "a");
        fwrite($handle, $xml);
        unset($xml);
        fclose($handle);
    }

    return $old_group;
}


function linkedCat($cat_id)
{
    $db = JFactory::getDBO();
    $q = "SELECT ceneo_cats.link FROM #__porownywarki_vm2_ceneo_vm_cats_xref as xref JOIN #__porownywarki_vm2_ceneo_categories as ceneo_cats USING(ceneo_cat_id) WHERE xref.virtuemart_cat_id=" . $cat_id;
    $db->setQuery($q);
    $cat = $db->loadResult();
    return $cat;
}

function linkedMedia($product_id)
{
    $db = JFactory::getDBO();
    $q = "SELECT amedia.file_url, amedia.file_url_thumb FROM #__virtuemart_product_medias as pmedia JOIN #__virtuemart_medias as amedia ON amedia.virtuemart_media_id=pmedia.virtuemart_media_id WHERE pmedia.virtuemart_product_id=" . $product_id . " ORDER BY pmedia.ordering";
    $db->setQuery($q);
    $media = $db->loadObject();
    return $media;
}

function getCustomFieldValue($product_id, $field_id)
{
    $db = JFactory::getDBO();
    $q = "SELECT custom_value FROM #__virtuemart_product_customfields WHERE virtuemart_product_id=" . $product_id . " AND virtuemart_custom_id=" . $field_id . " LIMIT 1";
    $db->setQuery($q);
    $cFields = $db->loadResult();
    if ($cFields == false) {
        return "";
    } else {
        return $cFields;
    }
}

function getCeneoAvail($product_availability)
{
    $db = JFactory::getDBO();
    $q = "SELECT ceneo_avail FROM #__porownywarki_ceneo_avail_xref WHERE product_availability='" . $product_availability . "' ORDER BY id DESC LIMIT 1";
    $db->setQuery($q);
    $cFields = $db->loadResult();
    if ($cFields == false) {
        return "";
    } else {
        return $cFields;
    }
}

function getCatLevel($cat_id)
{
    $category = new stdClass();

    $level = 0;
    $highest_parent_id = 0;

    do {
        $level++;
        $highest_parent_id = $cat_id;
    } while (($cat_id = getCategoryParent($cat_id)) != 0);

    $category->level = $level;
    $category->highest_parent_id = $highest_parent_id;
    return $category;
}

function getCatChilds($parent_cat_id)
{
    $childs = array();
    while (($parent_cat_id = getCatChild($parent_cat_id)) != false) {

        if (is_array($parent_cat_id)) {
            foreach ($parent_cat_id as $parent) {
                $childs[] = $parent;
            }
        } else {
            $childs[] = $parent_cat_id;
        }

    }
    return $childs;
}

function getCatChild($parent_cat_id)
{
    if (is_array($parent_cat_id)) {
        $parent_cat_id = implode(', ', $parent_cat_id);
    }

    $db = JFactory::getDBO();
    $q = "SELECT category_child_id FROM #__virtuemart_category_categories WHERE category_parent_id IN (" . $parent_cat_id . ") ";
    $db->setQuery($q);
    $parent_cat_id = $db->loadResultArray();
    if (empty($parent_cat_id)) {
        return false;
    } else {
        return $parent_cat_id;
    }
}

function getCategoryParent($child_cat_id)
{
    $db = JFactory::getDBO();
    $q = "SELECT category_parent_id FROM #__virtuemart_category_categories WHERE category_child_id = " . $child_cat_id . " ";
    $db->setQuery($q);
    $parent_cat_id = $db->loadResult();
    return $parent_cat_id;
}

function getProductPriceRow($ceneo_product_object)
{
    $db = JFactory::getDBO();
    $q = "SELECT product_price, product_currency, override, product_override_price, product_tax_id, product_discount_id FROM #__virtuemart_product_prices WHERE virtuemart_product_id = " . $ceneo_product_object->virtuemart_product_id . " ORDER BY virtuemart_product_price_id DESC LIMIT 1 ";
    $db->setQuery($q);
    $product_price_row = $db->loadObject();
    if (!empty($product_price_row)) {
        foreach ($product_price_row as $key => $field) {
            $ceneo_product_object->$key = $field;
        }
    }
    return $ceneo_product_object;
}

// DJ 2013-07-16 Dodaje funkcję, ale bez sprawdzania published.
function getProductsCats($dodatkowa_kategoria_rabatowa = 0)
{
    $db = JFactory::getDBO();
    $q = "
	SELECT
		VPC.virtuemart_product_id,
		VPC.virtuemart_category_id
	FROM
		#__virtuemart_product_categories AS VPC
		JOIN #__virtuemart_categories AS VC ON VPC.virtuemart_category_id = VC.virtuemart_category_id #AND (VC.published = 1
	";

    // Dodaję LUB na nieopublikowaną kategorię.
    if (!empty($dodatkowa_kategoria_rabatowa) && is_numeric($dodatkowa_kategoria_rabatowa)) {
        $q .= " OR VPC.virtuemart_category_id = " . $dodatkowa_kategoria_rabatowa;
    }

    // published do włączenia w przyszłości (?)

    $db->setQuery($q);
    $products_cats_xref = $db->loadObjectList();

    $tablica_wynikowa_produkty = array();

    if (!empty($products_cats_xref)) {
        foreach ($products_cats_xref as $key => $field) {
            // Robię tablicę z id produktów, gdzie dopisują kategorie.
            $tablica_wynikowa_produkty[$field->virtuemart_product_id][] = $field->virtuemart_category_id;
        }
    }
    return $tablica_wynikowa_produkty;
}