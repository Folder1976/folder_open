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
$type_id = 7;

include "class/customer.class.php";
$Customer = new Customer();
$postav_list = $postavs = $Customer->getCustomers(4);

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
		background-color: <?php echo $types[$type_id]['color']; ?>;
	}
</style>

	
<h1>Операция : <b>Получили товар из другого магазина </b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
//Если прилетел указатель на родительскую накладную
	if(isset($_GET['invert_operation_id']) AND (int)$_GET['invert_operation_id'] > 0){

		$operation = $Operation->getOperation((int)$_GET['invert_operation_id']);
		
		
		
		$to_warehouse_id = array_shift($warehouses_shop[abs($operation['to_warehouse_id'])]);
		$from_warehouse_id = $warehouses[$operation['from_warehouse_id']];
		
		
		$data = array(
					  'customer_id'=>4,
					  'comment'=>'',
					  'type_id'=>$type_id,
					  'invert_operation_id'=>(int)$_GET['invert_operation_id'],
					  'from_warehouse_id' => -$from_warehouse_id['shop_id'],
					  'to_warehouse_id' => $to_warehouse_id['warehouse_id']
					  );
		
		$operation_id = $Operation->addOperation($data);
		
		$_GET['operation_id'] = $operation_id;
		?>
		<script>
			$(document).ready(function(){
				location.href = "/backend/index.php?route=operation_shop/in.shop.index.php&operation_id=<?php echo $operation_id; ?>";	
			});
		</script>
		<?php
		
	}
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
		
		//Если есть зеркальная операция
		if($operation_header['invert_operation_id'] > 0){
			$invert_operation_products = $Operation->getOperationProducts($operation_header['invert_operation_id']);
			$invert_operation_products_grp = array();
			
			if($invert_operation_products){
				foreach($invert_operation_products as $rows){
					foreach($rows as $row){
					// Групируем товары по Ид и закупу. Цена закупа может разниться от размера
					$invert_operation_products_grp[$row['product_id'].'_'.$row['operation_zakup'].'_'.$row['master_id']]['product'] = $row;
					$invert_operation_products_grp[$row['product_id'].'_'.$row['operation_zakup'].'_'.$row['master_id']]['sizes'][$row['size_id']] = $row;
					}
				}
			}
		}
	
	}
	//echo '<pre>'; print_r(var_dump( $_SESSION  ));
	
	include 'product/category_tree.php';
	
	
	if($operation_header['invert_operation_id'] > 0){ ?>
		<input type="hidden" id="invert_operation_id" value="<?php echo $operation_header['invert_operation_id'];?>">
	<?php } ?>
	
	
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
			<b>От куда (Магазин)</b>
		</td>
		<td class="left">
			<select class="header_edit" id="from_warehouse_id" style="width:300px;">
				<option value="0">* * *</option>
				<?php foreach($shops as $shop_id => $shop){?>
					<?php if(isset($operation_header['from_warehouse_id']) AND -($shop_id) == (int)$operation_header['from_warehouse_id']){ ?>
						<option value="<?php echo -($shop_id); ?>" selected><?php echo $shop['name']; ?></option>
					<?php }else{ ?>
						<option value="<?php echo -($shop_id); ?>"><?php echo $shop['name']; ?></option>
					<?php } ?>
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
		<td class="left">
			<select class="header_edit" id="to_warehouse_id" style="width:300px;">
				<option value="0">* * *</option>
				<?php foreach($shops as $shop_id => $shop){?>
					<optgroup label="маг. <?php echo $shop['name']; ?>">
						<?php foreach($warehouses_shop[$shop_id] as $index => $value){?>
							<?php if(isset($operation_header['to_warehouse_id']) AND $index == (int)$operation_header['to_warehouse_id']){ ?>
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
<style>
	.control_list{
		background-color: #FCDBB0;
	}
</style>

<?php
/*
header("Content-Type: text/html; charset=UTF-8");
echo '<pre>'; print_r(var_dump( $invert_operation_products_grp  ));
die();
*/


//Если прилетел указатель на родительскую накладную
	if(isset($invert_operation_products_grp) AND count($invert_operation_products_grp)>0){ ?>
		<table class="text control_list" style="margin-top: 15px;">
		<tr>
			<th colspan="5">Список товаров по накладной отправки (расхождение)</th>
		</tr>
		<tr>
			<th>Индекс</th>
			<th>ШтрихКод</th>
			<th>Фото</th>
			<th style="max-width: 50%;" colspan="2">К-во</th>
		</tr>
		
		<?php foreach($invert_operation_products_grp as $index => $ex){ ?>
		<?php $is_show = 0; ob_start();?>
			<tr>
				<td class="mixed"><?php echo $ex['product']['model']; ?></td>
				<td class="mixed"><?php echo $ex['product']['code']; ?></td>
				<td class="mixed" style="width: 80px;">
					<img class="product_image" src="/image/<?php echo $ex['product']['image']; ?>">
				</td>
				<td class="left size_wrapper">
							<?php $size_group_id = $ex['product']['size_group_id'] ; ?>
							<?php $row_summ = 0;?>
							
							<?php if($size_group_id > 0){ ?>
								<b><?php echo $size_groups[(int)$size_group_id]['name']; ?></b><br>
								
								<?php foreach($sizes_on_groups[$size_group_id] as $size_id => $value){?>
									
									<?php if(isset($ex['sizes'][$value['size_id']])){
										$quantity = (int)$ex['sizes'][$value['size_id']]['operation_quantity'];
										
										//Отнимаем от того что должно прийти
										if(isset($operation_products_grp[$index]['sizes'][$value['size_id']]['operation_quantity'])){
											
											$quantity_tmp = $quantity;
											
											$quantity = $quantity - $operation_products_grp[$index]['sizes'][$value['size_id']]['operation_quantity'];
											
											$operation_products_grp[$index]['sizes'][$value['size_id']]['operation_quantity'] -= $quantity_tmp;
											
										}
										
									}else{
										$quantity = 0;
									}
									?>
									
									<?php if($quantity > 0){ ?>
									<div class="size-box">
										<span><?php echo $value['name'];?></span> : 
										<span><?php echo $quantity;?></span>
									</div>
									<?php $is_show = 1; ?>
									<?php } ?>
									
									<?php $row_summ += (int)$quantity;?>
								
								<?php } ?>
							<?php }else{ ?>
							
								<?php  $size = array_shift($ex['sizes']);
									$zakup = $size['zakup'];
								?>
								
								<?php
									//Проверяем расхождение
									if(isset($operation_products_grp[$index]) AND count($operation_products_grp[$index]['sizes']) > 0){
										$size_2 = array_shift($operation_products_grp[$index]['sizes']);
										
										
									}
								?>
								
								<div class="size-box">
										<span>Без размера</span> :
										<span><?php echo $size['operation_quantity'];?></span>
									</div>
								<?php if($size['operation_quantity'] > 0) $is_show = 1; ?>
								
								<?php $row_summ += (int)$size['operation_quantity'];?>
							<?php } ?>
					</td>
					<td><?php echo $row_summ;?></td>
				</tr>
				<?php
					$content = '';
						if($is_show){
							$content = ob_get_contents();
						}
					ob_end_clean();
					echo $content;
				?>
		<?php } ?>
		<!--/table-->
	<?php } ?>

<?php
	//Если есть лишние товары
	if(isset($operation_products_grp) AND count($operation_products_grp)>0){ ?>
		<!--table class="text control_list" style="margin-top: 15px;"-->
		<tr>
			<th colspan="5">&nbsp;</th>
		</tr>
		<tr>
			<th colspan="5">Приехало лишнее</th>
		</tr>
		<tr>
			<th>Индекс</th>
			<th>ШтрихКод</th>
			<th>Фото</th>
			<th style="max-width: 50%;" colspan="2">К-во</th>
		</tr>
		
		<?php foreach($operation_products_grp as $index => $ex){ ?>
		<?php $is_show = 0; ob_start();?>
			<tr>
				<td class="mixed"><?php echo $ex['product']['model']; ?></td>
				<td class="mixed"><?php echo $ex['product']['code']; ?></td>
				<td class="mixed" style="width: 80px;">
					<img class="product_image" src="/image/<?php echo $ex['product']['image']; ?>">
				</td>
				<td class="left size_wrapper">
							<?php $size_group_id = $ex['product']['size_group_id'] ; ?>
							<?php $row_summ = 0;?>
							
							<?php if($size_group_id > 0){ ?>
								<b><?php echo $size_groups[(int)$size_group_id]['name']; ?></b><br>
								
								<?php foreach($sizes_on_groups[$size_group_id] as $size_id => $value){?>
									
									<?php if(isset($ex['sizes'][$value['size_id']])){
										$quantity = (int)$ex['sizes'][$value['size_id']]['operation_quantity'];
									}else{
										$quantity = 0;
									}
									?>
									
									<?php if($quantity > 0){ ?>
									<div class="size-box">
										<span><?php echo $value['name'];?></span> : 
										<span><?php echo $quantity;?></span>
									</div>
									<?php $is_show = 1; ?>
									<?php } ?>
									
									<?php $row_summ += (int)$quantity;?>
								
								<?php } ?>
							<?php }else{ ?>
							
								<?php  $size = array_shift($ex['sizes']);
									$zakup = $size['zakup'];
								?>
							
								<div class="size-box">
										<span>Без размера</span> :
										<span><?php echo $size['operation_quantity'];?></span>
									</div>
								<?php if($size['operation_quantity'] > 0) $is_show = 1; ?>
								
								<?php $row_summ += (int)$size['operation_quantity'];?>
							<?php } ?>
					</td>
					<td><?php echo $row_summ;?></td>
				</tr>
				<?php
					$content = '';
						if($is_show){
							$content = ob_get_contents();
						}
					ob_end_clean();
					echo $content;
				?>
		<?php } ?>
		</table>
	<?php } ?>


<input type="hidden" id="table" value="<?php echo $table; ?>">

</div>

</div>


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

