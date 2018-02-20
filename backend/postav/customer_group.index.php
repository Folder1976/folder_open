<!-- Sergey Kotlyarov 2016 folder.list@gmail.com -->
<?php
$file = explode('/', __FILE__);
if(strpos($_SERVER['PHP_SELF'], $file[count($file)-1]) !== false){
	header("Content-Type: text/html; charset=UTF-8");
	die('Прямой запуск запрещен!');
}

$key = 'customer_group_id';
$table = 'customer_group_description';
//$mainkey = 'shop_';

include "class/customer.class.php";
$Customer = new Customer();

$List = $Customer->getCustomerGroups();

?>
<br>
<!--script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/js/backend/ajax_edit_attributes.js"></script-->
<h1>Справочник : Группы клиентов</h1>
<div style="width: 90%">
<div class="table_body">

<table class="text">
    <tr>
        <th>id</th>
        <th>Название</th>
		<th>Активный</th>
      	<th>Сорт</th>
      	<th>Описание</th>
        <th>&nbsp;</th>
    </tr>
	
    <tr>
        <td class="mixed">новый</td>
		<td class="mixed"><input type="text"        id="name" style="width:200px;" value="" placeholder="Имя группы"></td>
		<td class="mixed"><input type="checkbox"    id="status"  checked ></td>
		<td class="mixed"><input type="text"        id="sort_order" style="width:50px;" value="" placeholder="0"></td>
        <td class="mixed"><input type="text"        id="description" style="width:500px;" value="" placeholder="Примечания для группы. Для памятки."></td>
		<td>        
            <a href="javascript:" class="add">
                <img src="/<?php echo TMP_DIR; ?>backend/img/add.png" title="Добавить" width="16" height="16">
            </a>
        </td>              
    </tr>
    <td>
        <td colspan="8">&nbsp;</td>
    </td>

<?php foreach($List as $index => $ex){ ?>
    <tr id="<?php echo $ex[$key];?>">
        <td class="mixed"><?php echo $ex[$key];?></td>
	    <td class="mixed"><input type="text" class="edit" id="name<?php echo $ex[$key];?>" style="width:200px;" value="<?php echo $ex['name']; ?>"></td>
    	<td class="mixed"><input type="checkbox" class="edit" id="status<?php echo $ex[$key];?>"  <?php if($ex['status']) echo 'checked';?>></td>
		<td class="mixed"><input type="text" class="edit" id="sort_order<?php echo $ex[$key];?>" style="width:50px;" value="<?php echo $ex['sort_order']; ?>"></td>
        <td class="mixed"><input type="text" class="edit" id="description<?php echo $ex[$key];?>" style="width:500px;" value="<?php echo $ex['description']; ?>"></td>
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
      
	    var enable_tmp = 0;
        if (jQuery('#status'+id).prop('checked')) {
             enable_tmp = 1;
        }

	  
		var post = 'key=edit_customer_group';
		post = post + '&id='+id;
		post = post + '&language_id=1';
		post = post + '&mainkey=<?php echo $key; ?>';
		post = post + '&table='+jQuery('#table').val();
		post = post + '&name='+jQuery('#name'+id).val();
		post = post + '&status='+enable_tmp;
		post = post + '&sort_order='+jQuery('#sort_order'+id).val();
		post = post + '&description='+jQuery('#description'+id).val();
		
		
		
        jQuery.ajax({
            type: "POST",
            url: "/<?php echo TMP_DIR; ?>backend/ajax/ajax_guideuniversal.php",
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
		
		var enable_tmp = 0;
        if (jQuery('#status').prop('checked')) {
             enable_tmp = 1;
        }

		var post = 'key=add_customer_group';
		post = post + '&id=0';
		post = post + '&language_id=1';
		post = post + '&mainkey=<?php echo $key; ?>';
		post = post + '&table='+jQuery('#table').val();
		post = post + '&name='+jQuery('#name').val();
		post = post + '&status='+enable_tmp;
		post = post + '&sort_order='+jQuery('#sort_order').val();
		post = post + '&description='+jQuery('#description').val();
		
        if (jQuery('#name').val() != "") {
            jQuery.ajax({
                type: "POST",
                url: "/<?php echo TMP_DIR; ?>backend/ajax/ajax_guideuniversal.php",
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
        var table = jQuery('#table').val();
        
		
		var post = 'key=dell_customer_group';
		post = post + '&id='+id;
		post = post + '&mainkey=<?php echo $key; ?>';
		post = post + '&table='+table;
		
        if (confirm('Вы действительно желаете удалить Группу?')){
            jQuery.ajax({
                type: "POST",
                url: "/<?php echo TMP_DIR; ?>backend/ajax/ajax_guideuniversal.php",
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
