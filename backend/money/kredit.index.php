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

if(isset($_GET['sub_operation_id']) AND (int)$_GET['sub_operation_id'] > 0){
	$filter_data['sub_operation_id'] = (int)$_GET['sub_operation_id'];
}

if(isset($_GET['date']) AND $_GET['date'] != ''){
	$filter_data['date'] = $_GET['date'];
}

if(isset($_GET['shop_id']) AND (int)$_GET['shop_id'] > 0){
	$filter_data['shop_id'] = (int)$_GET['shop_id'];
}

if(isset($_GET['page']) AND (int)$_GET['page'] > 0){
	$filter_data['page'] = (int)$_GET['page'];
}

if(isset($_GET['user_id']) AND $_GET['user_id'] != ''){
	$filter_data['user_id'] = $_GET['user_id'];
}

$customer_id = 0;
if(isset($_GET['customer_id']) AND $_GET['customer_id'] != ''){
	$customer_id = $filter_data['customer_id'] = $_GET['customer_id'];
}

if(isset($_GET['status']) AND (int)$_GET['status'] > 0){
	$filter_data['status'] = (int)$_GET['status'];
}

$kredit_sums = $Operation->getKreditSummOnCustomers($customer_id);
if(isset($_GET['dolg_summ']) AND (int)$_GET['dolg_summ'] > 0){
	
	foreach($kredit_sums as $index => $row){
		
		if((int)$row < (int)$_GET['dolg_summ']){
			unset($kredit_sums[$index]);
			$filter_data['not_customer_id'][] = (int)$index;
		}
			
	}

}


$List = $Operation->getKredit($filter_data);


include "class/customer.class.php";
$Customer = new Customer();
$customers = $Customer->getCustomers();

foreach($customers as $index => $row){
	if(!isset($kredit_sums[$index])){
		unset($customers[$index]);
	}
}

//$moneys = $Operation->getShopsMonej();

$warehouse_to_shop[0] = 0;
$shops[0] = 0;

?>
<style>
	.dell{
		z-index:9999;
	}
	.shop_money{
		float: left;
		margin: 10px;
		
	}
</style>	
<br>
<h1>Справочник : Кредиты</h1>
<div style="width: 90%">
<div class="table_body">
	

<div style="clear: both;"></div>
<!--h3>История операций</h3-->
<table class="text">
    <tr>
		<th>Дата создания
		<a href="/backend/index.php?route=money_in/money_in.index.php" target="_blank">
				<img src="/backend/img/pay.png" style="width: 80px;position: absolute;margin-top: -50px;margin-left: 40px;" title="Добавить оплату" alt="Добавиьт оплату">
			</a>
		
		</th>
        <th>Номер документа</th>
        <th>Покупатель</th>
        <th>Магазин</th>
        <th>Сумма</th>
		<th>Долг (сумм > )</th>
        <th>Коментарий</th>
     </tr>
	<form method="GET">
		<input type="hidden" name="route" value="<?php echo $_GET['route']; ?>">
		<tr>
			<th><input type="text" class="operation_sort" name="date" onChange="this.form.submit();"
					value="<?php echo isset($_GET['date']) ? $_GET['date'] : '' ;?>"></th>
			
			<th><input type="text" class="operation_sort" name="operation_id" onChange="this.form.submit();"
					value="<?php echo isset($_GET['operation_id']) ? $_GET['operation_id'] : '' ;?>"></th>
			
			<th>
				<select class="operation_sort" name="customer_id" style="width:100%;" onChange="this.form.submit();">
					<option value="">* * *</option>
					<?php foreach($customers as $index => $value){?>
						<?php if(isset($_GET['customer_id']) AND $index == (int)$_GET['customer_id']){ ?>
							<option value="<?php echo $index; ?>" selected><?php echo $value['firstname'].' '.$value['lastname']; ?></option>
						<?php }else{ ?>
							<option value="<?php echo $index; ?>"><?php echo $value['firstname'].' '.$value['lastname']; ?></option>
						<?php } ?>
					<?php } ?>
				</select></th>
			
			<th>
				<select class="operation_sort" name="shop_id" style="width:100%;" onChange="this.form.submit();">
					<option value="">* * *</option>
					<?php foreach($shops as $index => $value){?>
						<?php if(isset($_GET['shop_id']) AND $index == (int)$_GET['shop_id']){ ?>
							<option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
						<?php }else{ ?>
							<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
						<?php } ?>
					<?php } ?>
				</select></th>
			<th>* * *</th>
			<th><input type="text" class="operation_sort" name="dolg_summ" onChange="this.form.submit();"
					value="<?php echo isset($_GET['dolg_summ']) ? $_GET['dolg_summ'] : '' ;?>"
					placeholder="Долг больше этой суммы"></th>
		
			<th>* * *</th>
	</form>

<?php foreach($List as $index => $rows){
		
	foreach($rows as $key => $row){ ?>
  
  <?php //echo '<pre>'; print_r(var_dump( $row  )); ?>
  
  
	<tr class="link_table_row" id="<?php echo $index; ?>" data-customer_id="<?php echo $row['customer_id']; ?>">
        <td class="mixed"><?php echo date('d-m-Y H:i:s',strtotime($row['date'])); ?></td>
        <td class="left"><?php echo $row['operation_id']; ?></td>
        <td class="left"><?php echo $customers[$row['sub_operation_id']]['firstname'].' '.$customers[$row['sub_operation_id']]['lastname']; ?></td>
		<?php if($row['from_warehouse_id'] < 0){ ?>
			<td class="left">Возврат -> <?php echo $shops[$warehouse_to_shop[$row['to_warehouse_id']]]['name']; ?></td>
		<?php }else{ ?>
			<td class="left"><?php echo $shops[$warehouse_to_shop[$row['from_warehouse_id']]]['name']; ?></td>
		<?php } ?>
        
		
		<td class="right"><b><?php echo number_format($row['oplat_summ'], 2, '.', ''); ?></b></td>
		<td class="right"><b style="color:red;"><?php echo number_format($kredit_sums[$row['customer_id']], 2, '.', ''); ?></b></td>
		<td class="right"><b><?php echo $row['comment']; ?></b></td>

	</tr>
	
	<?php } ?>	
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



<script>
	$(document).on('click', '.link_table_row', function(e){
		
		//console.log(e.target.nodeName);
		
		if(e.target.nodeName != 'INPUT'){
			var tmp = $(this).attr('id');
			var customer_id = $(this).data('customer_id');
			var tmp2 = tmp.split('_');
			var operation_id = tmp2[0];
			var sub_operation_id = tmp2[1];
		
					
			window.open('/backend/index.php?route=operation/operation.index.php&customer_id='+customer_id,'_blank');
		}else{
			
		}
		
	});
	
	$(document).on('change', '.status', function(){
		var status = 0;
			
		if($(this).prop('checked')){
			status = 1;
		}
		
		var post = 'key=set_beznal_status';
		post = post + '&operation_id='+$(this).data('operation_id');
		post = post + '&sub_operation_id='+$(this).data('sub_operation_id');
		post = post + '&status='+status;
	
		//console.log(post);
	
		jQuery.ajax({
			type: "POST",
			url: "/<?php echo TMP_DIR; ?>backend/operation/ajax_edit_operation.php",
			dataType: "text",
			data: post,
			beforeSend: function(){
			},
			success: function(msg){
				console.log( msg );
			}
		});	
	});
	
	
	$(document).ready(function(){
		$(".datepicker").datepicker();
	});
	
	/*
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
	*/
</script>