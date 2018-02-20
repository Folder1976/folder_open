<!-- Sergey Kotlyarov 2016 folder.list@gmail.com -->
<?php
$file = explode('/', __FILE__);
if(strpos($_SERVER['PHP_SELF'], $file[count($file)-1]) !== false){
	header("Content-Type: text/html; charset=UTF-8");
	die('Прямой запуск запрещен!');
}

$key = 'currency_id';
$table = 'currency';

include "class/currency.class.php";
$Currency = new Currency();

$List = $Currency->getCurrencys();

?>
<br>
<!--script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/js/backend/ajax_edit_attributes.js"></script-->
<h1>Справочник : Валюты</h1>
<div style="width: 90%">
<div class="table_body">

<table class="text">
    <tr>
        <th>id</th>
        <th>Название</th>
        <th>Код</th>
        <th>Левый символ</th>
        <th>Правый символ</th>
        <th>Кол. десятков</th>
        <th>Курс</th>
        <th>Значение</th>
        <th>Активен</th>
        <th>Последнее изменение</th>
        <th>&nbsp;</th>
    </tr>

    <tr>
        <td class="mixed">новый</td>
        <td class="mixed"><input type="text"        id="title" style="width:150px;" value="" placeholder="Euro"></td>
     	<td class="mixed"><input type="text"        id="code" style="width:100px;" value="" placeholder="Euro"></td>
     	<td class="mixed"><input type="text"        id="symbol_left" style="width:50px;" value="" placeholder=""></td>
     	<td class="mixed"><input type="text"        id="symbol_right" style="width:50px;" value="" placeholder="€"></td>
     	<td class="mixed"><input type="text"        id="decimal_place" style="width:50px;" value="" placeholder="2"></td>
     	<td class="mixed"><input type="text"        id="kurs" style="width:100px;" value="" placeholder="32.00"></td>
     	<td class="mixed"></td>
     	<td class="mixed"><input type="checkbox"    id="status"  checked ></td>
        <td class="mixed"><input type="hidden"        id="date_modified" style="width:100px;" value="<?php echo date('Y-m-d H:i:s'); ?>"></td>
        <td>        
            <a href="javascript:" class="add">
                <img src="/<?php echo TMP_DIR; ?>backend/img/add.png" title="Добавить" width="16" height="16">
            </a>
        </td>              
    </tr>
    <td>
        <td colspan="6">&nbsp;</td>
    </td>

<?php foreach($List as $index => $ex){ ?>
    <tr id="<?php echo $ex[$key];?>">
        <td class="mixed"><?php echo $ex[$key];?></td>
        <td class="mixed"><input type="text" class="edit" id="title<?php echo $ex[$key];?>" style="width:150px;" value="<?php echo $ex['title']; ?>"></td>
		<td class="mixed"><input type="text" class="edit" id="code<?php echo $ex[$key];?>" style="width:100px;" value="<?php echo $ex['code']; ?>"></td>
		<td class="mixed"><input type="text" class="edit" id="symbol_left<?php echo $ex[$key];?>" style="width:50px;" value="<?php echo $ex['symbol_left']; ?>"></td>
		<td class="mixed"><input type="text" class="edit" id="symbol_right<?php echo $ex[$key];?>" style="width:50px;" value="<?php echo $ex['symbol_right']; ?>"></td>
		<td class="mixed"><input type="text" class="edit" id="decimal_place<?php echo $ex[$key];?>" style="width:50px;" value="<?php echo $ex['decimal_place']; ?>"></td>
		<td class="mixed"><input type="text" class="edit kurs" id="kurs<?php echo $ex[$key];?>" style="width:100px;" value="<?php echo number_format((1 / $ex['value']),2,".",""); ?>"></td>
		<td class="mixed"><input type="text" class="edit" id="value<?php echo $ex[$key];?>" style="width:150px;" value="<?php echo $ex['value']; ?>"></td>
		
		<td class="mixed"><input type="checkbox" class="edit" id="status<?php echo $ex[$key];?>"  <?php if($ex['status']) echo 'checked';?>></td>
		<td class="mixed"><?php echo $ex['date_modified']; ?></td>
        
        <td>        
            <a href="javascript:;" class="dell" data-id="<?php echo $ex[$key];?>">
                <img src="/<?php echo TMP_DIR; ?>backend/img/cancel.png" title="удалить" width="16" height="16">
            </a>
           </td>              
    </tr>
<?php } ?>

</table>
<input type="hidden" id="table" value="<?php echo $table; ?>">
<input type="hidden" id="mainkey" value="<?php echo $key; ?>">
<script>

    
</script>



</div>

</div>


<script>
	 //======================================================================   
    jQuery(document).on('change','.kurs', function(){
		
		var id = jQuery(this).parent('td').parent('tr').attr('id');
		
		var value = $(this).val();
		
		value = parseFloat(value);
		
		var vl = 1 / value;
		
		$('#value'+id).val(vl);
	});
	
    jQuery(document).on('change','.edit', function(){
        var id = jQuery(this).parent('td').parent('tr').attr('id');
        var enable_tmp = 0;
        if (jQuery('#status'+id).prop('checked')) {
             enable_tmp = 1;
        }

		var post = 'key=edit';
		post = post + '&id='+id;
		post = post + '&mainkey='+jQuery('#mainkey').val();
		post = post + '&table='+jQuery('#table').val();
		post = post + '&title='+jQuery('#title'+id).val();
		post = post + '&code='+jQuery('#code'+id).val();
		post = post + '&symbol_left='+jQuery('#symbol_left'+id).val();
		post = post + '&symbol_right='+jQuery('#symbol_right'+id).val();
		post = post + '&decimal_place='+jQuery('#decimal_place'+id).val();
		//post = post + '&kurs='+jQuery('#kurs'+id).val();
		post = post + '&value='+jQuery('#value'+id).val();
		post = post + '&date_modified='+jQuery('#date_modified'+id).val();
		post = post + '&status='+enable_tmp;
		 
        jQuery.ajax({
            type: "POST",
            url: "/<?php echo TMP_DIR; ?>backend/ajax/ajax_edit_universal.php",
            dataType: "text",
            data: post,
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
        var enable_tmp = 0;
         
        if (jQuery('#status').prop('checked')) {
             enable_tmp = 1;
        }
		
		var value = jQuery('#kurs').val();
		value = parseFloat(value);
		var value = 1 / value;
     
		var post = 'key=add';
		post = post + '&id='+id;
		post = post + '&mainkey='+jQuery('#mainkey').val();
		post = post + '&table='+jQuery('#table').val();
		post = post + '&title='+jQuery('#title').val();
		post = post + '&code='+jQuery('#code').val();
		post = post + '&symbol_left='+jQuery('#symbol_left').val();
		post = post + '&symbol_right='+jQuery('#symbol_right').val();
		post = post + '&decimal_place='+jQuery('#decimal_place').val();
		post = post + '&date_modified='+jQuery('#date_modified').val();
		//post = post + '&kurs='+jQuery('#kurs'+id).val();
		post = post + '&value='+value;
		post = post + '&status='+enable_tmp;
		
		
        if (jQuery('#title').val() != "") {
            jQuery.ajax({
                type: "POST",
                url: "/<?php echo TMP_DIR; ?>backend/ajax/ajax_edit_universal.php",
                dataType: "text",
                data: post,
                beforeSend: function(){
                },
                success: function(msg){
                    console.log( msg );
                    location.reload();
                }
            });
        }
        
    });
    
    jQuery(document).on('click','.dell', function(){
        var id = jQuery(this).data('id');
        
		var post = 'key=dell';
		post = post + '&id='+id;
		post = post + '&mainkey='+jQuery('#mainkey').val();
		post = post + '&table='+jQuery('#table').val();
		
        if (confirm('Вы действительно желаете удалить Валюту?')){
            jQuery.ajax({
                type: "POST",
                url: "/<?php echo TMP_DIR; ?>backend/ajax/ajax_edit_universal.php",
                dataType: "text",
                data: post,
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
