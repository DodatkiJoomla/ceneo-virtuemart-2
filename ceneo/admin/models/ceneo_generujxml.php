<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class Porownywarki_VM2ModelCeneo_generujxml extends JModel
{
    public function getConfig()
    {
        $db = JFactory::getDBO();
        $db->setQuery("SELECT config FROM #__porownywarki_ceneo_config WHERE id=1 ");
        $uniqid = unserialize($db->loadResult());
        $_GET['u'] = $uniqid->uniqid;
        require_once(JPATH_SITE . '/components/com_porownywarki_vm2/ceneoGetProducts.php');
    }
}

