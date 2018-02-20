<!-- Sergey Kotlyarov 2016 folder.list@gmail.com -->
<?php
$file = explode('/', __FILE__);
if(strpos($_SERVER['PHP_SELF'], $file[count($file)-1]) !== false){
	header("Content-Type: text/html; charset=UTF-8");
	die('Прямой запуск запрещен!');
}

$key = 'customer_id';
$table = 'customer';
//$mainkey = 'shop_';

include "class/customer.class.php";
$Customer = new Customer();

$List = $Customer->getCustomers(0);
$GroupList = $Customer->getCustomerGroups();

?>
<br>
<!--script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/js/backend/ajax_edit_attributes.js"></script-->
<h1>Справочник : Клиенты</h1>
<div style="width: 90%">
<div class="table_body">

<table class="text">
    <tr>
        <th>id</th>
        <th>Группа</th>
		<th>Имя</th>
        <th>Фамилия</th>
        <th>Активный</th>
        <th>email</th>
        <th>Телефон</th>
        <th>Адрес</th>
        <th>&nbsp;</th>
    </tr>

    <tr>
        <td class="mixed">новый</td>
		 <td class="mixed"><select id="customer_group_id" style="width:150px;">
                <?php foreach($GroupList as $index => $value){?>
                    <option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
                <?php } ?>
            
                </select>
        </td>
	 	<td class="mixed"><input type="text"        id="firstname" style="width:150px;" value="" placeholder=""></td>
		<td class="mixed"><input type="text"        id="lastname" style="width:150px;" value="" placeholder=""></td>
		<td class="mixed"><input type="checkbox"    id="status"  checked ></td>
        <td class="mixed"><input type="text"        id="email" style="width:150px;" value=""></td>
        <td class="mixed"><input type="text"        id="telephone" style="width:100px;" value="" placeholder=""></td>
        <td class="mixed"><input type="text"        id="address" style="width:400px;" value="" placeholder=""></td>
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
		      <td class="mixed"><select class="edit" id="customer_group_id<?php echo $ex[$key];?>" style="width:150px;">
                <?php foreach($GroupList as $index => $value){?>
                    <?php if($index == (int)$ex['customer_group_id']){ ?>
                        <option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
                    <?php }else{ ?>
                        <option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
                    <?php } ?>
                <?php } ?>
                </select>
        </td>
    	<td class="mixed"><input type="text" class="edit" id="firstname<?php echo $ex[$key];?>" style="width:150px;" value="<?php echo $ex['firstname']; ?>"></td>
    	<td class="mixed"><input type="text" class="edit" id="lastname<?php echo $ex[$key];?>" style="width:150px;" value="<?php echo $ex['lastname']; ?>"></td>
    	<td class="mixed"><input type="checkbox" class="edit" id="status<?php echo $ex[$key];?>"  <?php if($ex['status']) echo 'checked';?>></td>
        <td class="mixed"><input type="text" class="edit" id="email<?php echo $ex[$key];?>" style="width:150px;" value="<?php echo $ex['email']; ?>"></td>
        <td class="mixed"><input type="text" class="edit" id="telephone<?php echo $ex[$key];?>" style="width:100px;" value="<?php echo $ex['telephone']; ?>"></td>
        <td class="mixed"><input type="text" class="edit" id="address<?php echo $ex[$key];?>" style="width:400px;" value="<?php echo $ex['address']; ?>"></td>
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

		var post = 'key=edit';
		post = post + '&id='+id;
		post = post + '&mainkey=<?php echo $key; ?>';
		post = post + '&table='+jQuery('#table').val();
		post = post + '&name='+jQuery('#firstname'+id).val()+' '+jQuery('#lastname'+id).val();
		post = post + '&firstname='+jQuery('#firstname'+id).val();
		post = post + '&lastname='+jQuery('#lastname'+id).val();
		post = post + '&customer_group_id='+jQuery('#customer_group_id'+id).val();
		post = post + '&address='+jQuery('#address'+id).val();
		post = post + '&email='+jQuery('#email'+id).val();
		post = post + '&telephone='+jQuery('#telephone'+id).val();
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
		
		//debugger;
		
		//console.log('11 '+name);   
        var enable_tmp = 0;
        if (jQuery('#status').prop('checked')) {
             enable_tmp = 1;
        }
     
		var post = 'key=add';
		post = post + '&id=0';
		post = post + '&customer_group_id=4';
		post = post + '&mainkey=<?php echo $key; ?>';
		post = post + '&table='+jQuery('#table').val();
		post = post + '&name='+jQuery('#firstname').val()+' '+jQuery('#lastname').val();
		post = post + '&firstname='+jQuery('#firstname').val();
		post = post + '&lastname='+jQuery('#lastname').val();
		post = post + '&customer_group_id='+jQuery('#customer_group_id').val();
		post = post + '&address='+jQuery('#address').val();
		post = post + '&email='+jQuery('#email').val();
		post = post + '&telephone='+jQuery('#telephone').val();
		post = post + '&status='+enable_tmp;
		
        if (jQuery('#name').val() != "") {
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
        var table = jQuery('#table').val();
        
		
		var post = 'key=dell';
		post = post + '&id='+id;
		post = post + '&mainkey=<?php echo $key; ?>';
		post = post + '&table='+table;
		
        if (confirm('Вы действительно желаете удалить Клиента?')){
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
