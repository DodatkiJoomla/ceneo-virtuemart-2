<?php defined('_JEXEC') or die('Restricted access'); 
JHTML::_('behavior.modal'); 
$host = JURI::base();
$document =& JFactory::getDocument();
$document->addStyleSheet($host.'components/'.$_REQUEST['option'].'/assets/ceneo.css');
?>
<h4>W poniższej tabeli znajduja się powiązane dostępności produktu w VM2 (za ile np. dni produkt będzie dostępny - jeśli nie ma go na stanie), ze stanami dostępności w Ceneo.</h4>

<form action="index.php" method="post" name="adminForm">
<div id="editcell">

    <table class="adminlist">
    <thead>
        <tr>
            <th width="5">
                <?php echo JText::_( '#' ); ?>
            </th>
			<th width="20">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->powiazania ); ?>);" />
			</th>
            <th width="10">
                <?php echo JText::_( 'ID' ); ?>
            </th>
			<th >
                <?php echo JText::_( 'Powiązana dostepność produktu w Ceneo' ); ?>
            </th>
			<th >
                <?php echo JText::_( 'Typ dostępności produktu VM' ); ?>
            </th>
        </tr>            
    </thead>
    <?php
    $k = 0;

    foreach ($this->powiazania as $klucz => &$rows )
    {
	    $checked    = JHTML::_( 'grid.id', $klucz, $rows->id );

        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td align=center>
                <?php echo $klucz+1; ?>
            </td >
			<td align=center>
				<?php echo $checked; ?>
			</td>
            <td>
                <?php  echo $rows->id; ?>
            </td>
			<td align=center>
                <?php echo $rows->ceneo_avail; ?>
            </td >
			<td align=center>
               <?php
				if($rows->product_availability=="")
					echo "(puste pole dostępności)";
				else
					echo $rows->product_availability;

			   ?>
            </td >
        </tr>
        <?php
        $k = 1 - $k;
    }
    ?>
    </table>
</div>
 
<input type="hidden" name="option" value="com_porownywarki_vm2" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="ceneo_dostepnosc" />
 
</form>

