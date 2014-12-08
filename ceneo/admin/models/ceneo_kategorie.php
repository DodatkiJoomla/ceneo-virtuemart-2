<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');


class Porownywarki_VM2ModelCeneo_kategorie extends JModel
{
    private $categories = array();
    private $table = "#__porownywarki_vm2_ceneo_categories";
    private $table_xref = "#__porownywarki_vm2_ceneo_vm_cats_xref";


    public function getXrefs($search = "")
    {
        $lang = &JFactory::getLanguage();
        $lang = $lang->getLocale();
        $lang = substr(strtolower($lang[2]), 0, 5);

        $where = "";
        if (!empty($search)) {
            $search = htmlspecialchars(trim($search));
            $where = " WHERE lang.category_name LIKE '%" . $search . "%' OR ceneo.name LIKE '%" . $search . "%' ";
        }

        $db = JFactory::getDBO();
        $q = "SELECT xref.*, lang.category_name, ceneo.name, ceneo.link FROM " . $this->table_xref . " as xref JOIN #__virtuemart_categories as vm ON xref.virtuemart_cat_id=vm.virtuemart_category_id JOIN #__virtuemart_categories_" . $lang . " as lang using(virtuemart_category_id) JOIN " . $this->table . " as ceneo using(ceneo_cat_id) " . $where . " ORDER BY id DESC ";
        $db->setQuery($q);
        $result = $db->loadObjectList();
        if (empty($result) && count($result) != 0) {
            return false;
        }
        return $result;
    }


    public function getXref($id)
    {
        $db = JFactory::getDBO();
        $q = "SELECT * FROM " . $this->table_xref . " WHERE id = " . $id . "; ";
        $db->setQuery($q);
        $result = $db->loadObject();
        if (empty($result)) {
            return false;
        }
        return $result;
    }

    // 2013-05-21 Dodanie nowej kolumny - ceneo_zakres_produktow
    public function setXref($vm_cat, $ceneo_cat, $ceneo_category_type, $custom_fields, $ceneo_zakres_produktow)
    {
        if (!empty($ceneo_category_type)) {
            $ceneo_category_type_name = $this->getCeneoCategoryTypes($ceneo_category_type);
        }

        if (empty($ceneo_category_type_name)) {
            $ceneo_category_type_name = array();
            $ceneo_category_type_name[0]->parent_name = "other";
        }

        $db = JFactory::getDBO();
        $q = "INSERT INTO " . $this->table_xref . "(ceneo_cat_id, virtuemart_cat_id, ceneo_category_type, ceneo_category_type_name, custom_fields, ceneo_zakres_produktow) VALUES(" . $ceneo_cat . "," . $vm_cat . "," . $ceneo_category_type . ",'" . $ceneo_category_type_name[0]->parent_name . "','" . $custom_fields . "', '" . $ceneo_zakres_produktow . "') ";
        $db->setQuery($q);
        $result = $db->query();
        if (empty($result)) {
            return false;
        }
        return $result;
    }

    public function updateXref(
        $vm_cat,
        $ceneo_cat,
        $ceneo_category_type,
        $custom_fields,
        $ceneo_zakres_produktow,
        $xref_id
    ) {
        if (!empty($ceneo_category_type)) {
            $ceneo_category_type_name = $this->getCeneoCategoryTypes($ceneo_category_type);
        }

        if (empty($ceneo_category_type_name)) {
            $ceneo_category_type_name = array();
            $ceneo_category_type_name[0]->parent_name = "other";
        }

        $db = JFactory::getDBO();
        $q = "UPDATE " . $this->table_xref . " SET ceneo_cat_id = " . $ceneo_cat . ", virtuemart_cat_id = " . $vm_cat . ", ceneo_category_type = " . $ceneo_category_type . ", ceneo_category_type_name = '" . $ceneo_category_type_name[0]->parent_name . "', custom_fields = '" . $custom_fields . "', ceneo_zakres_produktow = '" . $ceneo_zakres_produktow . "' WHERE id = " . $xref_id . "; ";
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
        $q = "DELETE FROM " . $this->table_xref . " WHERE id IN (" . implode(", ", $cid) . ")";
        $db->setQuery($q);
        $result = $db->query();
        if (empty($result)) {
            return false;
        }
        return $result;
    }

    public function getVmCategories($like = "", $selected_id = 0)
    {
        $lang = &JFactory::getLanguage();
        $lang = $lang->getLocale();
        $lang = substr(strtolower($lang[2]), 0, 5);

        $db = JFactory::getDBO();
        $q = "SELECT virtuemart_category_id as category_id, category_name FROM #__virtuemart_categories JOIN #__virtuemart_categories_" . $lang . " using(virtuemart_category_id) ";
        if (!empty($like)) {
            $q .= " WHERE category_name LIKE '%" . $like . "%' AND virtuemart_category_id NOT IN (SELECT virtuemart_cat_id FROM " . $this->table_xref . ") ";
        } else {
            if (empty($like) && $selected_id) {
                $q .= " WHERE virtuemart_category_id NOT IN (SELECT virtuemart_cat_id FROM " . $this->table_xref . ") OR virtuemart_category_id=" . $selected_id . "; ";
            } else {
                $q .= " WHERE virtuemart_category_id NOT IN (SELECT virtuemart_cat_id FROM " . $this->table_xref . ") ";
            }
        }

        $db->setQuery($q);
        $result = $db->loadObjectList();
        if (empty($result) && count($result) != 0) {
            return false;
        }
        return $result;
    }

    public function getCeneoCategories($like = "")
    {
        $db = JFactory::getDBO();
        $q = "SELECT ceneo_cat_id, name, link FROM " . $this->table . " WHERE last=1 ";
        if (!empty($like)) {
            $q .= " AND link LIKE '%" . $like . "%' AND last=1 ";
        }
        $db->setQuery($q);
        $result = $db->loadObjectList();
        if (empty($result) && count($result) != 0) {
            return false;
        }
        return $result;
    }

    public function getCeneoCategoryTypes($parent_id = "", $group_by_parent = false, $id = 0)
    {
        $db = JFactory::getDBO();
        $q = "SELECT * FROM #__porownywarki_ceneo_cat_types ";
        if (trim($parent_id) != "") {
            $q .= " WHERE parent_id = " . $parent_id . " ";
        } else {
            if ($group_by_parent) {
                $q = " SELECT * FROM #__porownywarki_ceneo_cat_types GROUP BY parent_id ";
            }

            if (!empty($id)) {
                $q .= " WHERE id = " . $id . " ";
            }
        }

        $db->setQuery($q);
        if (!empty($id)) {
            $result = $db->loadObject();
        } else {
            $result = $db->loadObjectList();
        }
        if (empty($result) && count($result) != 0) {
            return false;
        }
        return $result;
    }

    public function getCustomFields($id = 0)
    {
        $db = JFactory::getDBO();
        $q = "SELECT virtuemart_custom_id, custom_title FROM #__virtuemart_customs ";
        if ($id > 0) {
            $q .= " WHERE virtuemart_custom_id = " . $id . " ";
        }

        $db->setQuery($q);
        if (!empty($id)) {
            $result = $db->loadObject();
        } else {
            $result = $db->loadObjectList();
        }

        if (empty($result) && count($result) != 0) {
            return false;
        }
        return $result;
    }

    public function addNewAttribute($parent_id, $name)
    {
        $parent_info = $this->getCeneoCatTypeName($parent_id);
        if (!$parent_info) {
            return false;
        }

        $db = JFactory::getDBO();
        $q = "INSERT INTO #__porownywarki_ceneo_cat_types(parent_id, parent_name, parent_name_pl, name, is_default) VALUES(" . $parent_id . ", '" . $parent_info->parent_name . "', '" . $parent_info->parent_name_pl . "', '" . $name . "', 0  ) ";
        $db->setQuery($q);
        $result = $db->query();

        if (!empty($result)) {
            $q = "SELECT id FROM `#__porownywarki_ceneo_cat_types` ORDER BY id DESC LIMIT 1";
            $db->setQuery($q);
            $count = $db->loadResult();
            return $count;
        } else {
            return 0;
        }
    }

    public function getCeneoCatTypeName($parent_id)
    {
        $db = JFactory::getDBO();
        $q = 'SELECT parent_name, parent_name_pl FROM #__porownywarki_ceneo_cat_types WHERE parent_id = ' . $parent_id . ' LIMIT 1 ';
        $db->setQuery($q);
        $result = $db->loadObject();
        if (empty($result)) {
            return false;
        } else {
            return $result;
        }
    }

    public function deleteAttribute($id)
    {
        $db = JFactory::getDBO();
        $q = 'DELETE FROM #__porownywarki_ceneo_cat_types WHERE id = ' . $id . ' ';
        $db->setQuery($q);
        $result = $db->query();
        if (!empty($result)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Pobieranie plików z kategorii VM
     *
     * @param $category_id
     *
     * @created 2013-05-20
     * @return bool
     */
    public function getVMCategoryProducts($category_id)
    {
        $db = JFactory::getDBO();
        $query = "
		SELECT
			VP.virtuemart_product_id, VPLANG.product_name
		FROM
			#__virtuemart_categories AS VC
			JOIN #__virtuemart_categories_pl_pl AS VCLANG ON VC.virtuemart_category_id = VCLANG.virtuemart_category_id
			JOIN #__virtuemart_product_categories AS VPC ON VC.virtuemart_category_id = VPC.virtuemart_category_id
			JOIN #__virtuemart_products AS VP ON VP.virtuemart_product_id = VPC.virtuemart_product_id
			JOIN #__virtuemart_products_pl_pl AS VPLANG ON VP.virtuemart_product_id = VPLANG.virtuemart_product_id
		WHERE
			VC.virtuemart_category_id = " . $category_id . "
		";
        $db->setQuery($query);
        $result = $db->query();

        if ($result == false) {
            return false;
        }

        $products = $db->loadObjectList();

        return $products;
    }
}
