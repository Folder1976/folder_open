<!-- Sergey Kotlyarov 2016 folder.list@gmail.com -->
<?php
$file = explode('/', __FILE__);
if(strpos($_SERVER['PHP_SELF'], $file[count($file)-1]) !== false){
	header("Content-Type: text/html; charset=UTF-8");
	die('Прямой запуск запрещен!');
}

$key = 'operation_id';

include_once "class/operation.class.php";
$Operation = new Operation();

$UsersList = $users;
$TypeList = $Operation->getTypes();

$filter_data = array();

if(isset($_GET['operation_id']) AND (int)$_GET['operation_id'] > 0){
	$filter_data['operation_id'] = (int)$_GET['operation_id'];
}

if(isset($_GET['date']) AND $_GET['date'] != ''){
	$filter_data['date'] = $_GET['date'];
}

if(isset($_GET['type_id']) AND (int)$_GET['type_id'] > 0){
	$filter_data['type_id'] = (int)$_GET['type_id'];
}

if(isset($_GET['comment']) AND $_GET['comment'] != ''){
	$filter_data['comment'] = $_GET['comment'];
}

if(isset($_GET['from_warehouse_id']) AND (int)$_GET['from_warehouse_id'] > 0){
	$filter_data['from_warehouse_id'] = (int)$_GET['from_warehouse_id'];
}

if(isset($_GET['to_warehouse_id']) AND (int)$_GET['to_warehouse_id'] > 0){
	$filter_data['to_warehouse_id'] = (int)$_GET['to_warehouse_id'];
}

if(isset($_GET['customer_id']) AND (int)$_GET['customer_id'] > 0){
	$filter_data['customer_id'] = (int)$_GET['customer_id'];
}

if(isset($_GET['user_id']) AND (int)$_GET['user_id'] > 0){
	$filter_data['user_id'] = (int)$_GET['user_id'];
}

if(isset($_GET['edit_date']) AND $_GET['edit_date'] != ''){
	$filter_data['edit_date'] = $_GET['edit_date'];
}

$filter_data['no_type_id'] = array(8);

$List = $Operation->getOperations($filter_data);

include "class/customer.class.php";
$Customer = new Customer();
$postavs = $Customer->getCustomers(4);

?>
<br>
<h1>Справочник : Операций</h1>
<div style="width: 90%">
<div class="table_body">

<table class="text">
    <tr>
        <th>Номер документа</th>
        <th>Дата создания</th>
        <th>Тип</th>
        <th>Примечение</th>
        <th rowspan="2">Сумма</th>
        <th>От куда </th>
        <th>Куда</th>
        <th>Поставщик</th>
        <th>Пользователь</th>
        <th>Дата изменения</th>
		<th rowspan="2"></th>
    </tr>
	<form method="GET">
		<input type="hidden" name="route" value="<?php echo $_GET['route']; ?>">
		<tr>
			<th><input type="text" class="operation_sort" name="operation_id" onChange="this.form.submit();"
					value="<?php echo isset($_GET['operation_id']) ? $_GET['operation_id'] : '' ;?>"></th>
			<th><input type="text" class="operation_sort datepicker" name="date" onChange="this.form.submit();"
					value="<?php echo isset($_GET['date']) ? $_GET['date'] : '' ;?>"></th>
			<th>
				<select class="header_edit" name="type_id" style="width:100%;" onChange="this.form.submit();">
					<option value="">* * *</option>
					<?php foreach($TypeList as $index => $value){?>
						<?php if(isset($_GET['type_id']) AND $index == (int)$_GET['type_id']){ ?>
							<option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
						<?php }else{ ?>
							<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
						<?php } ?>
					<?php } ?>
				</select></th>
			<th><input type="text" class="operation_sort" name="comment" onChange="this.form.submit();"
					value="<?php echo isset($_GET['comment']) ? $_GET['comment'] : '' ;?>"></th>
			<th>
				<select class="header_edit" name="from_warehouse_id" style="width:100%;" onChange="this.form.submit();">
					<option value="-1000">* * *</option>
					<?php foreach($warehouses as $index => $value){?>
						<?php if(isset($_GET['from_warehouse_id']) AND $index == (int)$_GET['from_warehouse_id']){ ?>
							<option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
						<?php }else{ ?>
							<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
			</th>
			<th>
				<select class="header_edit" name="to_warehouse_id" style="width:100%;" onChange="this.form.submit();">
					<option value="-1000">* * *</option>
					<?php foreach($warehouses as $index => $value){?>
						<?php if(isset($_GET['to_warehouse_id']) AND $index == (int)$_GET['to_warehouse_id']){ ?>
							<option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
						<?php }else{ ?>
							<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
			</th>
			<th>
				<select class="header_edit" name="customer_id" style="width:100%;" onChange="this.form.submit();">
					<option value="">* * *</option>
					<?php foreach($postavs as $index => $value){?>
						<?php if(isset($_GET['customer_id']) AND $index == (int)$_GET['customer_id']){ ?>
							<option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
						<?php }else{ ?>
							<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
			</th>
			<th>
				<select class="header_edit" name="user_id" style="width:100%;" onChange="this.form.submit();">
					<option value="">* * *</option>
					<?php foreach($UsersList as $index => $value){?>
						<?php if(isset($_GET['user_id']) AND $index == (int)$_GET['user_id']){ ?>
							<option value="<?php echo $index; ?>" selected><?php echo $value['lastname'].' '.$value['firstname']; ?></option>
						<?php }else{ ?>
							<option value="<?php echo $index; ?>"><?php echo $value['lastname'].' '.$value['firstname']; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
			</th>
			<th><input type="text" class="operation_sort datepicker" name="edit_date" onChange="this.form.submit();"
					value="<?php echo isset($_GET['edit_date']) ? $_GET['edit_date'] : '' ;?>"></th>
			
		</tr>
	</form>

<?php foreach($List as $index => $ex){ ?>
  
	<tr class="link_table_row" id="<?php echo $ex[$key];?>" data-type_id="<?php echo $ex['type_id']; ?>">
        <td class="mixed"><?php echo $ex[$key];?></td>
        <td class="mixed"><?php echo date('d-m-Y H:i:s',strtotime($ex['date'])); ?></td>
        <td class="left" style="font-weight: bold; background-color: <?php echo $TypeList[$ex['type_id']]['color']; ?>;"><?php echo $TypeList[$ex['type_id']]['name']; ?></td>
        <td class="left"><?php echo $ex['comment']; ?></td>
        <td class="right"><?php echo number_format($ex['summ'], 2, '.', ''); ?></td>
		<td class="right"><?php echo $warehouses[$ex['from_warehouse_id']]['name']; ?></td>
        <td class="left"><?php echo $warehouses[$ex['to_warehouse_id']]['name']; ?></td>
        <td class="mixed"><?php echo isset($postavs[$ex['customer_id']]) ? $postavs[$ex['customer_id']]['name'] : ''; ?></td>
        <td class="mixed"><?php echo ($ex['user_id']) ? $UsersList[$ex['user_id']]['lastname'] . ' ' . $UsersList[$ex['user_id']]['firstname'] : 'нет'; ?></td>
        <td class="mixed"><?php echo date('d-m-Y H:i:s',strtotime($ex['edit_date'])); ?></td>
		<td>
			<?php if($ex[$key] > 0){ ?>
            <a href="javascript:;" class="dell" id="dell" data-id="<?php echo $ex[$key];?>">
                <img src="/backend/img/cancel.png" id="dell" title="удалить" width="16" height="16">
            </a>
			<?php } ?>
        </td>
    </tr>
	
<?php } ?>

</table>

</div>

</div>

<!--script type="text/javascript" src="/backend/js/ui/jquery.ui.datepicker.min.js"></script>
<script type="text/javascript" src="/backend/js/ui/jquery.ui.datepicker-ru.js"></script-->

<!--script type="text/javascript" src="/backend/js/jquery/jquery-1.8.2.min.js"></script-->
<script type="text/javascript" src="/backend/js/jquery/ui/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="/backend/js/jquery/ui/jquery.ui.widget.min.js"></script>
<script type="text/javascript" src="/backend/js/jquery/ui/jquery.ui.datepicker.min.js"></script>
<script type="text/javascript" src="/backend/js/jquery/ui/jquery.ui.datepicker-ru.js"></script>
<style>
	.dell{
		z-index:9999;
	}
</style>	

<script>
	$(document).on('click', '.link_table_row', function(){
		
		var type_id = $(this).data('type_id');
		var operation_id = $(this).attr('id');
		
		//debugger;
		
		var id = event.target.id;
		
		if(id != 'dell'){
		
			if(type_id == 1){
				
				window.open('/backend/index.php?route=operation_prihod/prihod.index.php&operation_id='+operation_id,'_blank');
			
			}else if(type_id == 3){
				
				window.open('/backend/index.php?route=operation_shop/out.shop.index.php&operation_id='+operation_id,'_blank');
			
			}else if(type_id == 7){
				
				window.open('/backend/index.php?route=operation_shop/in.shop.index.php&operation_id='+operation_id,'_blank');
			
			}else if(type_id == 6){
				
				window.open('/backend/index.php?route=operation_warehouse/warehouse.index.php&operation_id='+operation_id,'_blank');
			
			}else if(type_id == 4){
				
				window.open('/backend/index.php?route=operation_dolg/dolg.index.php&operation_id='+operation_id,'_blank');
			
			}else if(type_id == 5){
				
				window.open('/backend/index.php?route=operation_rezerv/rezerv.index.php&operation_id='+operation_id,'_blank');
			
			}else if(type_id == 9){
				
				window.open('/backend/index.php?route=shop_torg/return.index.php&operation_id='+operation_id,'_blank');
			
			}else if(type_id == 11){
				
				window.open('/backend/index.php?route=operation_kredit/kredit.index.php&operation_id='+operation_id,'_blank');
			
			}
		}
		
	});
	
	$(document).ready(function(){
		$(".datepicker").datepicker();
	});
	
	jQuery(document).on('click','.dell', function(){
        var id = jQuery(this).data('id');
        
		var post = 'key=dell_operation';
		post = post + '&id='+id;
		
        if (confirm('Вы действительно желаете удалить Операцию?')){
            jQuery.ajax({
                type: "POST",
                url: "/<?php echo TMP_DIR; ?>backend/operation/ajax_edit_operation.php",
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

</script>