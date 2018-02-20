<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/libs/category_tree/type-for-get.css">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/product.css">
<script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/libs/category_tree/script-for-get.js"></script>
<script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/product/category_tree.js"></script>
<script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/product/product.js"></script>
<?php

$file = explode('/', __FILE__);
if(strpos($_SERVER['PHP_SELF'], $file[count($file)-1]) !== false){
	header("Content-Type: text/html; charset=UTF-8");
	die('Прямой запуск запрещен!');
}

	include('print/php-barcode-generator-master/src/BarcodeGenerator.php');
	//include('print/php-barcode-generator-master/src/BarcodeGeneratorPNG.php');
	//$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
	include('print/php-barcode-generator-master/src/BarcodeGeneratorJPG.php');
	$generator = new \Picqer\Barcode\BarcodeGeneratorJPG();
	
	
	$uploaddir = DIR_IMAGE.'product/';
	$uploaddir_s = 'product/';
	include_once('class/shops.class.php');
	$Shops = new Shops($mysqli, DB_PREFIX);
	
	include_once('class/size.class.php');
	$Size = new Size();
	$size_group_list = $Size->getSizeGroups();
	
	include_once('class/product.class.php');
	$Product = new Product($mysqli, DB_PREFIX);
	
	include_once('class/category.class.php');
	$Category = new Category($mysqli, DB_PREFIX);

	include "class/brand.class.php";
	$Brand = new Brand();
	$brand_list = $Brand->getBrands();
	
	include "class/attributes.class.php";
	$Attributes = new Attributes();
	$attributes_group_list = $Attributes->getAttributeGroups();
	
	include "class/customer.class.php";
	$Customer = new Customer();

	$postav_list = $Customer->getCustomers(4);
	?>
<!-- ================================================================== -->
<!-- ================================================================== -->
<?php
	$filters = array();
	//$filters['start'] = 0;
	
	$name = '';
	if(isset($_GET['product_name']) AND $_GET['product_name'] != ''){
		/*$filters['filter_code'] = $filters['filter_model'] =*/
		$filters['filter_name'] = $name = $_GET['product_name'];
	}
	$shop_id = 0;
	if(isset($_GET['product_shop']) AND $_GET['product_shop'] > 0){
		$filters['filter_shop'] = $shop_id = $_GET['product_shop'];
	}
	$filter_manufacturer = 0;
	if(isset($_GET['product_brand']) AND $_GET['product_brand'] > 0){
		$filters['filter_manufacturer'] = $filter_manufacturer = $_GET['product_brand'];
	}
	$filter_moderation = -1;
	if(isset($_GET['product_status']) AND $_GET['product_status'] > -1){
		$filters['filter_moderation'] = $filter_moderation = $_GET['product_status'];
	}
	
	$filter_category = 0;
	if(isset($_GET['category']) AND $_GET['category'] > 0){
		$filters['filter_category'] = $filter_category = $_GET['category'];
	}

	if(isset($_GET['product_id']) AND $_GET['product_id'] > 0){
		$product_id= $_GET['product_id'];
	}

	$filter_orders = 'pd.name ASC';
	if(isset($_GET['product_order']) AND $_GET['product_order'] != ''){
		$filters['product_order'] = $_GET['product_order'];
	}

	?>

<style>
	.reprice img{
		width: 32px;
		border: 1px solid blue;
		border-radius: 10px;
		float: right;
		margin-top: -8px;
	}
</style>

<form method="GET">
	<input type="hidden" class="product_sort" name="route" value="<?php echo $_GET['route']; ?>">
<table class="find_table">
	<tr>
		<th colspan="4">Выборка продуктов</th>
	</tr>
	<tr>
		<td>Название товара</td>
		<td><input type="text" class="product_sort" name="product_name" value="<?php echo (isset($_GET['product_name'])) ? $_GET['product_name'] : '' ;?>" placeholder="Часть названия или кода"></td>
		<td>Магазин</td>
		<td>
			<?php $shops = $Shops->getShops(); ?>
			<SELECT class="product_sort" name="product_shop" >
				<option value="0">все</option>
				<?php foreach($shops as $index => $value){ ?>
					<?php if(isset($_GET['product_shop']) AND is_numeric($_GET['product_shop']) AND $_GET['product_shop'] == $index){ ?>
						<option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
					<?php }else{ ?>
						<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
					<?php } ?>
				<?php } ?>
			</SELECT>
		</td>
	</tr>
	
	<tr>
		<td>Категория</td>
		<td><a href="javascript:;" class="category_tree select_category" data-id="filter_category_id">выбрать [дерево]</a> (<span class="selected_category" id="name_filter_category_id">Все...</span>)
			<input type="hidden" name="category" id="filter_category_id" class="selected_category_id" value="<?php if($filter_category > 0) echo $filter_category; ?>">
			</td>
		<td>Бренд</td>
		<td>
			<?php $brands = $Brand->getBrands(); ?>
			<SELECT class="product_sort" name="product_brand" >
				<option value="0">все</option>
				<?php foreach($brands as $index => $value){ ?>
					<?php if(isset($_GET['product_brand']) AND is_numeric($_GET['product_brand']) AND $_GET['product_brand'] == $index){ ?>
						<option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
					<?php }else{ ?>
						<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
					<?php } ?>
				<?php } ?>
			</SELECT>
		</td>
	</tr>
	
	<tr>
		<td><b>Сортировка</b></td>
		<td>
			<?php $shops = $Shops->getShops(); ?>
			<SELECT class="product_sort" name="product_order" >
				
				<?php
					$orders = array(
									"pd.name ASC" => 'По алфавиту A-Я',
									"pd.name DESC" => 'По алфавиту Я-А',
									"p.product_id DESC" => 'Новые',
									"p.product_id ASC" => 'Старые',
									"p.price ASC" => 'Дешевые',
									"p.price DESC" => 'Дорогие',
									);
						
				?>
				<?php foreach($orders as $index => $value){ ?>
					<?php if(isset($_GET['product_order']) AND $_GET['product_order'] == $index){ ?>
						<option value="<?php echo $index; ?>" selected><?php echo $value; ?></option>
					<?php }else{ ?>
						<option value="<?php echo $index; ?>"><?php echo $value; ?></option>
					<?php } ?>
				<?php } ?>
			</SELECT>
		</td>
		<td>Статус</td>
		<td>
			<SELECT class="product_sort" name="product_status" >
				<?php $status = array(-1 => 'Все', 0 => 'На сайте', 1 => 'Модерация', 2 => 'Брак/Закрыт' ) ?>
				<?php foreach($status as $index => $value){ ?>
					<?php if(isset($_GET['product_status']) AND is_numeric($_GET['product_status']) AND $_GET['product_status'] == $index){ ?>
						<option value="<?php echo $index; ?>" selected><?php echo $value; ?></option>
					<?php }else{ ?>
						<option value="<?php echo $index; ?>"><?php echo $value; ?></option>
					<?php } ?>
				<?php } ?>
			</SELECT>
		</td>
	</tr>
	<tr>
		<td colspan="4" style="text-align: center;"><input type="submit" name="submit" class="product_sort" value="submit"></td>
	</tr>
</table>
</form>
<!-- ================================================================== -->
<!-- ================================================================== -->

<?php if(isset($_GET['submit']) OR isset($product_id)){ ?>
<?php
 
	if(!isset($product_id)){
		$products_ID = $Product->getProductsID($filters);
	}else{
		$products_ID[] = (int)$product_id;
	}
	
	$filters['start'] = 0;
	$filters['limit'] = 50;
	
	if(isset($_GET['page']) AND $_GET['page'] == 'all'){
		$filters['start'] = 0;
		$filters['limit'] = 100000;
	}else{
		if(isset($_GET['page'])) $filters['start'] = (int)($_GET['page']-1);
	
		if($filters['start'] < 0)$filters['start']  = 1;
		if($filters['start'] > (count($products_ID) / $filters['limit'])) $filters['start']  = (int)(count($products_ID) / $filters['limit']);
	
		$filters['start'] = $filters['start'] * $filters['limit'];
	}

	if(!isset($product_id)){
		$tmp = $products = $Product->getProducts($filters);
	}else{
		$tmp[] = $products[] = $Product->getProduct($product_id);
	}
	
	$page_ids = array();
	foreach($tmp as $tt => $ttt){
		$page_ids[$tt] = $tt;
	}
	
	//echo "<pre>";  print_r(var_dump( $products )); echo "</pre>";
	if(count($products) > 0){
		$max = ceil(count($products_ID) / $filters['limit']);
		$href = '/'.TMP_DIR.'backend/index.php?route=product%2Fproduct.index.php&product_name='.$name.'&product_shop='.$shop_id.'&category='.$filter_category.'&product_brand='.$filter_manufacturer.'&product_status='.$filter_moderation.'&submit=submit';
		$count = 1;
		echo '<a href="'.$href.'&page=all" class="pagination pagination_active">Все</a>';	
		while($count <= $max){
			if(isset($_GET['page']) AND is_numeric($_GET['page']) AND $_GET['page'] == $count){
				echo '<a href="'.$href.'&page='.$count.'" class="pagination pagination_active">'.$count.'</a>';	
			}else{ 
				echo '<a href="'.$href.'&page='.$count.'" class="pagination">'.$count.'</a>';
			}
			if($count == 40 OR $count == 80 OR $count == 120){
				echo '<br><br>';
			}
			$count++;
		}

?>
<style>
	.model_generator_msg{
		display: none;
		padding: 50px;
		border: 3px solid gray;
		position: fixed;
		z-index: 9;
		background-color: #F2E9A9;
		margin-left: calc(50% - 200px);
		width: 400px;
		border-radius: 20px;
	}
</style>
<div class="model_generator_msg">
	Сгенерировать индекс 00-0-00-000<br>
	00 - Категория<br>
	0 - Сезон<br>
	00 - Цвет<br>
	000 - Порядковый номер<br>
</div>
<script>

</script>


<div style="width: 95%" style="margin: 0 auto;">
<div class="table_body">
	<table class="text">
		<tr>
			<th>#</th>
			<th>Статус</th>
			<th>Категория</th>
			<th>Индекс</th>
			<th>Индекс Леся</th>
			<th>Индекс Леся+</th>
			<th>Индекс Твист</th>
			<th>ШтрихКод</th>
			<th>Картинка</th>
			<th style="min-width: 150px;">Атрибуты</th>
			<th>Размеры</th>
			<th>Бренд
				<div class="attribute_list" style="position:fixed; "></div>
			</th>
			<th>Закуп</th>
			<th>Розница</th>
			<th>Сорт</th>
			<th>ВСЕ 
				<input type="checkbox" class="dell_check_all" id="dellall">
				<a href="javascript:;" class="dell_key_all" data-id="all">
					<img src="/<?php echo TMP_DIR; ?>backend/img/cancel.png" title="удалить" width="16" height="16">
				</a>
			</th>
		</tr>
		
		<!-- ================================================================== -->
		<!-- ================================================================== -->
		<?php
			include 'product/sub.add.product.php';
		?>
		<!-- ================================================================== -->
		<!-- ================================================================== -->

		
		<?php $key = 'product_id'; ?>
		<?php $ids = implode(',', $page_ids); ?>
		<?php foreach($products as $index => $ex){ ?>
			<tr id="<?php echo $ex['product_id']; ?>" style="height: 65px;">
				<!--td><?php echo $ex['product_id']; ?></td-->
				<!--td><a href="javascript:;" data-barcode="<?php echo $ex['code']; ?>" class="barcode-print"-->
				<td><a href="/backend/print/barcode_print.php?product_id=<?php echo $ex['product_id']; ?>" data-barcode="<?php echo $ex['code']; ?>" class="barcode-print_" target="_blank">
					<img title="Нажми чтоб напечатать. Продукт Ид = <?php echo $ex['product_id']; ?>" src="data:image/png;base64,<?php echo base64_encode($generator->getBarcode($ex['code'], $generator::TYPE_CODE_128, 1, 70));?>">
					</a>
				</td>
				<td class="mixed"><input type="checkbox" class="edit" id="status<?php echo $ex[$key];?>"  <?php if($ex['status']) echo 'checked';?>></td>
				<?php $category_info = $Category->getCategory($ex['category_id']);?>
				<td>
					<a href="javascript:;" class="category_tree select_category" data-id="category_id_<?php echo $ex['product_id']; ?>"><span class="selected_category" id="name_category_id_<?php echo $ex['product_id']; ?>"><?php echo ($category_info['path'] != '') ? $category_info['path'] : 'выбрать категорию'; ?></span></a>
					<input type="hidden" name="category" class="edit" id="category_id_<?php echo $ex['product_id']; ?>" class="selected_category_id" value="0">
		
				</td>
				<td class="mixed">
					<a href="javascript:;" class="model_generator"
							data-product_id="<?php echo $ex['product_id']; ?>"
							><img src="/backend/img/document_add.png" style="width: 25px;margin-top: -3px;float: left;"></a>
					<input type="text" class="edit" id="model<?php echo $ex[$key];?>" style="width:120px;"
						value="<?php echo $ex['model']; ?>"></td>
				<td class="mixed"><input type="text" class="edit" id="model7<?php echo $ex[$key];?>" style="width:70px;"
					value="<?php echo $ex['model7']; ?>"></td>
				<td class="mixed"><input type="text" class="edit" id="model8<?php echo $ex[$key];?>" style="width:70px;"
					value="<?php echo $ex['model8']; ?>"></td>
				<td class="mixed"><input type="text" class="edit" id="model4<?php echo $ex[$key];?>" style="width:70px;"
					value="<?php echo $ex['model4']; ?>"></td>
				<td class="left"><input type="text" class="edit" id="code<?php echo $ex[$key];?>" style="width:100%;"
					value="<?php echo $ex['code']; ?>">
				</td>
				<td><img class="product_image" src="<?php echo '/image/'.$ex['image'];?>"></td>	
				<td id="product_attribute_wrapper<?php echo $ex['product_id']; ?>">
					<select class="product_attribute_group" id="product_attribute_group<?php echo $ex['product_id']; ?>">
						<option value="0">Выбрать</option>
						<?php foreach($attributes_group_list as $index => $row){ ?>
							<option value="<?php echo $row['attribute_group_id']; ?>"><?php echo $row['name']; ?></option>
						<?php } ?>
					</select>
					<input type="text" placeholder="Новый атрибут" class="product_attribute" id="product_attribute<?php echo $ex['product_id']; ?>">
					<?php $attributes = $Product->getAttributes($ex['product_id']); ?>
					<?php foreach($attributes as $index => $value){ ?>
						<?php
							$data_attribute = array(
											'product_id' => $ex['product_id'],
											'attribute_id' => $value['attribute_id'],
											'group_name' => $value['group_name'],
											'name' => $value['name']
													);
							?>
						<?php $Attributes->echoAttributeList1($data_attribute); ?>
					<?php } ?> 
				</td>
				<td>
					<select class="edit" id="size_group_id<?php echo $ex[$key];?>" style="width:100px;">
						<option value="0">* Без размеров *</option>
						<?php foreach($size_group_list as $index => $value){?>
							<?php if($index == (int)$ex['size_group_id']){ ?>
								<option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
							<?php }else{ ?>
								<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
							<?php } ?>
						<?php } ?>
	                </select>
					<a href="/backend/index.php?route=size/size.main.index.php" target="_blank"><b>+</b></a>
				</td>
				<td>
					<!--a href="javascript:;" class="product_manufacturer" data-id="<?php echo $ex['product_id']; ?>">
						<?php echo $brand_list[$ex['status']]['name'];?>
					</a>
					<input type="hidden" class="edit" id="manufacturer_id<?php echo $ex[$key];?>"
						value="<?php echo $ex['manufacturer_id']; ?>">
					</td-->
					<select class="edit" id="manufacturer_id<?php echo $ex[$key];?>" style="width:100px;">
						<?php foreach($brand_list as $index => $value){?>
							<?php if($index == (int)$ex['manufacturer_id']){ ?>
								<option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
							<?php }else{ ?>
								<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
							<?php } ?>
						<?php } ?>
	                </select>
					<a href="/backend/index.php?route=brands/brands.index.php" target="_blank"><b>+</b></a>
				</td>
				<td class="mixed"><input type="text" class="edit" id="zakup<?php echo $ex[$key];?>" style="width:48%;"
					value="<?php echo number_format($ex['zakup'],2,'.', ''); ?>">
					
					<select class="edit" id="zakup_currency_id<?php echo $ex[$key];?>" style="width:48px;">
						<?php foreach($currencys as $index => $value){?>
							<?php if($index == (int)$ex['zakup_currency_id']){ ?>
								<option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
							<?php }else{ ?>
								<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
							<?php } ?>
						<?php } ?>
	                </select>
				</td>
				<td class="mixed"><input type="text" class="edit" id="price<?php echo $ex[$key];?>" style="width:50%;"
					value="<?php echo number_format($ex['price'],2,'.', ''); ?>">
					<a href="javascript:;" class="reprice" data-product_id="<?php echo $ex['product_id']; ?>">
						<img src="/backend/img/reprice_64.png" title="Выделить и переоценить товар"></a>
				</td>
				<td class="mixed"><input type="text" class="edit" id="sort_order<?php echo $ex[$key];?>" style="width:70px;" value="<?php echo $ex['sort_order']; ?>"></td>
				
				<td class="mixed">
					<input type="checkbox" class="dell_check" id="dell<?php echo $ex['product_id'];?>" data-id="<?php echo $ex['product_id'];?>">
					<a href="javascript:;" class="dell_key" data-id="<?php echo $ex['product_id'];?>">
						<img src="/<?php echo TMP_DIR; ?>backend/img/cancel.png" title="удалить" width="16" height="16">
					</a>
				</td>
			</tr>		
		<?php } ?>
	</table>
</div>

<div class="images_list_back"></div>
<div class="images_list"></div>
	
</div>

<?php
	}
} ?>

<style>
	.reprice_back{
		background-color: black;
		opacity: 0.5;
		position: fixed;
		width: 100%;
		height: 100%;
		display:none;
		top:0;
	}
	.reprice_wrapper_body{
		margin-bottom: 10px;
	}
	.reprice_wrapper{
		display: none;
		padding: 50px;
		border: 3px solid gray;
		position: fixed;
		z-index: 9;
		background-color: #AAEFA7;
		margin-left: calc(50% - 400px);
		width: 800px;
		border-radius: 20px;
	}
	.reprice_wrapper_price, .reprice_wrapper_price input{
		font-size: 25px;
		margin-top: 10px;
	}
	.reprice_wrapper_price input{
		text-align: right;
		border-radius: 10px;
		width: 150px;
		padding-right: 10px;
	}
	.product_qnt{
		border: 1px solid #7B92AA;
		border-collapse: collapse;
		width: 100%;
		/*margin-top: 20px;*/
	}
	.product_qnt tr:hover{
		background-color: #CAD8C9;
	}
	.product_qnt th{
		font-weight: bold;
		text-align: center;
		border: 1px solid #7B92AA;
	}
	product_qnt td{
		border: 1px solid #7B92AA;
		padding: 4px 7px 15px 7px;
	}
	.product_size:hover{
		background-color: black;
		color: white;
		cursor: pointer;
	}
	.selected_sizes{
		background-color: #88AACC;
	}
</style>
<script>
	
	$(document).on('click','.product_size', function(){
		
		var warehouse_id = $(this).data('warehouse_id');
		var size_id = $(this).data('size_id');
		var master_id = $(this).data('master_id');
		
		if($(this).hasClass('minus')){
			var str = $('#reprice_items').val();
			str = str.replace(master_id +'*'+ warehouse_id+'*'+size_id+'|','');
			
			var target = master_id +'_'+size_id +'_'+warehouse_id;
			var value = parseInt($('.'+target).html());
			$('.'+target).html(value + 1);
			
			$('#reprice_items').val(str);
		}else{
			
			//debugger;
			var target = master_id +'_'+size_id +'_'+warehouse_id;
			var value = parseInt($('.'+target).html());
			
			if(value > 0){
				
				$('.'+target).html(value - 1);
				
				$('#reprice_items').val($('#reprice_items').val() + master_id +'*'+ warehouse_id +'*'+size_id+'|');
			}
			
		}
		
		jQuery.ajax({
				type: "POST",
				url: "/backend/ajax/ajax_edit_product.php",
				dataType: "text",
				data: "array="+$('#reprice_items').val()+"&key=get_selected_sizes",
				beforeSend: function(){
				},
				success: function(msg){
					//console.log(msg);
					
					jQuery('.reprice_wrapper_selected').html(msg);
					
				}
		
		});
		
	});
	$(document).on('click','.reprice_back', function(){
		$('.reprice_back').hide();
		$('.reprice_wrapper').hide();
	});
	$(document).on('click','.reprice', function(){
		
		var product_id = $(this).data('product_id');
		
		$('#new_price').val($('#price'+product_id).val());
		
		jQuery.ajax({
				type: "POST",
				url: "/backend/ajax/ajax_edit_product.php",
				dataType: "text",
				data: "id="+product_id+"&key=get_sizes",
				beforeSend: function(){
				},
				success: function(msg){
					//console.log(msg);
					
					jQuery('.reprice_wrapper_body').html(msg);
					jQuery('#reprice_product_id').val(product_id);
					$('.reprice_back').show();
					$('.reprice_wrapper').show();
					
				}
		
		});
		
	});
	
	
	$(document).on('click','.set_price', function(){
		
		$(this).hide();
		$('.set_price_msg').show();
		
		var product_id = $('#reprice_product_id').val();
		var str = $('#reprice_items').val();
		var price = $('#new_price').val();
		
		//debugger;
		
		jQuery.ajax({
				type: "POST",
				url: "/backend/ajax/ajax_edit_product.php",
				dataType: "text",
				data: "array="+str+"&product_id="+product_id+"&price="+price+"&key=set_reprice",
				beforeSend: function(){
				},
				success: function(msg){
					
					console.log(msg);
					$('.reprice_back').trigger('click');
					$(this).show();
					$('.set_price_msg').hide();
				}
		
		});
		
	});
</script>
<div class="reprice_back"></div>
<div class="reprice_wrapper">
	<h2>Выбрать товар для переоценки</h2>
	<div class="reprice_wrapper_price">
		Новая цена
		<input type="text" id="new_price" value="0.00">
		<input type="button" class="set_price" value="Отделить и переоценить" style="width: 400px; text-align: center;">
		<span style="display: none;" class="set_price_msg">Разделяю и переоценяю</span>
		<input type="hidden" id="reprice_product_id" value="">
		<input type="hidden" id="reprice_items" value="">
	</div>
	<h2>Наличие товара на складах</h2>
	<div class="reprice_wrapper_body"></div>
	<h2>Товар по новой цене</h2>
	<div class="reprice_wrapper_selected"></div>
	
</div>
<!-- ================================================================== -->
<!-- ================================================================== -->
<?php
include 'category_tree.php';