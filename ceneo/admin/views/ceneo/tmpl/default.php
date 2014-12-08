<?php defined('_JEXEC') or die('Restricted access'); 
JHTML::_('behavior.framework', true);
$host = JURI::base();
$document =& JFactory::getDocument();
$document->addStyleSheet($host.'components/'.$_REQUEST['option'].'/assets/ceneo.css');
?>

<div class="adminform">
	<div class="cpanel-left">
		<div class="cpanel">
			<div class="icon-wrapper">
				<div class="icon">
					<a href="index.php?option=com_porownywarki_vm2&controller=ceneo_kategorie">
						<img src="<?php echo JURI::base(); ?>components/com_porownywarki_vm2/assets/ceneo_category.png" style="width: 40px;" alt="">
						<span>Powiąż kategorie Ceneo i VM</span>
					</a>
				</div>
			</div>
		</div>
		<div class="cpanel">
			<div class="icon-wrapper">
				<div class="icon">
					<a href="index.php?option=com_porownywarki_vm2&controller=ceneo_dostepnosc">
						<img src="<?php echo JURI::base(); ?>components/com_porownywarki_vm2/assets/ceneo_dostepny.png" style="width: 40px;" alt="">
						<span>Powiąż stany dostepności</span>
					</a>
				</div>
			</div>
		</div>
		<div class="cpanel">
			<div class="icon-wrapper">
				<div class="icon">
					<a href="index.php?option=com_porownywarki_vm2&controller=ceneo_generujxml">
						<img src="<?php echo JURI::base(); ?>components/com_porownywarki_vm2/assets/xml_create_icon.png" alt="">
						<span>Generuj plik XML</span>
					</a>
				</div>
			</div>
		</div>
		<div class="cpanel">
			<div class="icon-wrapper">
				<div class="icon">
					<a href="index.php?option=com_porownywarki_vm2&controller=ceneo&task=updateDB">
						<img src="<?php echo JURI::base(); ?>components/com_porownywarki_vm2/assets/generate.png" style="width: 40px;" alt="">
						<span>Aktualizuj kategorie Ceneo</span>
					</a>
				</div>
			</div>
		</div>
        <div class="cpanel">
            <div class="icon-wrapper">
                <div class="icon">
                    <a href="index.php?option=com_porownywarki_vm2&controller=ceneo_wykluczone_kategorie">
                        <img src="<?php echo JURI::base(); ?>components/com_porownywarki_vm2/assets/ceneo_wykluczone_kategorie.png" style="width: 40px;" alt="">
                        <span>Wykluczone<br />kategorie</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="cpanel">
            <div class="icon-wrapper">
                <div class="icon">
                    <a href="index.php?option=com_porownywarki_vm2&controller=ceneo_wykluczone_produkty">
                        <img src="<?php echo JURI::base(); ?>components/com_porownywarki_vm2/assets/ceneo_wykluczone_produkty.png" style="width: 40px;" alt="">
                        <span>Wykluczone<br />produkty</span>
                    </a>
                </div>
            </div>
        </div>
		<div class="cpanel">
			<div class="icon-wrapper">
				<div class="icon">
					<a href="index.php?option=com_porownywarki_vm2&controller=ceneo_konfiguracja">
						<img src="<?php echo JURI::base(); ?>components/com_porownywarki_vm2/assets/ceneo_config.png" style="width: 40px;" alt="">
						<span>Konfiguracja Ceneo</span>
					</a>
				</div>
			</div>
		</div>


	</div>
	<div class="cpanel-right">		
		<div id="panel-sliders" class="pane-sliders" style="margin-top: 0px;">
			<div class="panel">
				<h3 class="title pane-toggler" id="cpanel-panel-logged">
					<a href="javascript:void(0);"><span>Aktualności - Ceneo dla Virtuemart 2</span></a>
				</h3>
				<div class="pane-slider content pane-hide" >
					<iframe src="http://dodatkijoomla.pl/index.php?option=com_k2&view=itemlist&layout=category&task=category&id=17&tmpl=component" style="width: 100%; max-height: 250px;"></iframe>
				</div>
			</div>
		</div>
	</div>
</div>

<div style="clear: both;"></div> 		
<div style="text-align: center;">     
	<br> <br>Stworzone przez:<br>   
	<a target="_blank" href="http://dodatkijoomla.pl/index.php?in=porownywarki_vm2">
		<img border="0" src="http://dodatkijoomla.pl/images/logo_podpis_site_mini.png">
	</a>
	<p> Szukaj najlepszych rozszerzeń dla Joomla na <a target="_blank" href="http://dodatkijoomla.pl/index.php?in=porownywarki_vm2">DodatkiJoomla.pl</a>  
	</p>    
</div>

<?php

if($this->ceneoTableCount==0)
{
	?>
	<script type="text/javascript">		
		alert("Jest to twoje pierwsze uruchomienie komponentu.\n Po zamknięciu tego okna, kliknij w przycisk 'Aktualizuj kategorie Ceneo', aby pobrać plik z kategoriami Ceneo.");
	</script>
	<?php
}

?>
<script type="text/javascript">
window.addEvent('domready', function(){ new Fx.Accordion($$('div#panel-sliders.pane-sliders > .panel > h3.pane-toggler'), $$('div#panel-sliders.pane-sliders > .panel > div.pane-slider'), {onActive: function(toggler, i) {toggler.addClass('pane-toggler-down');toggler.removeClass('pane-toggler');i.addClass('pane-down');i.removeClass('pane-hide');Cookie.write('jpanesliders_panel-sliders',$$('div#panel-sliders.pane-sliders > .panel > h3').indexOf(toggler));},onBackground: function(toggler, i) {toggler.addClass('pane-toggler');toggler.removeClass('pane-toggler-down');i.addClass('pane-hide');i.removeClass('pane-down');if($$('div#panel-sliders.pane-sliders > .panel > h3').length==$$('div#panel-sliders.pane-sliders > .panel > h3.pane-toggler').length) Cookie.write('jpanesliders_panel-sliders',-1);},duration: 300,opacity: false,alwaysHide: true}); });
</script>
