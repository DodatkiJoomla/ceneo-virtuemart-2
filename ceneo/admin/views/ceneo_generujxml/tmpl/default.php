<?php defined('_JEXEC') or die('Restricted access'); 
JHTML::_('behavior.modal'); 
$host = JURI::base();
$document =& JFactory::getDocument();
$document->addStyleSheet($host.'components/'.$_REQUEST['option'].'/assets/ceneo.css');
?>

<div class="adminform" id="adminForm">
    <?php
    if(file_exists(JPATH_ROOT."/".FILE_NAME))
    {
        $link_file = JURI::root().FILE_NAME;
		$last_change = date ("d-m-Y H:i:s", filemtime(JPATH_ROOT."/".FILE_NAME));
        ?>
        <div class="width-100 ">
            <fieldset class="adminform">
                <legend>Twój aktualny link do pliku XML dla Ceneo (wygenerowany <?php echo $last_change; ?> ):</legend>
                <div><input type="text" size="100" value="<?php echo $link_file; ?>" onclick="this.select()" /></div>
                <div class="clr"></div>
            </fieldset>
        </div>
    <?php
    }
?>
	<div class="width-100 ">
		<fieldset class="adminform">
			<legend>Generuj plik dla Ceneo<!-- - zostanie wygenerowanych <?php // echo TABLE_SIZE; ?> produktów.--></legend>
			<p style="margin: 10px 0; color: red; font-weight: bold;">Uwaga: Każdy produkt musi mieć przypisaną minimum jedną kategorię Virtuemart!</p>
			<button id="generuj" style="padding: 10px 20px; font-weight: bold; font-size: 16px;">Generuj plik teraz!</button>
			<div id="generuj_wynik" style="width: 100%;float: left;"></div>
			<div class="clr"></div>
		</fieldset>
	</div>
	<div class="width-100 ">
		<fieldset class="adminform">
			<legend>Linki dla CRONa, do automatycznego generowania pliku:</legend>
			<ul class="adminformlist">
                <li style="font-weight: bold; padding: 10px 0 0 0;">Kroków potrzebnych do utworzenia pliku XML: <?php $iteracji = ceil(TABLE_SIZE/LIMITED); echo $iteracji; ?></li>
                <li style="padding: 10px 0;">Dodaj poniższe linki do listy zadań Crona, aby plik był wykonywany automatycznie o określonej porze dnia.</li>
                <?php


                $links = array();

                if(TABLE_SIZE<LIMITED)
                {
                    $links[] = JURI::root()."index.php?option=com_porownywarki_vm2&typ=ceneo&u=".UNIQID;
                }
                else
                {
                    for($k=1; $k<=$iteracji; $k++)
                    {
                        $links[] = JURI::root()."index.php?option=com_porownywarki_vm2&typ=ceneo&u=".UNIQID."&tmpl=component&i=".$k ;
                    }
                }

                for($i = 1; $i<=20; $i++)
                {
                ?>
				<li>
					<label>Link <?php echo $i; ?>:</label>
					<span>
						<input type="text" size="150" value="<?php echo $links[$i-1]; ?>" onclick="this.select()" />
					</span>
				</li>
                <?php
                }
                ?>
			</ul>			
			<div class="clr"></div>
		</fieldset>
	</div>
</div>

<div style="clear: both;"></div> 		

<script type="text/javascript">

window.addEvent("domready", function(){

	$('generuj').addEvent('click', function()
    {
        $('generuj_wynik').innerHTML = '<h4>Generuję plik XML, nie zamykaj tego okna.</h4><h4>Jeśli wystąpi jakikolwiek błąd serwera/php/pamięci/zapytań do bazy danych, zmniejsz liczbę generowanych ofert na krok (zrobisz to w konfiguracji ceneo - <a target="_blank" href="<?php echo JURI::root(); ?>administrator/index.php?option=com_porownywarki_vm2&controller=ceneo_konfiguracja">tutaj</a>).</h4><iframe src="<?php echo JURI::root(); ?>index.php?option=com_porownywarki_vm2&typ=ceneo&tmpl=component&u=<?php echo UNIQID ?>" style="width: 100%; height: 200px;" frameborder="0" scrolling="no"></iframe>';
		$('generuj').style.display = 'none';
    });

});

</script>