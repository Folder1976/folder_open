<!-- Sergey Kotlyarov 2016 folder.list@gmail.com -->
<?php
$file = explode('/', __FILE__);
if(strpos($_SERVER['PHP_SELF'], $file[count($file)-1]) !== false){
	header("Content-Type: text/html; charset=UTF-8");
	die('Прямой запуск запрещен!');
}

$key = 'country_id';
$table = 'country';

include "class/country.class.php";
$Country = new Country();

$CountryList = $Country->getCountrys();

?>
<br>
<!--script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/js/backend/ajax_edit_attributes.js"></script-->
<h1>Справочник : Страны</h1>
<div style="width: 90%">
<div class="table_body">

<table class="text">
    <tr>
        <th>id</th>
        <th>Название</th>
        <th>ISO-2</th>
        <th>ISO-3</th>
        <th>Активный</th>
        <th>Сорт</th>
        <th>&nbsp;</th>
    </tr>

    <tr>
        <td class="mixed">новый</td>
        <td class="mixed"><input type="text"        id="name" style="width:300px;" value="" placeholder="Название страны"></td>
        <td class="mixed"><input type="text"        id="iso_code_2" style="width:150px;" value="" placeholder="код страны"></td>
        <td class="mixed"><input type="text"        id="iso_code_3" style="width:150px;" value="" placeholder="код страны"></td>
		<td class="mixed"><input type="checkbox"    id="enable"  checked ></td>
        <td class="mixed"><input type="text"        id="sort_order" style="width:100px;" value=""></td>
        <td>        
            <a href="javascript:" class="add">
                <img src="/<?php echo TMP_DIR; ?>backend/img/add.png" title="Добавить" width="16" height="16">
            </a>
        </td>              
    </tr>
    <td>
        <td colspan="6">&nbsp;</td>
    </td>

<?php foreach($CountryList as $index => $ex){ ?>
    <tr id="<?php echo $ex[$key];?>">
        <td class="mixed"><?php echo $ex[$key];?></td>
        <td class="mixed"><input type="text" class="edit" id="name<?php echo $ex[$key];?>" style="width:300px;" value="<?php echo $ex['name']; ?>"></td>
        <td class="mixed"><input type="text" class="edit" id="iso_code_2<?php echo $ex[$key];?>" style="width:150px;" value="<?php echo $ex['iso_code_2']; ?>"></td>
        <td class="mixed"><input type="text" class="edit" id="iso_code_3<?php echo $ex[$key];?>" style="width:150px;" value="<?php echo $ex['iso_code_3']; ?>"></td>
        
		<td class="mixed"><input type="checkbox" class="edit" id="enable<?php echo $ex[$key];?>"  <?php if($ex['enable']) echo 'checked';?>></td>
        <td class="mixed"><input type="text" class="edit" id="sort_order<?php echo $ex[$key];?>" style="width:100px;" value="<?php echo $ex['sort_order']; ?>"></td>
        <td>        
            <a href="javascript:;" class="dell" data-id="<?php echo $ex[$key];?>">
                <img src="/<?php echo TMP_DIR; ?>backend/img/cancel.png" title="удалить" width="16" height="16">
            </a>
           </td>              
    </tr>
<?php } ?>

</table>
<input type="hidden" id="table" value="<?php echo $table; ?>">
<script>

    
</script>



</div>

</div>


<script>
	 //======================================================================   
    
    jQuery(document).on('change','.edit', function(){
        var id = jQuery(this).parent('td').parent('tr').attr('id');
        var name = jQuery('#name'+id).val();
        var iso_code_2 = jQuery('#iso_code_2'+id).val();
        var iso_code_3 = jQuery('#iso_code_3'+id).val();
        var enable_tmp = 0;
        var sort = jQuery('#sort_order'+id).val();
        var table = jQuery('#table').val();
        
        if (jQuery('#enable'+id).prop('checked')) {
             enable_tmp = 1;
        }
        
		name = name.replace('=', '*1*');
		name = name.replace('&', '@*@');
		
        jQuery.ajax({
            type: "POST",
            url: "/<?php echo TMP_DIR; ?>backend/ajax/ajax_guideuniversal.php",
            dataType: "text",
            data: "id="+id+"&iso_code_2="+iso_code_2+"&iso_code_3="+iso_code_3+"&name="+name+"&enable="+enable_tmp+"&sort_order="+sort+"&key=edit_country",
            beforeSend: function(){
            },
            success: function(msg){
                console.log( msg );
            }
        });
        
    });
 
    jQuery(document).on('click','.add', function(){
		
		//console.log('11 '+name);   
        var id = 0;
        var name = jQuery('#name').val();
		var iso_code_2 = jQuery('#iso_code_2').val();
        var iso_code_3 = jQuery('#iso_code_3').val();
	    var enable_tmp = 0;
        var sort = jQuery('#sort_order').val();
        var table = jQuery('#table').val();
        
		name = name.replace('=', '*1*');
		name = name.replace('&', '@*@');
		
        if (jQuery('#enable').prop('checked')) {
             enable_tmp = 1;
        }
     
        if (name != "") {
            jQuery.ajax({
                type: "POST",
                url: "/<?php echo TMP_DIR; ?>backend/ajax/ajax_guideuniversal.php",
                dataType: "text",
                data: "id="+id+"&iso_code_2="+iso_code_2+"&iso_code_3="+iso_code_3+"&name="+name+"&enable="+enable_tmp+"&sort_order="+sort+"&key=add_country",
                beforeSend: function(){
                },
                success: function(msg){
                    console.log( msg );
                   // location.reload();
                }
            });
        }
        
    });
    
    jQuery(document).on('click','.dell', function(){
        var id = jQuery(this).data('id');
        var table = jQuery('#table').val();
        
        if (confirm('Вы действительно желаете удалить страну?')){
            jQuery.ajax({
                type: "POST",
                url: "/<?php echo TMP_DIR; ?>backend/ajax/ajax_guideuniversal.php",
                dataType: "text",
                data: "id="+id+"&key=dell_country",
                beforeSend: function(){
                },
                success: function(msg){
                    console.log( msg );
                    jQuery('#'+id).hide();
                }
            });
        }
    });
    //======================================================================
</script>
