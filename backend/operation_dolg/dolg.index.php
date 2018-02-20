<!-- Sergey Kotlyarov 2016 folder.list@gmail.com -->
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/libs/category_tree/type-for-get.css">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/product.css">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/operation.css">
<script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/libs/category_tree/script-for-get.js"></script>
<script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/product/category_tree.js"></script>


<?php
$file = explode('/', __FILE__);
if(strpos($_SERVER['PHP_SELF'], $file[count($file)-1]) !== false){
	header("Content-Type: text/html; charset=UTF-8");
	die('Прямой запуск запрещен!');
}

$key = 'operation_id';
$table = 'operation_products';
$type_id = 4;

global $warehouses;

include "class/customer.class.php";
$Customer = new Customer();
$postav_list = $postavs = $Customer->getCustomers(4);
$customers = $Customer->getCustomers();

include "class/brand.class.php";
$Brand = new Brand();
$brand_list = $Brand->getBrands();

$size_group_list = $size_groups = $Size->getSizeGroups();

$masters = $Master->getMasters();

$tmp = $Size->getSizes();
$sizes_on_groups = array();
foreach($tmp as $row){
	$sizes_on_groups[$row['group_id']][]= $row;
}

	include "class/attributes.class.php";
	$Attributes = new Attributes();
	$attributes_group_list = $Attributes->getAttributeGroups();

$types = $Operation->getTypes();

?>
<?php include 'operation/main_setup_menu.php'; ?>
<br>

<style>

	h1{
		background-color: <?php echo $types[$type_id]['color']; ?>;
		padding: 10px;
		margin-bottom: 0px;
	}
	.size-box input{
		max-width: 30px;
		text-align: center;
		font-weight: bold;
		font-size: 13px;
		color: red;
		color: #B56E99;
		background-color: <?php echo $types[$type_id]['color']; ?>;
	}
</style>

	
<h1>Операция : <b>На примерку / Долг</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php if(!isset($_GET['operation_id'])){ ?>
	<a href="javascript:;" id="add_new_operation" class="add_new_operation">Создать новую операцию</a>
	<span class="msg_note">! Создайте операцию чтоб начать добавлять товары</span>
<?php }else{ ?>
	
	<a href="javascript:;" id="create_new_operation" class="add_new_operation">Сохранить и начать новую операцию</a>
	<a href="/backend/operation/print.operation.php?operation_id=<?php echo $_GET['operation_id'];?>" id="print_operation" class="print_operation">
		<img class="print_icon" src="/backend/img/Print_128.png" title="Печать документа" alt="print">
	</a>
<?php } ?>

</h1>

<div style="width: 90%">
<div class="table_body">
	
<?php
//=====================================================================================================================
//=====================================================================================================================
//=====================================================================================================================
//=====================================================================================================================
	if(isset($_GET['operation_id'])){
		$operation_header = $Operation->getOperation($_GET['operation_id']);
		$operation_products = $Operation->getOperationProducts($_GET['operation_id']);
		//$operation_postav = $Operation->getOperationPostav($_GET['operation_id']);
		
		$operation_products_grp = array();
		
		if($operation_products){
			foreach($operation_products as $rows){
				foreach($rows as $row){
				
				// Групируем товары по Ид и закупу. Цена закупа может разниться от размера
				$operation_products_grp[$row['product_id'].'_'.$row['operation_zakup'].'_'.$row['master_id']]['product'] = $row;
				$operation_products_grp[$row['product_id'].'_'.$row['operation_zakup'].'_'.$row['master_id']]['sizes'][$row['size_id']] = $row;
				}
			}
		}
	
	}
	//echo '<pre>'; print_r(var_dump( $_SESSION  ));
	
	include 'product/category_tree.php';
?>

	<input type="hidden" id="type_id" value="<?php echo $type_id;?>">
	<input type="hidden" id="user_id" value="<?php echo $_SESSION['default']['user_id'];?>">
				
	<table class="text">
    <tr>
		<td class="right">
			<b>Номер операции</b>
		</td>
		<td class="left">
				   
			<input type="text"
				   class="header_edit"
				   id="operation_id"
				   style="width:300px;"
				   placeholder="Номер операции (автоматически)"
				   value="<?php echo isset($operation_header['operation_id']) ? $operation_header['operation_id'] : ''; ?>"
				   disabled
				   >
		</td>
		<td style="width: 30px;">&nbsp;</td>
		<td class="right">
			<b>Дата создания</b>
		</td>
		<td class="left">
			<input type="text"
				   class="header_edit"
				   id="date"
				   style="width:300px;"
				   placeholder="Дата создания (автоматически)"
				   value="<?php echo isset($operation_header['date']) ? $operation_header['date'] : ''; ?>"
				   disabled
				   >
		</td>
	</tr>
	
	<tr>
		<td class="right">
			<b>Пользователь</b>
		</td>
		<td class="left">
			<input type="text"
				   class="header_edit"
				   id="user_id"
				   style="width:300px;"
				   placeholder="Пользователь (автоматически)"
				   value="<?php echo isset($operation_header['operation_id']) ? $users[$operation_header['user_id']]['firstname'].' '.$users[$operation_header['user_id']]['lastname'] : ''; ?>"
				   disabled
				   >
		</td>
		<td style="width: 30px;">&nbsp;</td>
		<td class="right">
			<b>Дата изменения</b>
		</td>
		<td class="left">
			<input type="text"
				   class="header_edit"
				   id="edit_date"
				   style="width:300px;"
				   placeholder="Дата изменения (автоматически)"
				   value="<?php echo isset($operation_header['edit_date']) ? $operation_header['edit_date'] : ''; ?>"
				   disabled
				   >
		</td>
	</tr>
	
	<tr>
		<td class="right">
			<b>Сумма</b>
		</td>
		<td class="left">
			<input type="text"
				   class="header_edit"
				   id="summ"
				   style="width:300px;"
				   placeholder="Сумма (автоматически)"
				   value="<?php echo isset($operation_header['operation_id']) ? number_format($operation_header['summ'],2,'.','') : ''; ?>"
				   disabled
				   >
		</td>
		<td style="width: 30px;">&nbsp;</td>
		<td class="right">
			<b>Коментарий</b>
		</td>
		<td class="left">
			
			<input type="hidden" id="customer_id" value="0">
				
			<input type="text"
				   class="header_edit"
				   id="comment"
				   style="width:100%;"
				   placeholder="Коментарий к операции"
				   value="<?php echo isset($operation_header['comment']) ? $operation_header['comment'] : ''; ?>"
				   >
		</td>
	</tr>
	<tr>
		<td class="right">
			<b>От куда (Склад)</b>
		</td>
		<td class="left">
			<select class="header_edit" id="from_warehouse_id" style="width:300px;">
				<option value="0">* * *</option>
				<?php foreach($shops as $shop_id => $shop){?>
					<optgroup label="маг. <?php echo $shop['name']; ?>">
						<?php foreach($warehouses_shop[$shop_id] as $index => $value){?>
							<?php if(isset($operation_header['from_warehouse_id']) AND $index == (int)$operation_header['from_warehouse_id']){ ?>
								<option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
							<?php }else{ ?>
								<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
							<?php } ?>
						<?php } ?>
					</optgroup>
				<?php } ?>
			</select>
			<a href="/backend/index.php?route=shops/shops.index.php" target="_blank">
				<img src="/backend/img/jleditor_ico.png" title="редактировать" width="16" height="16">		
			</a>
			
		</td>
		<td style="width: 30px;">&nbsp;</td>
		<td class="right">
			<b>Куда (Склад)</b>
		</td>
		<td class="left"><b>Брак</b>
			<SELECT class="header_edit" id="to_warehouse_id" value="-1">
				<option value="-1" <?php if($operation_header['to_warehouse_id'] == -1) echo 'selected'; ?>>Товар у клиента</option>
				<option value="-3" <?php if($operation_header['to_warehouse_id'] == -3) echo 'selected'; ?>>Товар у нас</option>
			</select>
		</td>  
		</td>
	</tr>
	<tr>
		<td class="right">
			<b>Покупатель</b>
		</td>
		<td class="left">
			<select class="header_edit" id="customer_id" style="width:300px;">
				<option value="0">* * *</option>
				<?php foreach($customers as $customer_id => $row){?>
					<?php if(isset($operation_header['customer_id']) AND $customer_id == (int)$operation_header['customer_id']){ ?>
						<option value="<?php echo $customer_id; ?>" selected><?php echo $row['firstname'].' '.$row['lastname'].' ('.$row['name'].')'; ?></option>
					<?php }else{ ?>
						<option value="<?php echo $customer_id; ?>"><?php echo $row['firstname'].' '.$row['lastname'].' ('.$row['name'].')'; ?></option>
					<?php } ?>
				<?php } ?>
			</select>
			<a href="/backend/index.php?route=postav/customer.index.php" target="_blank">
				<img src="/backend/img/jleditor_ico.png" title="редактировать" width="16" height="16">		
			</a>
			
		</td>
		<td style="width: 30px;">&nbsp;</td>
		<td class="right">
			&nbsp;
		</td>
		<td class="left">&nbsp;
		</td>  
		</td>
	</tr>
</table>
	

<?php
//=====================================================================================================================
//=====================================================================================================================
?>

<!--script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/js/backend/ajax_edit_attributes.js"></script-->
<!-- Блок поиска -->
<?php //include 'operation/find_result.php'; ?>

<div class="find_result_back"></div>
<div class="find_result find_result_style">
	<h2 style="margin: 4px;"><a href="javascript:;" class="find_result_close">[Закрыть]</a></h2>

	<div class="add_new_product">
		<table class="text">

			<?php
			include 'product/sub.add.product.header.php';
				define('OPERATION', true);
				include 'product/sub.add.product.php';
			?>
		</table>
		
	</div>
	<div class="find_result_wrapper"></div>
</div>
<div class="find_result_style find_result_shirm">Поиск вариантов . . .</div>
<div class="find_result_style find_result_shirm_add">Создаю товар и добавляю в операцию . . .</div>


<!-- Конец Блок поиска -->
<?php if(isset($_GET['operation_id'])){ ?> 

<table class="text" style="margin-top: 15px;">
	<?php include "operation/product_list_header_rozn.php"; ?>
	<?php include "operation/product_list.php"; ?>
</table>

<?php } ?>

<input type="hidden" id="table" value="<?php echo $table; ?>">

</div>

</div>
<style>
	.size-box input {
		color: red;
	}
</style>

<script>
	 //======================================================================   
    
	//var currencys;
	<?php $list = '';
	foreach($currencys as $index => $value){ 
		$list .= ''.$index.':'.$value['value'].',';
	}
	$list = trim($list, ',');
	?>
	
	var currencys = {<?php echo $list; ?>};
	
	jQuery(document).on('click','#create_new_operation', function(){
	
		location.href = '/backend/index.php?route=<?php echo $_GET['route']; ?>';
   
	});
	
</script>
<script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/operation/operation.js"></script>

