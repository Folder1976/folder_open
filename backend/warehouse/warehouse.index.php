<!-- Sergey Kotlyarov 2016 folder.list@gmail.com -->
<?php
$file = explode('/', __FILE__);
if(strpos($_SERVER['PHP_SELF'], $file[count($file)-1]) !== false){
	header("Content-Type: text/html; charset=UTF-8");
	die('Прямой запуск запрещен!');
}

$key = 'warehouse_id';
$table = 'warehouse';
//$mainkey = 'shop_';

//include "class/warehouse.class.php";
//$Warehouse = new Warehouse();

//include "class/shops.class.php";
//$Shops = new Shops();

$List = $Warehouse->getWarehouses();
$ShopList = $Shops->getShops();

?>
<br>
<!--script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/js/backend/ajax_edit_attributes.js"></script-->
<h1>Справочник : Склады</h1>
<div style="width: 90%">
<div class="table_body">

<table class="text">
    <tr>
        <th>id</th>
        <th>Магазин где находится</th>
		<th>Название</th>
        <th>Активный</th>
        <th>Главный<br>только один на магазин</th>
        <th>Сорт</th>
        <th>Описание</th>
        <th>&nbsp;</th>
    </tr>

    <tr>
        <td class="mixed">новый</td>
		 <td class="mixed"><select id="shop_id" style="width:300px;">
                <?php foreach($ShopList as $index => $value){?>
                    <option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
                <?php } ?>
            
                </select>
        </td>
	    <td class="mixed"><input type="text"        id="name" style="width:300px;" value="" placeholder="Название магазина"></td>
		<td class="mixed"><input type="checkbox"    id="enable"  checked ></td>
		<td class="mixed"><input type="checkbox"    id="is_main"></td>
        <td class="mixed"><input type="text"        id="sort_order" style="width:100px;" value=""></td>
        <td class="mixed"><input type="text"        id="description" style="width:400px;" value="" placeholder="Описание магазина"></td>
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
		      <td class="mixed"><select class="edit" id="shop_id<?php echo $ex[$key];?>" style="width:300px;">
                <?php foreach($ShopList as $index => $value){?>
                    <?php if($index == (int)$ex['shop_id']){ ?>
                        <option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
                    <?php }else{ ?>
                        <option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
                    <?php } ?>
                <?php } ?>
                </select>
        </td>
        <td class="mixed"><input type="text" class="edit" id="name<?php echo $ex[$key];?>" style="width:300px;" value="<?php echo $ex['name']; ?>"></td>
    	<td class="mixed"><input type="checkbox" class="edit" id="enable<?php echo $ex[$key];?>"  <?php if($ex['enable']) echo 'checked';?>></td>
		<td class="mixed"><input type="checkbox" class="edit" id="is_main<?php echo $ex[$key];?>"  <?php if($ex['is_main']) echo 'checked';?>></td>
        <td class="mixed"><input type="text" class="edit" id="sort_order<?php echo $ex[$key];?>" style="width:100px;" value="<?php echo $ex['sort_order']; ?>"></td>
        <td class="mixed"><input type="text" class="edit" id="description<?php echo $ex[$key];?>" style="width:400px;" value="<?php echo $ex['description']; ?>"></td>
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
        var shop_id = jQuery('#shop_id'+id).val();
        var enable_tmp = 0;
        var is_main_tmp = 0;
        var sort = jQuery('#sort_order'+id).val();
        var description = jQuery('#description'+id).val();
        var table = jQuery('#table').val();
        
        if (jQuery('#enable'+id).prop('checked')) {
             enable_tmp = 1;
        }
		if (jQuery('#is_main'+id).prop('checked')) {
             is_main_tmp = 1;
        }

		var post = 'key=edit';
		post = post + '&id='+id;
		post = post + '&mainkey=<?php echo $key; ?>';
		post = post + '&description='+description;
		post = post + '&shop_id='+shop_id;
		post = post + '&table='+table;
		post = post + '&name='+name;
		post = post + '&enable='+enable_tmp;
		post = post + '&is_main='+is_main_tmp;
		post = post + '&sort_order='+sort;
		post = post + '&id='+id;
        
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
        var name = jQuery('#name').val();
        var enable_tmp = 0;
        var is_main_tmp = 0;
        var sort = jQuery('#sort_order').val();
        var shop_id = jQuery('#shop_id').val();
        var description = jQuery('#description').val();
        var table = jQuery('#table').val();
        
        if (jQuery('#enable').prop('checked')) {
             enable_tmp = 1;
        }
		if (jQuery('#is_main').prop('checked')) {
             is_main_tmp = 1;
        }
     
		var post = 'key=add';
		post = post + '&id='+id;
		post = post + '&description='+description;
		post = post + '&name='+name;
		post = post + '&table='+table;
		post = post + '&shop_id='+shop_id;
		post = post + '&enable='+enable_tmp;
		post = post + '&is_main='+is_main_tmp;
		post = post + '&sort_order='+sort;
		post = post + '&id='+id;
		
        if (name != "") {
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
		
        if (confirm('Вы действительно желаете удалить Склад?')){
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
