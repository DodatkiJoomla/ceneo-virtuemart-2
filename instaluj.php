<?php
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

class plgSystemPorownywarki_vm2_ceneoInstallerScript
{

    // właściwość przechowująca wersję rozszerzenia.
    private $_version;

    /**
     * method to install the component
     *
     * @return void
     */
    function install($parent)
    {
    }

    /**
     * method to uninstall the component
     *
     * @return void
     */
    function uninstall($parent)
    {
        $ext_name = "porownywarki_vm2_ceneo";
        $folder = "/system/" . $ext_name . "/ceneo";
        $site = $folder . "/site/";
        $admin = $folder . "/admin/";

        $files_structure = new ExtensionStructure(JPATH_PLUGINS . $folder);
        $site_files = $files_structure->getSiteFiles();
        $admin_files = $files_structure->getAdminFiles();

        // version 1.0
        foreach ($site_files as $site_file) {
            $com_path = JPATH_ROOT . '/components/com_porownywarki_vm2/' . $site_file;

            if (is_dir($com_path)) {
                JFolder::delete($com_path);
            } else {
                if (file_exists($com_path)) {
                    JFile::delete($com_path);
                }
            }
        }
        foreach ($admin_files as $admin_file) {
            $com_path = JPATH_ROOT . '/administrator/components/com_porownywarki_vm2/' . $admin_file;

            if (is_dir($com_path)) {
                JFolder::delete($com_path);
            } else {
                if (file_exists($com_path)) {
                    JFile::delete($com_path);
                }
            }
        }

        $db = JFactory::getDBO();

        $drop_tables = array(
            '#__porownywarki_ceneo_avail_xref',
            '#__porownywarki_ceneo_cat_types',
            '#__porownywarki_ceneo_config',
            '#__porownywarki_vm2_ceneo_categories',
            '#__porownywarki_vm2_ceneo_vm_cats_xref'
        );

        foreach ($drop_tables as $table) {
            $query = "DROP TABLE IF EXISTS " . $table . " ";
            $db->setQuery($query);
            $result = $db->query();
            if ($result === false) {
                JError::raiseWarning('Plugin Ceneo', 'Błąd podczas usuwania tablicy ' . $table . '.');
            }
        }

        return true;
    }

    /**
     * method to update the component
     *
     * @return void
     */
    function update($parent)
    {

    }

    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    function preflight($type, $parent)
    {
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();

        // Sprawdzam czy komponent bazowy porównywarek jest zainstalowany.
        $q = "SELECT extension_id FROM #__extensions WHERE element LIKE '%porownywarki_vm2%' AND type='component' LIMIT 1 ";
        $db->setQuery($q);
        $result = $db->loadResult();
        if (empty($result)) {
            JError::raiseWarning('Plugin Ceneo', 'Brak zainstalowanego komponentu porównywarek, instalacja przerwana.');
            return false;
        }

        if ($type == 'install') {
            $db = JFactory::getDbo();

            // Wersja zapytania - 1.0.2

            $queries = array();
            $queries[] = "CREATE TABLE IF NOT EXISTS #__porownywarki_ceneo_avail_xref (
			  id int(11) NOT NULL AUTO_INCREMENT,
			  ceneo_avail int(11) NOT NULL,
			  product_availability varchar(50) NOT NULL,
			  PRIMARY KEY (id)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

            $queries[] = "CREATE TABLE IF NOT EXISTS #__porownywarki_ceneo_cat_types (
			  id int(11) NOT NULL AUTO_INCREMENT,
			  parent_id int(11) NOT NULL,
			  parent_name varchar(50) NOT NULL,
			  parent_name_pl varchar(50) NOT NULL,
			  name varchar(100) NOT NULL,
			  is_default int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (id)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;";

            $queries[] = "INSERT INTO `#__porownywarki_ceneo_cat_types` (`id`, `parent_id`, `parent_name`, `parent_name_pl`, `name`, `is_default`) VALUES
			(1, 1, 'books', 'Książki / Ebooki / Audiobooki', 'Autor', 1),
			(2, 1, 'books', 'Książki / Ebooki / Audiobooki', 'ISBN', 1),
			(3, 1, 'books', 'Książki / Ebooki / Audiobooki', 'Ilosc_stron', 1),
			(4, 1, 'books', 'Książki / Ebooki / Audiobooki', 'Wydawnictwo', 1),
			(5, 1, 'books', 'Książki / Ebooki / Audiobooki', 'Rok_wydania', 1),
			(6, 1, 'books', 'Książki / Ebooki / Audiobooki', 'Oprawa', 1),
			(7, 1, 'books', 'Książki / Ebooki / Audiobooki', 'Format', 1),
			(8, 2, 'tires', 'Opony', 'Producent', 1),
			(9, 2, 'tires', 'Opony', 'SAP', 1),
			(10, 2, 'tires', 'Opony', 'EAN', 1),
			(11, 2, 'tires', 'Opony', 'Model', 1),
			(12, 2, 'tires', 'Opony', 'Szerokosc_opony', 1),
			(13, 2, 'tires', 'Opony', 'Profil', 1),
			(14, 2, 'tires', 'Opony', 'Srednica_kola', 1),
			(15, 2, 'tires', 'Opony', 'Indeks_predkosc', 1),
			(16, 2, 'tires', 'Opony', 'Indeks_nosnosc', 1),
			(17, 2, 'tires', 'Opony', 'Sezon', 1),
			(18, 3, 'rims', 'Felgi i kołpaki', 'Producent', 1),
			(19, 3, 'rims', 'Felgi i kołpaki', 'Kod_producenta', 1),
			(20, 3, 'rims', 'Felgi i kołpaki', 'EAN', 1),
			(21, 3, 'rims', 'Felgi i kołpaki', 'Rozmiar', 1),
			(22, 3, 'rims', 'Felgi i kołpaki', 'Rozstaw_srub', 1),
			(23, 3, 'rims', 'Felgi i kołpaki', 'Odsadzenie', 1),
			(24, 0, 'other', 'Inne', 'Producent', 1),
			(25, 0, 'other', 'Inne', 'Kod_producenta', 1),
			(26, 0, 'other', 'Inne', 'EAN', 1),
			(27, 4, 'perfumes', 'Perfumy', 'Producent', 1),
			(28, 4, 'perfumes', 'Perfumy', 'Kod_producenta', 1),
			(29, 4, 'perfumes', 'Perfumy', 'EAN', 1),
			(30, 4, 'perfumes', 'Perfumy', 'Linia', 1),
			(31, 4, 'perfumes', 'Perfumy', 'Rodzaj', 1),
			(32, 4, 'perfumes', 'Perfumy', 'Pojemnosc', 1),
			(33, 5, 'music', 'Płyty muzyczne', 'Wykonawca', 1),
			(34, 5, 'music', 'Płyty muzyczne', 'EAN', 1),
			(35, 5, 'music', 'Płyty muzyczne', 'Nosnik', 1),
			(36, 5, 'music', 'Płyty muzyczne', 'Wytwornia', 1),
			(37, 5, 'music', 'Płyty muzyczne', 'Gatunek', 1),
			(38, 6, 'games', 'Gry PC / Gry na konsole', 'Producent', 1),
			(39, 6, 'games', 'Gry PC / Gry na konsole', 'Kod_producenta', 1),
			(40, 6, 'games', 'Gry PC / Gry na konsole', 'EAN', 1),
			(41, 6, 'games', 'Gry PC / Gry na konsole', 'Platforma', 1),
			(42, 6, 'games', 'Gry PC / Gry na konsole', 'Gatunek', 1),
			(43, 7, 'movies', 'Filmy', 'Rezyser', 1),
			(44, 7, 'movies', 'Filmy', 'EAN', 1),
			(45, 7, 'movies', 'Filmy', 'Nosnik', 1),
			(46, 7, 'movies', 'Filmy', 'Wytwornia', 1),
			(47, 7, 'movies', 'Filmy', 'Obsada', 1),
			(48, 7, 'movies', 'Filmy', 'Tytul_oryginalny', 1),
			(49, 8, 'medicines', 'Leki, suplementy', 'Producent', 1),
			(50, 8, 'medicines', 'Leki, suplementy', 'BLOZ_12', 1),
			(51, 8, 'medicines', 'Leki, suplementy', 'Ilosc', 1),
			(52, 9, 'grocery', 'Delikatesy', 'Producent', 1),
			(53, 9, 'grocery', 'Delikatesy', 'EAN', 1),
			(54, 9, 'grocery', 'Delikatesy', 'Ilosc', 1),
			(55, 10, 'clothes', 'Odzież, obuwie, dodatki', 'Producent', 1),
			(56, 10, 'clothes', 'Odzież, obuwie, dodatki', 'Model', 1),
			(57, 10, 'clothes', 'Odzież, obuwie, dodatki', 'EAN', 1),
			(58, 10, 'clothes', 'Odzież, obuwie, dodatki', 'Kolor', 1),
			(59, 10, 'clothes', 'Odzież, obuwie, dodatki', 'Rozmiar', 1),
			(60, 10, 'clothes', 'Odzież, obuwie, dodatki', 'Kod_produktu', 1),
			(61, 10, 'clothes', 'Odzież, obuwie, dodatki', 'Sezon', 1),
			(62, 10, 'clothes', 'Odzież, obuwie, dodatki', 'Fason', 1),
			(63, 10, 'clothes', 'Odzież, obuwie, dodatki', 'ProductSetId', 1),
			(69, 1, 'books', 'Książki / Ebooki / Audiobooki', 'Fragment', 1),
			(68, 1, 'books', 'Książki / Ebooki / Audiobooki', 'Spis_tresci', 1); ";

            $queries[] = "CREATE TABLE IF NOT EXISTS #__porownywarki_ceneo_config (
			  id int(11) NOT NULL AUTO_INCREMENT,
			  config text NOT NULL,
			  PRIMARY KEY (id)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

            $config_string = new stdClass();
            $config_string->uniqid = uniqid();
            $config_string->stock_small = "10";
            $config_string->stock_medium = "20";
            $config_string->stock_large = "30";
            $config_string->limited = "2000";
            $config_string->desc_type = "product_s_desc";
            $config_string->file_name = uniqid();
            $config_string->dostepnosc = "0";
            $config_string->only_published = "1";
            $config_string->also_without_mfs = "0";
            $config_string->also_without_linked_cats = "0";
            $config_string->excluded_cats = "";
            $config_string->excluded_prods = "";
            $config_string->duplicates_mode = "0";
            $config_string->buy_ceneo_basket = "0";
            $config_string->rabaty_kategorie = "0";
            //$config_string->rabaty_kategorie_dodatkowa = "0";

            $queries[] = "INSERT INTO #__porownywarki_ceneo_config (id, config) VALUES
			(1, '" . serialize($config_string) . "');";

            $queries[] = "CREATE TABLE IF NOT EXISTS #__porownywarki_vm2_ceneo_categories (
			  id int(11) NOT NULL AUTO_INCREMENT,
			  parent_id int(11) DEFAULT NULL,
			  ceneo_cat_id int(11) NOT NULL,
			  name varchar(50) NOT NULL,
			  link varchar(200) NOT NULL,
			  last int(11) NOT NULL,
			  PRIMARY KEY (id)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;";

            $queries[] = "CREATE TABLE IF NOT EXISTS #__porownywarki_vm2_ceneo_vm_cats_xref (
			  id int(11) NOT NULL AUTO_INCREMENT,
			  ceneo_cat_id int(11) NOT NULL,
			  virtuemart_cat_id int(11) NOT NULL,
			  ceneo_category_type int(11) NOT NULL,
			  custom_fields text NOT NULL,
			  ceneo_category_type_name varchar(40) NOT NULL,
			  ceneo_zakres_produktow TEXT NULL,
			  PRIMARY KEY (id)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
			";

            // 1.0.1
            // dodanie kolumny ceneo_zakres_produktow do ograniczeń
            $query_errors = array();
            foreach ($queries as $key => $query) {
                $db->setQuery($query);
                $result = $db->query();
                if ($result == false) {
                    $errors[] = $k;
                }
            }
        }

        if ($type == 'update') {

            $q = "
				SELECT
					manifest_cache
				FROM
					#__extensions
				WHERE
					element = 'porownywarki_vm2_ceneo'
				";
            $db->setQuery($q);
            $manifest_cache = $db->loadResult();

            $manifest_cache_obj = json_decode($manifest_cache);
            $this->_version = $manifest_cache_obj->version;

            // Jeśli niezainstalowane - ustaw 0.
            if ($this->_version == "") {
                $this->_version = 0;
            }

            // Jeżeli zainstalowana wersja jest wyższa niż ta z paczki - zgłoś bład.
            if (version_compare($this->_version, $parent->get('manifest')->version, '>')) {
                JError::raiseWarning('Plugin Ceneo',
                    'Wersja z instalatora jest starsza niż ta zainstalowana, nie można wykonac aktualizacji.');
                return false;
            }


            // Zapytania aktualizujące plugin

            // 1.0.1
            // dodanie kolumny ceneo_zakres_produktow do ograniczeń
            if (version_compare($this->_version, '1.0.1', '<')) {
                $q = "
				ALTER TABLE 
					#__porownywarki_vm2_ceneo_vm_cats_xref
				ADD COLUMN
					ceneo_zakres_produktow TEXT NULL AFTER ceneo_category_type_name; 
				";
                $db->setQuery($q);
                $result = $db->query();

                if ($result === false) {
                    JError::raiseWarning('Plugin Ceneo', 'Błąd podczas aktualizacji do wersji 1.0.1.');
                    return false;
                }
            }

        }


        if (count($query_errors) > 0) {
            JError::raiseWarning('Plugin Ceneo',
                'Wystąpił błąd podczas tworzenie tabel komponentu w bazie danych (nie można było wykonać zapytań numer: ' . implode(', ',
                    $query_errors) . '), pisz na adres pomocy DodatkiJoomla.pl - <a href="mailto:kontakt@dodatkijoomla.pl">(kontakt)</a>');
            return false;
        }

        return true;
    }

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent)
    {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        $ext_name = "porownywarki_vm2_ceneo";
        $folder = "/system/" . $ext_name . "/ceneo";
        $site = $folder . "/site/";
        $admin = $folder . "/admin/";

        $files_structure = new ExtensionStructure(JPATH_PLUGINS . $folder);
        $site_files = $files_structure->getSiteFiles();
        $admin_files = $files_structure->getAdminFiles();

        // version 1.0
        foreach ($site_files as $site_file) {
            $plg_path = JPATH_PLUGINS . $site . $site_file;
            $com_path = JPATH_ROOT . '/components/com_porownywarki_vm2/' . $site_file;


            if (is_dir($plg_path) && !is_dir($com_path)) {
                JFolder::create($com_path, 0775);
            } else {
                if (is_file($plg_path)) {
                    if (!JFile::copy($plg_path, $com_path)) {
                        JError::raiseWarning('Plugin Ceneo',
                            'Wystąpił bład podczas kopiowania ' . $plg_path . ' do ' . $plg_path . '.');
                    }
                }
            }

        }

        foreach ($admin_files as $admin_file) {
            $plg_path = JPATH_PLUGINS . $admin . $admin_file;
            $com_path = JPATH_ROOT . '/administrator/components/com_porownywarki_vm2/' . $admin_file;


            if (is_dir($plg_path) && !is_dir($com_path)) {
                JFolder::create($com_path, 0775);
            } else {
                if (is_file($plg_path)) {
                    if (!JFile::copy($plg_path, $com_path)) {
                        JError::raiseWarning('Plugin Ceneo',
                            'Wystąpił bład podczas kopiowania ' . $plg_path . ' do ' . $plg_path . '.');
                    }
                }
            }
        }

        return true;
    }

    function check_l($d, $p)
    {
        $url = 'http://dodatkijoomla.pl/ch.php?d=' . $d . '&pid=' . $p;
        if (function_exists('curl_init')) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $val = curl_exec($curl);
            curl_close($curl);
            return $val;
        } else {
            $val = file_get_contents($url);
            return $val;
        }
    }
}

class ExtensionStructure
{
    private $_path;
    private $_site_files = array();
    private $_admin_files = array();

    public function __construct($path)
    {
        $this->_path = $path;
        $this->_get_structure($this->_path, 'site');
    }

    private function _get_structure($source)
    {
        if (is_dir($source)) {

            if (strstr($source, 'views/') != false) {
                if (strstr($source, '/admin/') != false) {
                    $this->_admin_files[] = str_replace($this->_path . '/admin/', '', $source);
                } else {
                    $this->_site_files[] = str_replace($this->_path . '/site/', '', $source);
                }
            }

            $dir = opendir($source);
            while (false !== ($entry = readdir($dir))) {
                if ($entry != ".." && $entry != ".") {
                    $this->_get_structure($source . '/' . $entry);
                }
            }
        } else {
            if (strstr($source, '/admin/') != false) {
                $this->_admin_files[] = str_replace($this->_path . '/admin/', '', $source);
            } else {
                $this->_site_files[] = str_replace($this->_path . '/site/', '', $source);
            }
        }
    }

    public function getSiteFiles()
    {
        return $this->_site_files;
    }

    public function getAdminFiles()
    {
        return $this->_admin_files;
    }
}