<?php defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.mootools');
$host = JURI::base();
$document =& JFactory::getDocument();
$document->addStyleSheet($host . 'components/' . $_REQUEST['option'] . '/assets/ceneo.css');
?>


<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div class="width-60 ">
        <fieldset class="adminform">
            <legend>Dodaj nową kategorię do wykluczenia:</legend>
            <ul class="adminformlist">

                <li>
                    <label>Wybierz kategorię VM2:</label>
					<span>
                        <select name="excluded_cat[]" multiple="multiple" size="10">
                            <option value='0'>-- wybierz --</option>
                            <?php

                            foreach ($this->cats as $cat) {
                                echo "<option value='" . $cat->virtuemart_category_id . "'>" . $cat->category_name . " [id: " . $cat->virtuemart_category_id . "]</option>";
                            }

                            ?>
                        </select>
                    </span>
                </li>

            </ul>
            <div class="clr"></div>
        </fieldset>
    </div>

    <input type="hidden" name="option" value="com_porownywarki_vm2"/>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="controller" value="ceneo_wykluczone_kategorie"/>
</form>