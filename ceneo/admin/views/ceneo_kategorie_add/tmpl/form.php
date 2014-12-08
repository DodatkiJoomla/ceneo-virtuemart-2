<?php defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.mootools');
$host = JURI::base();
$document =& JFactory::getDocument();
$document->addStyleSheet($host . 'components/' . $_REQUEST['option'] . '/assets/ceneo.css');
?>


<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div class="width-60 ">
        <fieldset class="adminform">
            <legend>Powiąż kategorie VM z kategorią Ceneo i ustaw atrybuty</legend>
            <ul class="adminformlist">
                <li>
                    <label><b>Kategorie Virtuemart (nieprzypisane)</b></label>
                </li>
                <li>
                    <label>Filtruj katgorie:</label>
                    <input type="text" name="search_vm_cat" id="search_vm_cat"/>
                </li>
                <li>
                    <label>Wybierz:</label>
					<span id="vm_cat_box">
                        <select name="vm_cat[]" multiple="multiple" size="15">
                            <?php
                            foreach ($this->vm_categories as $vm_cat) {

                                $selected = "";
                                if (in_array($vm_cat->category_id, $this->vm_cat)) {
                                    $selected = "selected='selected'";
                                }
                                echo "<option value='" . $vm_cat->category_id . "' " . $selected . ">[ID:" . $vm_cat->category_id . "] " . $vm_cat->category_name . "</option>";
                            }
                            ?>
                        </select>
                    </span>
                </li>
                <li>
                    <label><b>Kategorie Ceneo</b></label>
                </li>
                <li>
                    <label>Filtruj katgorie:</label>
                    <input type="text" name="search_ceneo_cat" id="search_ceneo_cat"/>
                </li>
                <li>
                    <label>Wybierz:</label>
                    <span id="ceneo_cat_box">
                        <select name="ceneo_cat">

                            <?php
                            echo "<option value=''>-- wybierz kategorię --</option>";
                            foreach ($this->ceneo_categories as $ceneo_cat) {
                                $selected = "";
                                if ($this->ceneo_cat == $ceneo_cat->ceneo_cat_id) {
                                    $selected = "selected='selected'";
                                }
                                echo "<option value='" . $ceneo_cat->ceneo_cat_id . "' " . $selected . ">" . str_replace("/",
                                        " / ", $ceneo_cat->link) . "</option>";
                            }
                            ?>
                        </select>
					</span>
                </li>

                <li>
                    <label><b>Atrybuty</b></label>
                </li>

                <li>
                    <label>Typ kategorii</label>
                    <span id="ceneo_cat_box2">
                        <select name="ceneo_category_type" id="ceneo_category_type">

                            <?php

                            foreach ($this->ceneo_category_types as $cat_type) {
                                $selected = "";
                                if ($this->ceneo_category_types_selected == $cat_type->parent_id) {
                                    $selected = "selected='selected'";
                                }
                                echo "<option value='" . $cat_type->parent_id . "' " . $selected . ">" . $cat_type->parent_name_pl . " (" . $cat_type->parent_name . ")</option>";
                            }
                            ?>
                        </select>
					</span>
                </li>

                <li>
                    <label><b>Połącz atrybuty ceneo z polami dodatkowym Virtuemart 2</b></label>
                </li>

                <li>
                    <label>Połącz:</label>
                    <span id="custom_fields">
					<?php

                    echo "<table id='fields_and_attribs' border='0' style='float: left; width: auto; margin: 5px 5px 5px 0;'>";
                    foreach ($this->ceneo_category_types2 as $cat_type) {
                        echo "<tr><td>" . $cat_type->name . "</td><td>";
                        echo "<select name='customF[]'>";
                        echo "<option value=''>-- puste --</option>";

                        $selected_mf = "";
                        if ($cat_type->name == "Producent" || $cat_type->name == "Wytwornia" || $cat_type->name == "Wydawnictwo") {
                            $selected_mf = " selected='selected' ";
                        }

                        echo "
						<optgroup label='Producent'>
							<option " . $selected_mf . " value='" . $cat_type->id . "_mfname'>Nazwa producenta</option>
						</optgroup>
						<optgroup label='Produkt'>
							<option value='" . $cat_type->id . "_sku'>SKU produktu</option>
						</optgroup>
						<optgroup label='Pola dodatkowe'>";
                        foreach ($this->custom_fields as $field) {
                            echo "<option value='" . $cat_type->id . "_" . $field->virtuemart_custom_id . "'>" . $field->custom_title . "</option>";
                        }
                        echo "</optgroup></select></td>";
                        $default = ($cat_type->is_default ? 'atrybut domyślny' : 'atrybut własny &nbsp;&nbsp;&nbsp;<a href="JavaScript:void(0);" onclick="deleteAttribute(' . $cat_type->id . ', this);">Usuń atrybut</a>');
                        echo "<td>" . $default . "</td></tr>";
                    }
                    echo "</table>";
                    ?>
					</span>
                </li>
                <li>
                    <label for="additional_fields">Nowe atrybuty:</label>

                    <div id="additional_fields" style="float:left;">
                        <button>Dodaj nowy atrybut</button>
                    </div>
                </li>
            </ul>
            <div class="clr"></div>
        </fieldset>
    </div>

    <input type="hidden" name="option" value="com_porownywarki_vm2"/>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="controller" value="ceneo_kategorie"/>
</form>
<script type="text/javascript">
$('search_vm_cat').addEvent('keyup', function () {
    var query = "index.php?option=com_porownywarki_vm2&controller=ceneo_kategorie&tmpl=component&task=ajaxgetcats&type=vm&like=" + $('search_vm_cat').value + "&multiple=0";
    var myRequest = new Request({
        url: query,
        method: 'get',
        onRequest: function () {
            $('vm_cat_box').innerHTML = '<select></select>';
        },
        onSuccess: function () {
            $('vm_cat_box').innerHTML = myRequest.response.text;
        }
    });
    myRequest.send();
});


$('search_ceneo_cat').addEvent('keyup', function () {

    var query = "index.php?option=com_porownywarki_vm2&controller=ceneo_kategorie&tmpl=component&task=ajaxgetcats&type=ceneo&like=" + $('search_ceneo_cat').value;
    var myRequest = new Request({
        url: query,
        method: 'get',
        onRequest: function () {
            $('ceneo_cat_box').innerHTML = '<select></select>';
        },
        onSuccess: function () {
            $('ceneo_cat_box').innerHTML = myRequest.response.text;
        }
    });
    myRequest.send();
});

$('ceneo_category_type').addEvent('change', function () {
    var query = "index.php?option=com_porownywarki_vm2&controller=ceneo_kategorie&tmpl=component&task=ajaxgetattributes&parent_id=" + $('ceneo_category_type').value;
    var myRequest = new Request({
        url: query,
        method: 'get',
        onRequest: function () {
            $('custom_fields').innerHTML = "<div style='line-height: 26px; width: 200px;'>Wczytuję...</div>";
        },
        onSuccess: function () {
            $('custom_fields').innerHTML = myRequest.response.text;
        }
    });
    myRequest.send();

    if ($('nowy_atrybut')) {
        $$('select[name=additional_field_parent]').setProperty('value', $('ceneo_category_type').value);
    }

});

$$('#additional_fields button').addEvent('click', function (event) {
        event.preventDefault();
        if (!$('nowy_atrybut')) {
            var nowy_atrybut = new Element('div#nowy_atrybut');
            var select_input_text = new Element('div', {text: 'Wybierz typ kategorii atrybutu:'});
            var select_input = $('ceneo_cat_box2').getElement('select').clone(true, false);
            select_input.setProperty('name', 'additional_field_parent');
            select_input.setStyle('float', 'none');
            var name_input_text = new Element('div', {text: 'Podaj nazwę atrybutu:'});
            var name_input = new Element('input', {name: 'additional_field_name', value: ''});
            name_input.setStyle('float', 'none');

            var custom_f_select = $$('select[name^=customF]');
            var custom_f_select_first_text = new Element('div', {text: 'Wybierz pole dodatkowe:'});
            var custom_f_select_first = custom_f_select[0].clone(true, false);
            custom_f_select_first.setStyle('float', 'none');
            var custom_f_select_first_option = custom_f_select_first.getElement('option');
            custom_f_select_first_option.setProperty('selected', 'selected');
            //console.log(custom_f_select)
            custom_f_select_first.setProperty('name', 'additional_field_custom_field');

            var save_button = new Element('button#save_additional_field', {
                text: 'Zapisz',
                styles: {
                    float: 'none',
                    display: 'block'
                },
                events: {
                    click: function (event) {
                        event.preventDefault();

                        var additional_field_parent = $$('select[name=additional_field_parent]').getProperty('value');
                        var additional_field_name = $$('input[name=additional_field_name]').getProperty('value');
                        var new_custom_field_xref = $$('select[name=additional_field_custom_field]').getProperty('value');
                        //console.log(check_input);
                        //console.log(check_select);
                        if (additional_field_name == "" || new_custom_field_xref == "") {
                            alert('Wypełnij wszystkie pola nowego atrybutu!');
                            return false;
                        }
                        else {
                            var query = "index.php?option=com_porownywarki_vm2&controller=ceneo_kategorie&tmpl=component&task=save_attribute&additional_field_parent=" + additional_field_parent + "&additional_field_name=" + additional_field_name;
                            var myRequest = new Request({
                                url: query,
                                method: 'get',
                                onRequest: function () {
                                    $('save_message').innerHTML = 'Zapisuję...';
                                },
                                onSuccess: function () {
                                    var ajax_responde = myRequest.response.text;
                                    if (ajax_responde != "Wystąpił błąd podczas zapisywania atrybutu do bazy danych.") {
                                        $('save_message').innerHTML = ajax_responde;

                                        // dodaj nowy wiersz w tabeli, jeśli te same kategorie
                                        if ($('ceneo_category_type').value == additional_field_parent.toString()) {
                                            var fields_and_attribs_table = new HtmlTable($('fields_and_attribs'));
                                            var custom_f_new_attrib = custom_f_select_first.clone(true, false);
                                            custom_f_new_attrib.setProperty('name', 'customF[]');
                                            var custom_f_new_attrib_options = custom_f_new_attrib.getElements('option');
                                            custom_f_new_attrib_options.each(function (item) {
                                                    var item_value = item.value;
                                                    if (item_value != "") {
                                                        var regexp_pattern = /\d+_/;
                                                        //console.log(item_value);
                                                        //console.log(item_value.replace(regexp_pattern, ajax_responde + '_'));
                                                        item.setProperty('value', item_value.replace(regexp_pattern, ajax_responde + '_'));
                                                    }

                                                }
                                            )

                                            fields_and_attribs_table.push([additional_field_name.toString(), custom_f_new_attrib, 'atrybut własny &nbsp;&nbsp;&nbsp;<a href="JavaScript:void(0);" onclick="deleteAttribute(' + ajax_responde + ', this)">Usuń atrybut</a>']);
                                        }

                                        $('nowy_atrybut').destroy();
                                    }
                                    else {
                                        $('save_message').innerHTML = ajax_responde;
                                    }
                                }
                            });
                            myRequest.send();

                        }

                    }
                }
            });

            var save_message = new Element('span#save_message');


            nowy_atrybut.grab(select_input_text);
            nowy_atrybut.grab(select_input);
            nowy_atrybut.grab(name_input_text);
            nowy_atrybut.grab(name_input);
            nowy_atrybut.grab(custom_f_select_first_text);
            nowy_atrybut.grab(custom_f_select_first);
            nowy_atrybut.grab(save_button);
            nowy_atrybut.grab(save_message);

            $('additional_fields').grab(nowy_atrybut);
            $('nowy_atrybut').setStyle('clear', 'both');
            $('nowy_atrybut').setStyle('margin-top', '40px');

        }
    }
);

function deleteAttribute(ceneo_cat_type_id, deleted_row) {
    var rows = $$('#fields_and_attribs tr');

    var query = "index.php?option=com_porownywarki_vm2&controller=ceneo_kategorie&tmpl=component&task=delete_attribute&attribute_id=" + ceneo_cat_type_id;
    var myRequest = new Request({
        url: query,
        method: 'get',
        onRequest: function () {

        },
        onSuccess: function () {
            var ajax_responde = myRequest.response.text;
            if (ajax_responde == 1) {
                deleted_row.parentNode.parentNode.destroy();
            }
            else {
                var inner = deleted_row.parentNode.innerHTML;
                deleted_row.parentNode.innerHTML = inner + " Wystąpił błąd podczas usuwania atrybutu.";
            }
        }
    });
    myRequest.send();

    //Wystąpił błąd podczas usuwania atrybutu
}

</script>