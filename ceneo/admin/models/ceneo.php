<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');


class Porownywarki_VM2ModelCeneo extends JModel
{
    private $categories = array();
    private $table = "#__porownywarki_vm2_ceneo_categories";
    private $table_xref = "#__porownywarki_vm2_ceneo_vm_cats_xref";

    /*
     * Pobierz categorie z serwera ceneo i zapisz w bazie
     */
    public function updateCeneoCategories($url = "https://api.ceneo.pl/Kategorie/dane.xml")
    {
        if (($xml_data = $this->getCeneoFile($url))) {

            $xml_data = new SimpleXMLElement($xml_data);
            foreach ($xml_data->Category as $category) {
                $this->categories[] = new ceneoCategory($category->Id, $category->Name, $category->Name);
                $this->getSubcategories($category, $this->categories);
            }

            if (empty($this->categories)) {
                JError::raiseWarning(100, 'Błąd: nie można utworzyć listy kategorii.');
                return false;
            }
            JFactory::getApplication()->enqueueMessage('Pobrano ' . count($this->categories) . ' kategorii z serwera ceneo.');
            unset($xml_data);

            // zapis do bazy
            $db = JFactory::getDBO();
            $q = "TRUNCATE " . $this->table;
            $db->setQuery($q);
            $result = $db->query();
            if (empty($result)) {
                JError::raiseWarning(100, 'Błąd: nie można wyczyścić tablicy z aktualnych kategorii.');
                return false;
            }
            JFactory::getApplication()->enqueueMessage(' Usunięto aktualną zawartość kategorii ceneo.');

            $errors = 0;
            $k = 0;
            foreach ($this->categories as $cat) {
                $q = "INSERT INTO " . $this->table . "(ceneo_cat_id, name, parent_id, link, last) VALUES(" . $cat->id . ",'" . $cat->name . "'," . $cat->parent_id . ", '" . $cat->link . "', " . $cat->lastCategory . ") ;";
                $db->setQuery($q);
                $result = $db->query();
                if (empty($result)) {
                    $errors++;
                }
                $k++;
            }

            if ($errors != 0) {
                JError::raiseWarning(100,
                    'Błąd: wystąpił bład podczas dodawania kategorii do bazy danych, nie zapisano ' . $errors . ' kategorii.');
                return false;
            }
            JFactory::getApplication()->enqueueMessage('Wypełniono tabelę kategorii ceneo liczbą ' . $k . ' kategorii.');

        } else {
            JError::raiseWarning(100, 'Błąd: nie można pobrać pliku kategorii z serwera ceneo (' . $url . ').');
            return false;
        }

    }

    private function getCeneoFile($url)
    {
        $xml_handle = null;
        if (function_exists('curl_init')) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            $xml_handle = curl_exec($curl);
            if ($xml_handle === false) {
                return false;
            }
            curl_close($curl);
            return $xml_handle;
        } else {
            $xml_handle = file_get_contents($url);
            return $xml_handle;
        }
    }

    private function getSubcategories(SimpleXMLElement $category)
    {
        $lenght_categories = count($this->categories);
        $last_category = $this->categories[$lenght_categories - 1];

        if (isset($category->Subcategories->Category)) {
            foreach ($category->Subcategories->Category as $subcategory) {
                $this->categories[] = new ceneoCategory($subcategory->Id, $subcategory->Name,
                    $last_category->link . "/" . $subcategory->Name, $category->Id);
                $this->getSubcategories($subcategory, $this->categories);
            }
        } else {
            $last_category->isLast();
        }
    }

    public function checkCeneoCatsTable()
    {
        $db = JFactory::getDBO();
        $q = "SELECT COUNT(*) FROM " . $this->table;
        $db->setQuery($q);
        $result = $db->loadResult();
        return $result;
    }
}
