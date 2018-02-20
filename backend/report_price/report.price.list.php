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
?>

<style>
	h1{
		background-color: #759B61;
		padding: 10px;
		margin-bottom: 0px;
		color: white;
		font-weight: bold;
	}
	.size-box input{
		max-width: 30px;
		text-align: center;
		font-weight: bold;
		font-size: 13px;
		color: red;
		background-color: <?php echo $types[$type_id]['color']; ?>;
	}
	.reprice_table td{
		padding-left: 10px;
		padding-right: 10px;
	}
</style>

	
<h1>История изменения цен ЦЕНЫ</h1>

<div style="width: 90%">
<div class="table_body">
	
	
<!-- ================================================================== -->
<!-- ================================================================== -->
<?php

	include "class/brand.class.php";
	$Brand = new Brand();
	$brand_list = $Brand->getBrands();
	
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
	$filter_date = '';
	if(isset($_GET['filter_date']) AND $_GET['filter_date'] > 0){
		$filters['filter_date'] = $filter_date = $_GET['filter_date'];
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

	$filter_orders = 'P.code ASC';
	if(isset($_GET['product_order']) AND $_GET['product_order'] != ''){
		$filters['product_order'] = $filter_orders = $_GET['product_order'];
	}

	$input_filter_category_id = 'Все';
	if(isset($_GET['input_category_name']) AND $_GET['input_category_name'] != ''){
		$input_filter_category_id = $_GET['input_category_name'];
	}

	?>

<!-- ================================================================== -->
<!-- ================================================================== -->

	
<?php if(!isset($_GET['filter_date'])){ ?>
	<style>
		.dates{
			margin-left: 50px;
		}
	</style>
	<?php
	$sql = 'SELECT PRP.date As rep_date,
				DATE_FORMAT( PRP.date , "%Y-%m-%d" ) as sort_date	
			FROM '.DB_PREFIX.'product_reprice PRP
			
				GROUP BY sort_date
				ORDER BY rep_date DESC';
	
		$r = $mysqli->query($sql) or die($sql);
		?>

		<div class="dates">
		<?php while($row = $r->fetch_assoc()){
			
			echo '<br><a href="/backend/index.php?route=report_price%2Freport.price.list.php&filter_date='.$row['sort_date'].'&submit=submit">'.$row['sort_date'].'<br>';
		}
		echo '</div>';
	die('<hr>***');
}


	$master_ids = array();
	$warehouse_ids = array();
	$warehouse_ids_colum = array();
	$size_ids = array();
	$size_ids_colum = array();
	$data = $filters;
	
	$products_no_sort = array();
	
		$sql = 'SELECT PRP.date As rep_date,
					PRP.price As rep_price,
					P.code AS product_code,
					P.*,
					U.*
			FROM '.DB_PREFIX.'product_reprice PRP
				LEFT JOIN '.DB_PREFIX.'product P ON P.product_id = PRP.product_id
				LEFT JOIN '.DB_PREFIX.'user U ON U.user_id = PRP.user_id
				LEFT JOIN '.DB_PREFIX.'product_to_category p2c ON (P.product_id = p2c.product_id)
				LEFT JOIN '.DB_PREFIX.'category_path cp ON (p2c.category_id = cp.category_id)
				WHERE 1 ';
				
		if (!empty($data['filter_name'])) {
			$sql .= " AND (";
			$sql .= "    P.model LIKE '%" . trim($data['filter_name']) . "%'";
			$sql .= " OR P.model4 LIKE '%" . trim($data['filter_name']) . "%'";
			$sql .= " OR P.model7 LIKE '%" . trim($data['filter_name']) . "%'";
			$sql .= " OR P.model8 LIKE '%" . trim($data['filter_name']) . "%'";
			$sql .= " OR P.code LIKE '%" . trim($data['filter_name']) . "%')";
		}

		if(isset($_GET['filter_date']) AND $_GET['filter_date'] > 0){
			$sql .= ' AND DATE_FORMAT( PRP.date , "%Y-%m-%d" ) = "' . $_GET['filter_date'] . '" ';
		}
		
		if (isset($data['filter_manufacturer']) && !is_null($data['filter_manufacturer'])) {
			$sql .= " AND P.manufacturer_id = '" . (int)$data['filter_manufacturer'] . "'";
		}

		if (isset($data['filter_category']) && !is_null($data['filter_category'])) {
			$sql .= " AND cp.path_id = '" . (int)$data['filter_category'] . "'";
		}	
		
		$sql .= ' ORDER BY '.$filter_orders.'';
		
		$r = $mysqli->query($sql) or die($sql);
		//echo $sql;
		
		$products = array();
		$print_product_ids = array();
		
		while($row = $r->fetch_assoc()){
			
			$products[$row['product_code']][] = $row;
			$print_product_ids[$row['product_id']] = $row['product_id'];
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

<form method="GET" class="findform" style="display: none;">
	<input type="hidden" class="product_sort" name="route" value="<?php echo $_GET['route']; ?>">
<table class="find_table" style="background-color:#ebf4fb">
	<tr>
		<th colspan="4">Выборка продуктов</th>
			<a href="/backend/report_price/print.price.php?key=price&product_ids=<?php echo implode(',', $print_product_ids); ?>" id="print_operation" class="print_operation">
			<img class="print_icon" src="/backend/img/Print_128.png" title="Печать документа" alt="print">
		</a>
	
	</tr>
	<tr>
		<td>Название товара</td>
		<td><input type="text" class="product_sort" name="product_name" value="<?php echo (isset($_GET['product_name'])) ? $_GET['product_name'] : '' ;?>" placeholder="Часть названия или кода"></td>
		<td>Дата переоценки</td>
		<td><input type="text" class="product_sort" name="filter_date" value="<?php echo (isset($_GET['filter_date'])) ? $_GET['filter_date'] : '' ;?>" placeholder="<?php echo date('Y-m-d'); ?>"></td>
		</td>
	<!--td>Магазин</td>
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
	</tr-->
	
	<tr>
		<td>Категория</td>
		<td><a href="javascript:;" class="category_tree select_category" data-id="filter_category_id">выбрать [дерево]</a> (<span class="selected_category" id="name_filter_category_id">Все...</span>)
			<input style="background-color: #ebf4fb;border:none;font-weight: bold;" type="text" id="input_category_name" name="input_category_name" value="<?php echo $input_filter_category_id;?>">
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
									"P.code ASC" => 'По алфавиту A-Я',
									"P.code DESC" => 'По алфавиту Я-А',
									"P.product_id DESC" => 'Новые',
									"P.product_id ASC" => 'Старые',
									"P.price ASC" => 'Дешевые',
									"P.price DESC" => 'Дорогие',
									"PRP.date ASC" => 'Давно переоценили',
									"PRP.date DESC" => 'Недавно переоценили',
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

		
		<?php $row_1 = ''; ?>

		<table class="reprice_table">
			<tr>
				<th>Картинка</th>
				<th>Индекс</th>
				<th>ШтрихКод
					<a href="/backend/report_price/print.price.list.php?key=price&&filter_date=<?php echo $_GET['filter_date']; ?>&product_ids=<?php echo implode(',', $print_product_ids); ?>" id="print_operation" class="print_operation">
					<img class="print_icon" src="/backend/img/Print_128.png" title="Печать документа" alt="print">
				</th>
				<th>Дата</th>
				<th>Цена</th>
				<th>Изменил</th>
			</tr>
			
			<?php foreach($products as $rows){ 
				
				$tmp = $rows;
				$row = array_shift($tmp);
				unset($tmp);
				
				?>
				<tr>
					<td rowspan="<?php echo count($rows)+1; ?>"><img class="product_image" src="<?php echo '/image/'.$ex['image'];?>"></td>	
					<td rowspan="<?php echo count($rows)+1; ?>"><?php echo $row['model']; ?></td>
					<td rowspan="<?php echo count($rows)+1; ?>"><?php echo $row['product_code']; ?></td>
					<td colspan="3"></td>
				</tr>
					
				<?php $count = 1; foreach($rows as $row){ ?>
				
					<tr>
						<?php if($count++ == 1){ ?>
							<!--td colspan="3" rowspan="<?php echo count($rows);?>"></td-->
						<?php } ?>
						<td><?php echo $row['rep_date']; ?></td>
						<td style="text-align: right;"><?php echo number_format($row['rep_price'],2,'.',' '); ?></td>
						<td><?php echo $row['firstname'].' '.$row['lastname']; ?></td>
					</tr>
					
				<?php } ?>
				<tr>
					<td colspan="6" style="background-color: #8495A5;"></td>
				</tr>
			<?php } ?>
			
			
		</table>
<?php
include 'product/category_tree.php';