<?php defined('_JEXEC') or die('Restricted access'); 
JHTML::_('behavior.modal'); 
$host = JURI::base();
$document =& JFactory::getDocument();
$document->addStyleSheet($host.'components/'.$_REQUEST['option'].'/assets/ceneo.css');
?>


<form action="index.php" method="post" name="adminForm">
<div id="editcell">
    <table>
		<tr>
			<td width="100%">
				<?php echo JText::_('Filter'); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->search;?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="document.adminForm.submit();">Szukaj</button>

				<button onclick="document.getElementById('search').value=''; this.form.submit();">Wyczyść</button>
			</td>
		</tr>
	</table>

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
                <?php echo JText::_( 'Nazwa kategorii Virtuemart [ID]' ); ?>
            </th>
			<th >
                <?php echo JText::_( 'Nazwa kategorii Ceneo [ID] [LINK]' ); ?>
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
               <?php echo $rows->category_name; ?> [<?php echo $rows->virtuemart_cat_id; ?>]
            </td >
			<td align=center>
                <?php echo $rows->name; ?> [<?php echo $rows->ceneo_cat_id; ?>] [<?php echo $rows->link; ?>]
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
<input type="hidden" name="controller" value="ceneo_kategorie" />
 
</form>

