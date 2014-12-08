<?php defined('_JEXEC') or die('Restricted access'); 
JHTML::_('behavior.mootools');
$host = JURI::base();
$document =& JFactory::getDocument();
$document->addStyleSheet($host.'components/'.$_REQUEST['option'].'/assets/ceneo.css');
?>


<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div class="width-60 ">
		<fieldset class="adminform">
			<legend>Połącz dostępności Virtuemart 2 z dostępnościami Ceneo</legend>
			<ul class="adminformlist">

				<li>
					<label>Wybierz dostępności VM2:</label>
					<span>
                        <select name="vm_avails_selected[]" multiple="multiple" size="15">
                            <?php
                            foreach($this->vm_avails as $vm_avail)
                            {
                                $selected = "";
                                if(in_array($vm_avail->product_availability,$this->vm_avails_selected))
                                    $selected = "selected='selected'";
								if($vm_avail->product_availability=="")	
									echo "<option value='".$vm_avail->product_availability."' ".$selected.">(puste pole dostępności)</option>";
								else
									echo "<option value='".$vm_avail->product_availability."' ".$selected.">".$vm_avail->product_availability."</option>";
                            }
                            ?>
                        </select>
                    </span>
				</li>
				<li>
					<label>Wybierz dostępność Ceneo:</label>
					<span>
                        <select name="ceneo_avail_selected">
                            <?php
							echo "<option value=''>-- wybierz --</option>";
                            foreach($this->ceneo_avails as $k => $ceneo_avail)
                            {
								
                                $selected = "";
                                if($this->ceneo_avail_selected==$ceneo_avail)
                                    $selected = "selected='selected'";
                                echo "<option value='".$ceneo_avail."' ".$selected.">".$k."</option>";
                            }
                            ?>
                        </select>
                    </span>
				</li>
				
			</ul>
			<div class="clr"></div>
		</fieldset>
	</div>
	
    <input type="hidden" name="option" value="com_porownywarki_vm2" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />	
	<input type="hidden" name="controller" value="ceneo_dostepnosc" />
</form>