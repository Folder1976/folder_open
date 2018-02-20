<!-- Sergey Kotlyarov 2016 folder.list@gmail.com -->
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/libs/category_tree/type-for-get.css">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/product.css">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/operation.css">

<script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/libs/category_tree/script-for-get.js"></script>
<script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/product/category_tree.js"></script>
<script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/operation/operation.js"></script>


<?php
$file = explode('/', __FILE__);
if(strpos($_SERVER['PHP_SELF'], $file[count($file)-1]) !== false){
	header("Content-Type: text/html; charset=UTF-8");
	die('Прямой запуск запрещен!');
}

$key = 'operation_id';
$table = 'operation_products';
$type_id = 8;

include "class/customer.class.php";
$Customer = new Customer();
$postav_list = $postavs = $Customer->getCustomers(4);

include "class/brand.class.php";
$Brand = new Brand();
$brand_list = $Brand->getBrands();

$size_group_list = $size_groups = $Size->getSizeGroups();

include "class/shop_torg.class.php";
$ShopTorg = new ShopTorg();

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
	#create_new_operation{
		padding: 5px 50px 5px 50px;
	}
	.add_new_product{
		display: none;
	}

</style>
<?php
	$operation_id = 0;
	$reload = 0;
	
	//Если есть номера операций и чека = проверим правильность магазина
	if(isset($_GET['operation_id']) AND isset($_GET['sub_operation_id'])){
		
		$operation_id = (int)$_GET['operation_id'];
		
		//if(isset($_GET['is_return']))
		$shop_id = $ShopTorg->getOperationShop($_GET['operation_id']);
		
		if(!isset($shop_lists)){
			echo '<h1>Ошибка : <b>У вас нет доступа к этому магазину!!!</b>';
			die();
		}
		
		$_SESSION['default']['shop_id'] = $shop_id;
		?>
			<script>
				$(document).ready(function(){
					
					$('#shop_id').val(<?php echo $shop_id;?>);
				});
			</script>
		<?php
	}
	
	if($_SESSION['default']['shop_id'] < 1){
		
		echo '<h1>Операция : <b>Нужно выбрать магазин с которым будете работать!!!</b>';
		die();
		
	}
	if(!isset($_GET['operation_id'])){

		$_GET['operation_id'] = $ShopTorg->addOperation($_SESSION['default']['shop_id']);
		$reload = 1;
		
	}
	if(!isset($_GET['sub_operation_id'])){
		$_GET['sub_operation_id'] = $ShopTorg->getSubOperation($_GET['operation_id'] );
		$reload = 1;
	}
	
	$main_warehouse_id = $Warehouse->getMainWarehouse($_SESSION['default']['shop_id']);
	
	//Были созданы новые параметры - перезагрузим страницу
	if($reload){
		
		$get = '';
		foreach($_GET as $index => $value){
			$get .= $index.'='.$value.'&';
		}
		
		$get = trim($get, '&');
		
		?>

		<script>
			location.href = '/backend/index.php?<?php echo $get; ?>';
		</script>

	<?php die();
		} ?>
		

<?php	
//=====================================================================================================================
//=====================================================================================================================
	if(isset($_GET['operation_id'])){
		$operation_header = $Operation->getOperation($_GET['operation_id']);
		$operation_products = $Operation->getOperationProducts($_GET['operation_id'], $_GET['sub_operation_id']);
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
?>
	
<h1>Операция : <b>Магазин</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php if(!isset($_GET['operation_id'])){ ?>
	<a href="javascript:;" id="add_new_operation" class="add_new_operation">Создать новую операцию</a>
	<span class="msg_note">! Создайте операцию чтоб начать добавлять товары</span>
<?php }else{ ?>
	
	<?php if($operation_header['type_id'] == 8){ ?>
	
	<a href="javascript:;" id="create_new_operation" class="add_new_operation">
		[+] Оплата-Закрыть</a>
	
	<a href="javascript:;" id="return" class="add_new_operation" style="padding-left: 40px; padding-right: 40px;background-color: #9B2020;">
		Возврат</a>
	
	<a href="javascript:;" id="incasation" class="add_new_operation" style="padding-left: 40px; padding-right: 40px;background-color: #242BAD;">
		Инкасация</a>

	<?php }else{ ?>
		
		Это не операция продажи.
	
	<?php } ?>

<?php } ?>

	<a href="/backend/operation/print.operation.php?operation_id=<?php echo $_GET['operation_id'];?>&sub_operation_id=<?php echo $_GET['sub_operation_id'];?>" id="print_operation" class="print_operation">
		<img class="print_icon" src="/backend/img/Print_128.png" title="Печать документа" alt="print">
	</a>


</h1>

<div style="width: 90%">
<div class="table_body">
	
<?php
//=====================================================================================================================
//=====================================================================================================================

	//echo '<pre>'; print_r(var_dump( $_SESSION  ));
	
	include 'product/category_tree.php';
?>

	<input type="hidden" id="type_id" value="<?php echo $type_id;?>">
	<input type="hidden" id="user_id" value="<?php echo $_SESSION['default']['user_id'];?>">
				
	<table class="text">
	<tr>
		<td rowspan="2" style="width:200px;" id="sub_operation_sum_wrapper">
			<span id="sub_operation_sum"></span>	
			<span id="sub_operation_currency"> грн</span>	
		</td>
	
		<td class="right">
			<b>Номер операции</b>
		</td>
		<td class="left">
				   
			<input type="text"
				   class="header_edit"
				   id="operation_id"
				   style="width:80px;"
				   placeholder="Номер операции (автоматически)"
				   value="<?php echo isset($operation_header['operation_id']) ? $operation_header['operation_id'] : ''; ?>"
				   disabled
				   >
		</td>
		<td style="width: 10px;">&nbsp;</td>
		<td class="right">
			<b>Дата торговли</b>
		</td>
		<td class="left">
			<input type="text"
				   class="header_edit"
				   id="date"
				   style="width:75px;"
				   placeholder="Дата создания (автоматически)"
				   value="<?php echo isset($operation_header['date']) ? $operation_header['date'] : ''; ?>"
				   disabled
				   >
		</td>
		<td style="width: 10px;">&nbsp;</td>
		<td class="right">
			<b>Дата изменения</b>
		</td>
		<td class="left">
			<input type="text"
				   class="header_edit"
				   id="edit_date"
				   style="width:130px;"
				   placeholder="Дата изменения (автоматически)"
				   value="<?php echo isset($operation_header['edit_date']) ? $operation_header['edit_date'] : ''; ?>"
				   disabled
				   >
		</td>
	</tr>
	
	<tr>
		<td class="right">
			<b>Чек дня</b>
		</td>
		<td class="left">
			<input type="hidden"
				   class="header_edit"
				   id="summ"
				   style="width:80px;"
				   placeholder="Сумма (автоматически)"
				   value="<?php echo isset($operation_header['operation_id']) ? number_format($operation_header['summ'],2,'.','') : ''; ?>"
				   disabled
				   >
			<input type="text"
				   class="header_edit"
				   id="sub_operation_id"
				   style="width:80px;"
				   placeholder="чек"
				   value="<?php echo $_GET['sub_operation_id']; ?>"
				   disabled
				   >
		</td>
		<td style="width: 10px;">&nbsp;</td>
		<td class="right">
			<b>От куда (Склад)</b>
		</td>
		<td class="left">
			<input type="hidden" class="header_edit" id="from_warehouse_id" value="<?php echo $main_warehouse_id; ?>">
			<?php echo $warehouses[$main_warehouse_id]['name']; ?>
		</td>
		<td style="width: 10px;">&nbsp;</td>
		<td class="right">
			<b>Куда</b>
		</td>
		<td class="left">
			<input type="hidden" class="header_edit" id="from_warehouse_id" value="-1">
			<?php echo $warehouses[-1]['name']; ?>
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
		<tr>
			<th>#</th>
			<th>Статус</th>
			<th>Категория</th>
			<th>Индекс</th>
			<th>ШтрихКод</th>
			<th>Картинка</th>
			<th style="min-width: 150px;">Атрибуты</th>
			<th>Размеры</th>
			<th>К-во</th>
			<th>Бренд
				<div class="attribute_list"></div>
			</th>
			<th>Закуп</th>
			<th>Розница</th>
			<th>Сорт</th>
			<th>*</th>
		</tr>
			<?php
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
    <tr>
        <th>id</th>
        <th>Хозяин</th>
        <!--th>Поставщик</th-->
        <th>Индекс</th>
		<th>ШтрихКод</th>
		<th>Фото</th>
        <th style="max-width: 50%;" colspan="2">К-во</th>
        <th >Цена</th>
		<th>Сумма</th>
		<!--th>Розница</th-->
        <th>&nbsp;</th>
    </tr>

    <tr style="background-color: <?php echo $types[$type_id]['color']; ?>;" colspan="2">
        <td class="mixed" colspan="2">новый</td>
        <td class="mixed"><input type="text" id="find_model" style="width:150px;" value="" placeholder="Индекс"></td>
        <td class="mixed"><input type="text" id="find_code" style="width:150px;" value="" placeholder="Код"></td>
        <td class="mixed" colspan="7">
			<b>Расширенный фильтр : </b>
			
			<input type="hidden" id="category_id_find" style="width:10px;" value="">
			<a href="javascript:;" class="category_tree select_category" data-id="category_id_find">Категория [дерево]</a> (<span class="selected_category" id="name_category_id_find">Все...</span>)
			<input type="hidden" name="category"  id="category_id_find" class="selected_category_id" value="0">
				
			
			<!--input type="text" id="find_manufacturer_id" style="width:50px;" value=""-->
			<select class="header_edit" id="find_manufacturer_id" style="width:100px;">
				<option value="">Фирма</option>
				<?php foreach($brand_list as $index => $value){?>
					<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
				<?php } ?>
			</select>

			<!--input type="text" id="find_shop_id" style="width:50px;" value=""-->
			<select class="header_edit" id="find_shop_id" style="width:100px;">
				<option value="">Магазин</option>
				<?php foreach($shops as $index => $value){?>
					<?php if(isset($_SESSION['find_shop_id']) AND $index == (int)$_SESSION['find_shop_id']){ ?>
						<option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
					<?php }else{ ?>
						<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
					<?php } ?>
				<?php } ?>
			</select>

			
			<!--input type="text" id="find_warehouse_id" style="width:50px;" value=""-->
			<select class="header_edit" id="find_warehouse_id" style="width:100px;">
				<option value="">Склад</option>
				<?php foreach($shops as $shop_id => $shop){?>
					<optgroup label="маг. <?php echo $shop['name']; ?>">
						<?php foreach($warehouses_shop[$shop_id] as $index => $value){?>
							<?php if(isset($_SESSION['find_warehouse_id']) AND $index == (int)$_SESSION['find_warehouse_id']){ ?>
								<option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
							<?php }else{ ?>
								<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
							<?php } ?>
						<?php } ?>
					</optgroup>
				<?php } ?>
			</select>
		
			
		</td>
	</tr>
    <td>
        <td colspan="10">&nbsp;</td>
    </td>

<?php include "operation/product_list_shop.php"; ?>

</table>
<?php } ?>

<input type="hidden" id="table" value="<?php echo $table; ?>">
	<br><br>
	<h1>Проданные продукты за этот день</h1>
	<div class="all_day_product" style="font-size: 12px;">
				<?php		
				//=====================================================================================================================
				//=====================================================================================================================
				//=====================================================================================================================
				//=====================================================================================================================
					if(isset($_GET['operation_id'])){
						$operation_header = $Operation->getOperation($_GET['operation_id']);
						
						$operation_products = $Operation->getOperationProducts($_GET['operation_id']);	
						
						//$operation_postav = $Operation->getOperationPostav($_GET['operation_id']);
						//echo '<pre>'; print_r(var_dump( $operation_products  ));
						
						$operation_products_grp = array();
						
						if($operation_products){
							
							foreach($operation_products as $product_id => $rows){
								foreach($rows as $row){
								
								// Групируем товары по Ид и закупу. Цена закупа может разниться от размера
								$operation_products_grp[$row['product_id'].'_'.$row['operation_zakup'].'_'.$row['master_id']]['product'] = $row;
								$operation_products_grp[$row['product_id'].'_'.$row['operation_zakup'].'_'.$row['master_id']]['sizes'][$row['size_id']] = $row;
								}
							}
						}
					
					}
				
					ksort($operation_products_grp);
				?>
				<table class="print_body">
					<tr>
						<th>id</th>
						<th>Хозяин</th>
						<th>Индекс</th>
						<th>Индекс Леся</th>
						<th>Индекс Леся+</th>
						<th>Индекс Твист</th>
						<th>ШтрихКод</th>
						<th>Картинка</th>
						<th>Размерная сетка</th>
						<th>К-во</th>
						<th>Закуп</th>
						<th>Розница</th>
						<th>Сумма</th>
					</tr>
					
					<?php
						$count = 1;
						$total_product = 0;
				
					?>
					<?php foreach($operation_products_grp as $index => $row){ ?>
						<?php foreach($row['sizes'] as $index => $size_row){
							
							$size_group_id = $row['product']['size_group_id'];
							$row_summ = 0;
							$size_group_name = 'Без размера';
							$print_row_show = 1;
							$print_row = '';
							
								
								if($size_group_id > 0){ 
									$print_row .= '';
									$size_group_name = $size_groups[(int)$size_group_id]['name'];
									
									foreach($sizes_on_groups[$size_group_id] as $size_id => $value){ 
									
										//if(!in_array($value['size_id'], $ProductsOnWare['size_ids']) and $type_id != 1) continue; 
										
										
										if(isset($row['sizes'][$value['size_id']])){
											$quantity = (int)$row['sizes'][$value['size_id']]['operation_quantity'];
										}else{
											$quantity = 0;
										}
										
										if($quantity > 0){
											$print_row .= '<tr class="size_row"><td colspan="8"></td>
											 <td style="text-align: right;"><b>';
											$print_row .= ''.$value['name'].'</b></td>
											<td style="text-align: left;"> '.$quantity.'</td>';
											$print_row .= '<td colspan="7"></td></tr>';
										}
										$row_summ += (int)$quantity;
										$total_product += (int)$quantity;
									} 
								}else{
								
									$size = array_shift($row['sizes']);
									$zakup = $size['zakup'];
									/*
									$print_row .= '<div class="size-box">
											'.$size['operation_quantity'] .'
										</div>';
										*/
									$print_row_show = 0;
									$row_summ += (int)$size['operation_quantity'];
									$total_product += (int)$size['operation_quantity'];
								} 
								
							
						
						} ?>
					
						<tr>
							<td><?php echo $count++;?></td>
							<td><?php echo $masters[$row['product']['master_id']]['name'];?></td>
							<td><?php echo $row['product']['model'];?></td>
							<td><?php echo $row['product']['model7'];?></td>
							<td><?php echo $row['product']['model8'];?></td>
							<td><?php echo $row['product']['model4'];?></td>
							<td><?php echo $row['product']['code'];?></td>
							<td><img class="product_image" src="<?php echo '/image/'.$row['image'];?>"></td>	
							<td><?php echo $size_group_name;?></td>
							<td style="text-align: right;"><b><?php echo $row_summ; ?></b></td>
							<td style="text-align: right;">
								<?php echo number_format($row['product']['zakup'], 2,'.','')
									.' '.$currencys[$row['product']['currency_id']]['symbol_right']
								; ?>
										
								</td>
							<td style="text-align: right;"><?php echo number_format($row['product']['price_invert'], 2,'.',''); ?></td>
							<td style="text-align: right;"><b><?php echo number_format($row['product']['summ'], 2,'.',''); ?></b></td>
						</tr>
						
						<?php if($print_row_show) echo $print_row; ?>
					
					<?php } ?>
					
					<table class="print_header">
					<tr>
						<td class="right" colspan="9">&nbsp;</td>
					</tr>
					
					<tr>
						<td class="right" colspan="8">Всего товаров</td>
						<td class="right"><b><?php echo $total_product; ?></b></td>
					</tr>
					
					<tr>
						<td class="right" colspan="8">Сумма</td>
						<td class="right"><b><?php echo number_format($operation_header['summ'], 2,'.',''); ?></b></td>
					</tr>
					
				</table>
					
				
				</table>
	</div>

</div>

</div>
<style>
	
	#incasation_form{
		width: 400px;
		height: 350px;
		background-color: #6B86FF;
		z-index: 99;
		position: relative;
		/* top: 170px; */
		margin: auto;
		border: 4px solid #011D9B;
		text-align: center;
		display: none;
		border-radius: 20px;
		
	}
	#incasation_form input, #incasation_form a, #incasation_form select{
		margin: 15px;
		font-size: 32px;
		width: 40%;
		border-radius: 10px;
	}
	#incasation_form input{
		padding: 7px;
		text-align: right;
	}

	#incasation_form a{
		margin: 30px;
		width: 300px;
		border: 2px solid;
		padding: 10px;
	}
	#incasation_form a:hover{
		background-color: black;
		color: white;
	}
	
	
	.oplata_form{
		width: 800px;
		height: 600px;
		margin-left: calc((100% - 800px) / 2);
		background-color: darkgray;
		z-index: 99;
		position: absolute;
		border: 4px solid;
		text-align: center;
		display: none;
		border-radius: 20px;
	}
	
	.vozvrat_form{
		width: 700px;
		height: 300px;
		background-color: #9B2020;
		z-index: 99;
		position: relative;
		/* top: 170px; */
		margin: auto;
		border: 4px solid;
		text-align: center;
		display: none;
		border-radius: 20px;
	}
	
	.oplata_form input, .oplata_form a, .vozvrat_form input, .vozvrat_form a{
		margin: 15px;
		font-size: 32px;
		width: 40%;
		border-radius: 10px;
	}
	.oplata_form input, .vozvrat_form input{
		padding: 7px;
		text-align: right;
	}

	.oplata_form a, .vozvrat_form a{
		margin: 30px;
		width: 300px;
		border: 2px solid;
		padding: 10px;
	}
	.oplata_form a:hover{
		background-color: black;
		color: white;
	}
	
</style>
<div class="oplata_form">
	
	&nbsp;К оплате:<input type="text" class="calculator1" id="oplat_summ" val=""><br>
	<span class="oplat_title">Оплатили:</span><input type="text"  class="calculator1" id="oplat_money" val=""><br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="zdacha_title">Сдача:</span><input type="text" id="zdacha" val=""><br>
	<span class="cart_title">Оплата карто:й xxxx xxxx xxxx</span><input type="text" id="cart" val="" placeholder="XXXX" style="width: 100px"><br><br><br>
	<input type="hidden" class="operation_type" value="0">
	<a href="javascript:;" id="bay">Оплата</a>
	<a href="javascript:;" class="continue_bay">Продолжить</a><br><br><br><br><br>
	<a href="javascript:;" class="new_sub_operation">Новая операция</a><br><br><br><br><br>
	<a href="javascript:;" class="second-key" style="font-size: 16px;" id="del_sub_operation">Удалить</a>
	<a href="javascript:;" class="second-key" style="font-size: 16px;" id="kredit">В кредит</a>
	<a href="javascript:;" class="second-key" style="font-size: 16px;" id="rezerv">Резерв(у нас)</a>
	<a href="javascript:;" class="second-key" style="font-size: 16px;" id="zabrali">Примерка(забрали)</a>
	
</div>
<div class="vozvrat_form">
<br>
	<h3>!!! Если на эту операцию есть оплата - будет создан новый чек.</h3>
	&nbsp;Возвращаем:<input type="text" class="calculator1" id="vozvrat_summ" val=""><br><br><br>
	<input type="hidden" id="operation_type" value="<?php echo $_SESSION['default']['user_id']; ?>">
	<a href="javascript:;" id="vozvrat">Вернуть</a>
	<a href="javascript:;" class="continue_bay">Продолжить</a><br><br><br><br><br>
	<a href="javascript:;" class="new_sub_operation">Новая операция</a><br><br><br><br><br>

</div>
<div id="incasation_form">
	
	&nbsp;Забрали:<input type="text" id="incasation_summ" val=""><br>
	Кто:<select type="text" id="incasation_user_id">
		<?php foreach($users as $user_id => $user){ ?>
			<option value="<?php echo $user_id;?>"><?php echo $user['firstname'].' '.$user['lastname'];?></option>
		<?php } ?>
		</select>
	<br><br><br>
	<a href="javascript:;" id="incasation_submit">Отдать деньги</a>
	<br><br><br><br><br>
	<a href="javascript:;" id="incasation_close">Закрыть</a>
</div>

	
<style>
	.shirma{
		display: none;
		z-index:99999;
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-color: black;
		opacity: 0.5;
	}
</style>
<div id="shirma" class="shirma"></div>

<style>
	.kredit_form{
		width: 700px;
		height: 600px;
		margin-left: calc((100% - 700px) / 2);
		background-color: #149B87;
		z-index: 99;
		position: absolute;
		/* top: 170px; */
		margin-top: 20px;
		border: 4px solid;
		text-align: center;
		display: none;
		border-radius: 20px;
		font-size: 22px;
		
	}
	.kredit_form input, .kredit_form select{
		font-size: 22px;
		margin: 10px;
		background-color: white;
		border-radius: 10px;
		padding: 4px;
		color: black;
	}
	.customer_find_res{
		height: 220px;
		text-align: left;
		margin-left: 20px;
		font-weight: bold;
		border: 1px solid gray;
		width: 90%;
		padding: 5px;
		overflow: auto;
		background-color: cornsilk;
		
	}
	.customer_find_res a{
		padding-bottom: 7px;
	}
	#selected_customer_name{
		font-size: 24px;
		font-weight: bold;
	}
	
	#set_kredir, #cansel_kredit{
		font-size: 24px;
		font-weight: bold;
		margin: 30px;
		width: 300px;
		border: 2px solid;
		padding: 10px;
		border-radius: 10px;
	}
	#set_kredir:hover{
		background-color: #87C14D;
	}
	#cansel_kredit:hover{
		background-color: #BF4C4C;
	}
</style>

<div class="kredit_form"><br>
	<input type="hidden" id="customer_id" val="">
	<input type="hidden" id="kredit_type_id" val="">
			<div id="selected_customer_name"><font color="red">Клиент - Не выбран</font></div>
			<br>
			<input type="text" id="customer_name" val="" placeholder="Искать, лимит 10 шт">
			<a href="/backend/index.php?route=postav/customer.index.php" target="_blank">
				<img src="/backend/img/customer-edit.png" style="width: 40px;position: absolute;margin-top: 10px;" title="Редактор клиентов" alt="Клиенты">
			</a>
			<br>
			<div class="customer_find_res">
				Начните набирать часть имени, результаты поиска будут тут...
			</div>
			
	Где товар:<SELECT id="kredit_to_warehouse_id" value="-1">
		<option value="-1" <?php if($operation_header['to_warehouse_id'] == -1) echo 'selected'; ?>>Товар у клиента</option>
		<option value="-3" <?php if($operation_header['to_warehouse_id'] == -3) echo 'selected'; ?>>Товар у нас</option>
	</select><br>
	Первый взнос:<input type="text" id="kredit_money" val="0" placeholder="0.00" style="width: 60%;">
	<br><input type="text" id="kredit_comment" val="" placeholder="Коментарий" style="width: 95%;">
	<br><br><br>
	<a href="javascript:;" id="set_kredir">Оформить</a>
	<a href="javascript:;" id="cansel_kredit">Отбой</a>
	
</div>

<script>
	
	$(document).on('keyup', '#customer_name', function(){
		
		var post = 'key=get_customers';
		post = post + '&name='+$(this).val();
		post = post + '&group_id=1,2,5';
		
		//debugger;
		
		jQuery.ajax({
			type: "POST",
			url: "/backend/customer/ajax_customer.php",
			dataType: "text",
			data: post,
			beforeSend: function(){
			},
			success: function(msg){
				
				//console.log(msg);
				
				$('.customer_find_res').html(msg);
				
			}
		});
		
	});
	
	$(document).on('click', '#set_kredir', function(){
		
		var post = 'key=add_kredit';
		post = post + '&customer_id='+$('#customer_id').val();
		post = post + '&operation_id='+$('#operation_id').val();
		post = post + '&comment='+$('#kredit_comment').val();
		post = post + '&kredit_money='+$('#kredit_money').val();
		post = post + '&sub_operation_id='+$('#sub_operation_id').val();
		post = post + '&type_id='+$('#kredit_type_id').val();
		post = post + '&to_warehouse_id='+$('#kredit_to_warehouse_id').val();
		
		if($('#customer_id').val() == "" || $('#customer_id').val() == 0 ){
			alert('Не выбран клиент!');
		}else{
		
			jQuery.ajax({
				type: "POST",
				url: "/backend/operation/ajax_edit_operation.php",
				dataType: "text",
				data: post,
				beforeSend: function(){
				},
				success: function(msg){
					
					console.log(msg);
					
					$('#kredit_form').html(msg);
					
					location.href = '/backend/index.php?route=shop_torg/shop_torg.index.php';
					
					
				}
			});
		}
		
	});
	
	$(document).on('click', '#cansel_kredit', function(){
		$('.kredit_form').hide();
	});
	
	$(document).on('click', '.customer_list', function(){
		
		var name = $(this).html();
		var id = $(this).data('customer_id');
		
		$('#customer_id').val(id);
		$('#selected_customer_name').html(name);
		
	});
	
	 //======================================================================   
    
	//var currencys;
	<?php $list = '';
	foreach($currencys as $index => $value){ 
		$list .= ''.$index.':'.$value['value'].',';
	}
	$list = trim($list, ',');
	?>
	
	var currencys = {<?php echo $list; ?>};
	
	$(document).on('click','#return', function(){
	
		$('#vozvrat_summ').val($('#sub_operation_sum').html());
		$('.vozvrat_form').show();

	});

	$(document).on('click','#kredit', function(){
	
		$('.kredit_form').show();
		$('#kredit_type_id').val(11);
		$('#kredit_to_warehouse_id').prop('disabled',false);

	});

	$(document).on('click','#rezerv', function(){
	
		$('.kredit_form').show();
		$('#kredit_type_id').val(5);
		$('#kredit_to_warehouse_id').val(-3);
		$('#kredit_to_warehouse_id').prop('disabled','disabled');

	});

	$(document).on('click','#zabrali', function(){
	
		$('.kredit_form').show();
		$('#kredit_type_id').val(4);
		$('#kredit_to_warehouse_id').val(-1);
		$('#kredit_to_warehouse_id').prop('disabled','disabled');

	});

	
	
	
	$(document).on('click','#create_new_operation', function(){
	/*
		$('.second-key').show();
		$('#oplat_money').show();
		$('#zdacha').show();	
		$('.oplat_title').show();
		$('.zdacha_title').show();
	*/
		$('#oplat_summ').val($('#sub_operation_sum').html());
		$('#oplat_money').val($('#sub_operation_sum').html());
		$('#zdacha').val('0');
	
		$('.oplata_form').show();
	
		//location.href = '/backend/index.php?route=<?php echo $_GET['route']; ?>';
   
	});
	
	$(document).on('click','.continue_bay', function(){
	
		$('.oplata_form').hide();
		$('.vozvrat_form').hide();
	
	});
	
	$(document).ready(function(){
		sub_summ();	
	});
	
	
	$(document).on('click','#incasation_submit', function(){
        
		$('#shirma').show();
		
		var operation_id = $('#operation_id').val();
		var sub_operation_id = 0;
		var zdacha = 0;
		var oplat_summ = $('#incasation_summ').val();
		var status = $('#incasation_user_id').val();
		var oplat_money = 0;
			
			if(operation_id > 0){
				var post = 'key=add_oplat';
				post = post + '&operation_id='+operation_id;
				post = post + '&sub_operation_id='+sub_operation_id;
				post = post + '&zdacha='+zdacha;
				post = post + '&oplat_summ='+oplat_summ;
				post = post + '&oplat_money='+oplat_money;
				post = post + '&status='+status;
				
				jQuery.ajax({
					type: "POST",
					url: "/backend/shop_torg/ajax_edit_shop_torg.php",
					dataType: "text",
					data: post,
					beforeSend: function(){
					},
					success: function(msg){
						
						console.log(msg);
						
						if(msg == ''){
						
							//location.href = '/backend/index.php?route=<?php echo $_GET['route']; ?>';
						}else{
							
							alert(msg);
						}
						
						$('#shirma').hide();
						
					}
				});
			}
		
    });
	
	
	$(document).on('click','#del_sub_operation', function(){
        
		var operation_id = $('#operation_id').val();
		var sub_operation_id = $('#sub_operation_id').val();
			
		if (confirm('Вы действительно желаете удалить эту операцию?')){
			if(operation_id > 0){
				var post = 'key=dell_sub_operation';
				post = post + '&operation_id='+operation_id;
				post = post + '&sub_operation_id='+sub_operation_id;
				
				jQuery.ajax({
					type: "POST",
					url: "/backend/operation/ajax_edit_operation.php",
					dataType: "text",
					data: post,
					beforeSend: function(){
					},
					success: function(msg){
						
						console.log(msg);
						
						location.href = '/backend/index.php?route=<?php echo $_GET['route']; ?>';
					}
				});
			}
		}
    });
	
		

	
	$(document).on('click', '#incasation', function(){
		$('#incasation_form').show();
	});
	$(document).on('click', '#incasation_close', function(){
		$('#incasation_form').hide();
	});
	
	$(document).on('click','#bay', function(){
        
		$('#shirma').show();
		
		var operation_id = $('#operation_id').val();
		var sub_operation_id = $('#sub_operation_id').val();
		var zdacha = $('#zdacha').val();
		var oplat_summ = $('#oplat_summ').val();
		var oplat_money = $('#oplat_money').val();
		var cart = $('#cart').val();
			
			if(operation_id > 0){
				var post = 'key=add_oplat';
				post = post + '&operation_id='+operation_id;
				post = post + '&sub_operation_id='+sub_operation_id;
				post = post + '&zdacha='+zdacha;
				post = post + '&oplat_summ='+oplat_summ;
				post = post + '&oplat_money='+oplat_money;
				post = post + '&cart='+cart;
				
				jQuery.ajax({
					type: "POST",
					url: "/backend/shop_torg/ajax_edit_shop_torg.php",
					dataType: "text",
					data: post,
					beforeSend: function(){
					},
					success: function(msg){
						
						console.log(msg);
						
						if(msg == ''){
						
							location.href = '/backend/index.php?route=<?php echo $_GET['route']; ?>';
						}else{
							
							alert(msg);
						}
						
						$('#shirma').hide();
						
					}
				});
			}
		
    });
	
	$(document).on('click','#vozvrat', function(){
        
		//debugger;
		
		$('#shirma').show();
		
		var operation_id = $('#operation_id').val();
		var sub_operation_id = $('#sub_operation_id').val();
		var vozvrat_summ = $('#vozvrat_summ').val();
		var status = $('#operation_type').val();
			
			if(operation_id > 0){
				var post = 'key=add_vozvrat';
				post = post + '&operation_id='+operation_id;
				post = post + '&sub_operation_id='+sub_operation_id;
				post = post + '&vozvrat_summ='+vozvrat_summ;
				post = post + '&status='+status;
				
				jQuery.ajax({
					type: "POST",
					url: "/backend/shop_torg/ajax_edit_shop_torg.php",
					dataType: "text",
					data: post,
					beforeSend: function(){
					},
					success: function(msg){
						
						console.log(msg);
						
						if(msg == ''){
						
							location.href = '/backend/index.php?route=<?php echo $_GET['route']; ?>';
						}else{
							
							alert(msg);
						}
						
						$('#shirma').hide();
						
					}
				});
			}
		
    });
	
	$(document).on('click','.new_sub_operation', function(){
		location.href = '/backend/index.php?route=<?php echo $_GET['route']; ?>';
	});
	
	
	
	$(document).keypress(function(eventObject){
		if(eventObject.which == 43){
			$('#create_new_operation').trigger('click');
		}
	});
	
	$(document).on('keyup', '.calculator1', function(){
		calculator();	
	});
	$(document).on('change', '.calculator1', function(){
		calculator();	
	});
	function calculator(){
		
		$('#zdacha').val($('#oplat_summ').val() - $('#oplat_money').val());
		
	}
	
</script>


