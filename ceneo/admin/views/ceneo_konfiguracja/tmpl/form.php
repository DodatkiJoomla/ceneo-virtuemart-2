<?php 
defined('_JEXEC') or die('Restricted access'); 
$host = JURI::base();
$document =& JFactory::getDocument();
$document->addStyleSheet($host.'components/'.$_REQUEST['option'].'/assets/ceneo.css');
JHTML::_('behavior.tooltip');
?>


<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend>Ustawienia konfiguracyjne Ceneo</legend>
			<ul class="adminformlist">
                <li>
                    <label style="font-weight: bold;">Ustawienia informacji o produktach</label>
                </li>
				<li>
					<label>Używany opis:</label>
					<select name="configi[desc_type]">
						<option value="product_desc" <?php $this->config->desc_type=="product_desc" ? print " selected='selected' ": print "" ; ?>>Opis</option>
						<option value="product_s_desc" <?php $this->config->desc_type=="product_s_desc" ? print " selected='selected' ": print "" ; ?>>Krótki opis</option>
					</select>
				</li>
				<li>
					<label>Ustawienia dostępności produktów:</label>
					<select name="configi[dostepnosc]">
						<option value="0" <?php $this->config->dostepnosc=="0" ? print " selected='selected' ": print "" ; ?>>Korzystaj z powiązań między dostępnościami w VM a Ceneo (ustawienia z komponentu) oraz liczbą prod. na stanie</option>
						<option value="1" <?php $this->config->dostepnosc=="1" ? print " selected='selected' ": print "" ; ?>>Wszystkie produkty jako zawsze dostępne</option>
						<option value="2" <?php $this->config->dostepnosc=="2" ? print " selected='selected' ": print "" ; ?>>Dostępność produktu niewidoczna w Ceneo, pokazany link do podstorny prod. w sklepie int.</option>
					</select>
				</li>
				<li>
					<label>Ustawienia dublowanych produktów:</label>
					<select name="configi[duplicates_mode]">
						<option value="0" <?php $this->config->duplicates_mode=="0" ? print " selected='selected' ": print "" ; ?>>Usuń wszystkie zdublowane rekordy, pokazuj produkt tylko z 1 najbardziej zagnieżdżoną kategorią.</option>
						<option value="1" <?php $this->config->duplicates_mode=="1" ? print " selected='selected' ": print "" ; ?>>Pozwalaj na zdublowane produkty z każdej kategorii.</option>
						<option value="2" <?php $this->config->duplicates_mode=="2" ? print " selected='selected' ": print "" ; ?>>Pozwalaj na zdublowane produkty tylko w najbardziej zagnieżdżonych kategoriach (pokazuj kategorie głównego poziomu, nie posiadające potomków).</option>
						<option value="3" <?php $this->config->duplicates_mode=="3" ? print " selected='selected' ": print "" ; ?>>Pozwalaj na zdublowane produkty tylko w najbardziej zagnieżdżonych kategoriach (ukryj kategorie głównego poziomu).</option>
					</select>
					<?php
                    echo JHTML::tooltip('Jeżeli produkt jest dodany do kilku kategorii Virtuemart, dla każdej z kategorii tworzony jest wpis produktu w pliku XML. Ustaw odpowiednią opcję dla twojego sklepu z menu obok.', 'Dublowanie produktów w pliku XML', 'tooltip.png');
                    ?>
				</li>
                <li>
                    <label style="font-weight: bold;">Liczba generowanych produktów</label>
                </li>
				<li>
					<label>Liczba produktów przetwarzanych w jednym kroku generowania XMLa:</label>
					<input class="inputbox" name="configi[limited]" value="<?php echo $this->config->limited; ?>" />
				</li>
                <li>
                    <label>Włączaj do XMLa tylko opublikowane produkty:</label>
                    <select name="configi[only_published]">
                        <option value="0" <?php $this->config->only_published =="0" ? print " selected='selected' ": print "" ; ?>>Nie</option>
                        <option value="1" <?php $this->config->only_published =="1" ? print " selected='selected' ": print "" ; ?>>Tak</option>
                    </select>
                </li>
                <li>
                    <label>Generuj XML dla produktów bez przypisanego producenta (zalecana opcja - "NIE"):</label>
                    <select name="configi[also_without_mfs]">
                        <option value="0" <?php $this->config->also_without_mfs =="0" ? print " selected='selected' ": print "" ; ?>>Nie</option>
                        <option value="1" <?php $this->config->also_without_mfs =="1" ? print " selected='selected' ": print "" ; ?>>Tak</option>
                    </select>
                    <?php
                        echo JHTML::tooltip('Niezalecane, może spowolnić tworzenie XMLa przy bazie danych z dużą liczbą produktów. Jeśli plik nie chce się w ogóle wygenerować po włączeniu tej opcji, należy zmniejszyć "Liczbę ofert przetwarzanych w jednym kroku".', 'Produkty bez przypisanych producentów', 'tooltip.png');
                    ?>
                </li>
                <li>
                    <label>Generuj XML dla produktów bez powiązanych kategorii między kategoriami sklepu a kategoriami Ceneo (zalecana opcja - "NIE"):</label>
                    <select name="configi[also_without_linked_cats]">
                        <option value="0" <?php $this->config->also_without_linked_cats =="0" ? print " selected='selected' ": print "" ; ?>>Nie</option>
                        <option value="1" <?php $this->config->also_without_linked_cats =="1" ? print " selected='selected' ": print "" ; ?>>Tak</option>
                    </select>
                    <?php
                    echo JHTML::tooltip('Niezalecane, może spowolnić tworzenie XMLa przy bazie danych z dużą liczbą produktów. Jeśli plik nie chce się w ogóle wygenerować po włączeniu tej opcji, należy zmniejszyć "Liczbę ofert przetwarzanych w jednym kroku".', 'Produkty bez powiązanych kategorii', 'tooltip.png');
                    ?>
                </li>
                <li>
                    <label style="font-weight: bold;">Ustawienia pliku XML:</label>
                </li>
                <li>
                    <label>Nazwa pliku:</label>
                    <?php $link_file = JURI::root().'ceneo_'.$this->config->file_name.'.xml'; ?>
                    <span style="display: block; float: left; margin: 5px 0px 5px 0;">ceneo_</span><input class="inputbox" style="margin-right: 0px;" name="configi[file_name]" value="<?php echo $this->config->file_name; ?>" /><span style="display: block; float: left; margin: 5px 0px 5px 0px;">.xml</span><span style="display: block; float: left; margin: 5px 0px 5px 30px;">Twój link do XML'a:</span><input class="inputbox" style="margin-left: 10px;" size="50" type="text" value="<?php echo $link_file;?>" on-click="this.select()"><a style="display: block; float: left; margin: 5px 0px 5px 0px;" target="_blank" href="<?php echo $link_file;?>"><?php echo $link_file;?></a>
                </li>
                <?php
                //DJ 2013-11-20 Usuwam START

                //DJ 2013-11-20 Usuwam KONIEC
                ?>
				<!-- DJ 2013-07-16 -->
				<li>
                    <label style="font-weight: bold;">Rabaty:</label>
					<label>Ustawienia rabatów wg. kategorii produktu:</label>
                    <select name="configi[rabaty_kategorie]" id="rabaty_kategorie" <?php echo ( $this->config->also_without_linked_cats == "1" ? " disabled='disabled' " : "" ) ?> >
                        <option value="0" <?php $this->config->rabaty_kategorie =="0" ? print " selected='selected' ": print "" ; ?>>Wyświetlaj rabaty ze wszystkich kategorii produktu (także nieopublikowanych, domyślne zachowanie VM2).</option>
                        <option value="1" <?php $this->config->rabaty_kategorie =="1" ? print " selected='selected' ": print "" ; ?>>Wyświetlaj rabaty tylko z 1, przypisanej do produktu w XMLu kategorii.</option>
                    </select>
					<?php
                        echo JHTML::tooltip('<b>Opcja dostępna tylko dla wcześnijeszego ustawienia "Generuj XML dla produktów bez powiązanych kategorii między kategoriami sklepu a kategoriami Ceneo" z opcją "NIE".</b>.','Ustawienie opcji', 'tooltip.png');
                    ?>
                </li>
				<li>
                    <label style="font-weight: bold;">Inne ustawienia:</label>
					<label>"Kup na Ceneo" - czy opcja włączona?:</label>
                    <select name="configi[buy_ceneo_basket]">
                        <option value="0" <?php $this->config->buy_ceneo_basket =="0" ? print " selected='selected' ": print "" ; ?>>Nie</option>
                        <option value="1" <?php $this->config->buy_ceneo_basket =="1" ? print " selected='selected' ": print "" ; ?>>Tak</option>
                    </select>
                </li>
			</ul>
			<div class="clr"></div>
			<input type="hidden" name="configi[uniqid]" value="<?php echo $this->config->uniqid; ?>" />	
			<div class="clr"></div>
		</fieldset>
	</div>
	
    <input type="hidden" name="option" value="com_porownywarki_vm2" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />	
	<input type="hidden" name="controller" value="ceneo_konfiguracja" />
</form>