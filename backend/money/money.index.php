<!-- Sergey Kotlyarov 2016 folder.list@gmail.com -->
<br><br><br>
<h1 style="margin-left: 30px;">Тут планируется интерфейс просмотра списка оплат. А так же долгов и проплат.</h1>
<?php

include 'remont.php';

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

if(isset($_GET['status']) AND (int)$_GET['status'] > 0){
	$filter_data['status'] = (int)$_GET['status'];
}

$List = $Operation->getKassa($filter_data);


include "class/customer.class.php";
$Customer = new Customer();
$postavs = $Customer->getCustomers(4);
$moneys = $Operation->getShopsMonej();

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
<h1>Справочник : Кассовые чеки</h1>
<div style="width: 90%">
<div class="table_body">
	
	<!--h3>Касса по магазинам</h3-->
<?php foreach($moneys as $shop_id => $summ){ ?>
			
		<div class="shop_money"><?php echo $shops[$shop_id]['name'].' : <b>'.$summ.'</b> грн' ?></div>
			
<?php } ?>

<div style="clear: both;"></div>
<!--h3>История операций</h3-->
<table class="text">
    <tr>
		<th>Дата создания</th>
        <th>Номер документа</th>
        <th>Номер чека</th>
        <th>Магазин</th>
        <th>Сумма</th>
        <th>Оплата</th>
        <th>Сдача</th>
        <th>Оператор</th>
        <th>Сотрудник</th>
    </tr>
	<form method="GET">
		<input type="hidden" name="route" value="<?php echo $_GET['route']; ?>">
		<tr>
			<th><input type="text" class="operation_sort" name="date" onChange="this.form.submit();"
					value="<?php echo isset($_GET['date']) ? $_GET['date'] : '' ;?>"></th>
			
			<th><input type="text" class="operation_sort" name="operation_id" onChange="this.form.submit();"
					value="<?php echo isset($_GET['operation_id']) ? $_GET['operation_id'] : '' ;?>"></th>
			
			<th><input type="text" class="operation_sort" name="sub_operation_id" onChange="this.form.submit();"
					value="<?php echo isset($_GET['sub_operation_id']) ? $_GET['sub_operation_id'] : '' ;?>"></th>
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
			<th>* * *</th>
			<th>* * *</th>
			<th>
				<select class="operation_sort" name="user_id" style="width:100%;" onChange="this.form.submit();">
					<option value="">* * *</option>
					<?php foreach($users as $index => $value){?>
						<?php if(isset($_GET['shop_id']) AND $index == (int)$_GET['user_id']){ ?>
							<option value="<?php echo $index; ?>" selected><?php echo $value['firstname'].' '.$value['lastname']; ?></option>
						<?php }else{ ?>
							<option value="<?php echo $index; ?>"><?php echo $value['firstname'].' '.$value['lastname']; ?></option>
						<?php } ?>
					<?php } ?>
				</select></th>
			
			<th>
				<select class="operation_sort" name="status" style="width:100%;" onChange="this.form.submit();">
					<option value="">* * *</option>
					<option value="0">Продажи</option>
					<?php foreach($users as $index => $value){?>
						<?php if(isset($_GET['status']) AND $index == (int)$_GET['status']){ ?>
							<option value="<?php echo $index; ?>" selected><?php echo $value['firstname'].' '.$value['lastname']; ?></option>
						<?php }else{ ?>
							<option value="<?php echo $index; ?>"><?php echo $value['firstname'].' '.$value['lastname']; ?></option>
						<?php } ?>
					<?php } ?>
				</select></th>
		</tr>
	</form>

<?php foreach($List as $index => $rows){
		
	foreach($rows as $key => $row){ ?>
  
  <?php //echo '<pre>'; print_r(var_dump( $row  )); ?>
  
  
	<tr class="link_table_row" id="<?php echo $index; ?>"	>
        <td class="mixed"><?php echo date('d-m-Y H:i:s',strtotime($row['date'])); ?></td>
        <td class="left"><?php echo $row['operation_id']; ?></td>
        <td class="left"><?php echo $row['sub_operation_id']; ?></td>
		<?php if($row['from_warehouse_id'] < 0){ ?>
			<td class="left">Возврат -> <?php echo $shops[$warehouse_to_shop[$row['to_warehouse_id']]]['name']; ?></td>
		<?php }else{ ?>
			<td class="left"><?php echo $shops[$warehouse_to_shop[$row['from_warehouse_id']]]['name']; ?></td>
		<?php } ?>
        
		
		<?php if($row['status'] > 0){ ?>
			<td class="right"><b>-<?php echo number_format($row['oplat_summ'], 2, '.', ''); ?></b></td>
		<?php }else{ ?>
			<td class="right"><?php echo number_format($row['sub_operation_summ'], 2, '.', ''); ?></td>
		<?php } ?>
		<td class="right"><?php echo number_format($row['oplat_money'], 2, '.', ''); ?></td>
		<td class="right"><?php echo number_format($row['zdacha'], 2, '.', ''); ?></td>
	    <td class="mixed"><?php echo $UsersList[$row['user_id']]['lastname'] . ' ' . $UsersList[$row['user_id']]['firstname']; ?></td>
	    <td class="mixed"><?php
			if($row['status'] > 0){
				echo $UsersList[$row['status']]['lastname'] . ' ' . $UsersList[$row['status']]['firstname'];
			}
			?></td>
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
	$(document).on('click', '.link_table_row', function(){
		
		var tmp = $(this).attr('id');
		var tmp2 = tmp.split('_');
		var operation_id = tmp2[0];
		var sub_operation_id = tmp2[1];
		
					
		window.open('/backend/index.php?route=shop_torg/shop_torg.index.php&operation_id='+operation_id+'&sub_operation_id='+sub_operation_id,'_blank');
			
		
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