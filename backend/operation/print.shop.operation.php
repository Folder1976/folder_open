<?php
include "../config.php";
include "../core.php";
header("Content-Type: text/html; charset=UTF-8");
?>

<?php if(!isset($_GET['operation_id'])) die('Нет номера операции'); ?>
<!-- Sergey Kotlyarov 2017 folder.list@gmail.com -->

<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/backend.css">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/product.css">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/operation.css">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/print.css">
<header>
	<title>Печать <?php echo $_GET['operation_id'];?></title>
</header>

<?php

include "../class/customer.class.php";
$Customer = new Customer();
$postav_list = $postavs = $Customer->getCustomers(4);

include "../class/brand.class.php";
$Brand = new Brand();
$brand_list = $Brand->getBrands();

$size_group_list = $size_groups = $Size->getSizeGroups();

$masters = $Master->getMasters();

$tmp = $Size->getSizes();
$sizes_on_groups = array();
foreach($tmp as $row){
	$sizes_on_groups[$row['group_id']][]= $row;
}

	include "../class/attributes.class.php";
	$Attributes = new Attributes();
	$attributes_group_list = $Attributes->getAttributeGroups();


$types = $Operation->getTypes();

//=====================================================================================================================
//=====================================================================================================================
//=====================================================================================================================
//=====================================================================================================================
	if(isset($_GET['operation_id'])){
		$operation_header = $Operation->getOperation($_GET['operation_id']);

		if(isset($_GET['operation_id']) AND (int)$_GET['operation_id'] > 0){
			$filter_data['operation_id'] = (int)$_GET['operation_id'];
		}		
		$operation_products = $Operation->getShopOperation($filter_data);
		
	}

?>

	
<table class="print_header">
    <tr>
		<td class="right">Номер операции</td>
		<td class="left"><b><?php echo $operation_header['operation_id']; ?></b></td>
		
		<td style="width: 30px;">&nbsp;</td>
		<td class="right">
			<b>Дата</b>
		</td>
		<td class="left"><b><?php echo date('d-m-Y',strtotime($operation_header['date'])); ?>
		</b></td>
	</tr>
	
	<tr>
		<td class="right">
			От куда
		</td>
		<td class="left" colspan="5"><b><?php echo $warehouses[$operation_header['from_warehouse_id']]['name']; ?></b></td>
	</tr>
	
	<tr>
		<td class="right">
			Куда
		</td>
		<td class="left" colspan="5"><b><?php echo $warehouses[$operation_header['to_warehouse_id']]['name']; ?></b></td>
	</tr>
	
	<tr>
		<td class="right">
			Коментарий
		</td>
		<td class="left" colspan="5"><b><?php echo $operation_header['comment']; ?></b></td>
	</tr>
	
</table>
	

<table class="print_body">
    <tr>
        <th>id</th>
        <th>Время</th>
		<th>Хозяин</th>
		<th>Индекс</th>
		<th>ШтрихКод</th>
		<th>Размерная сетка</th>
	    <th>К-во</th>
		<th>Розница</th>
		<th>Сумма</th>
    </tr>
	
	<?php
		$count = 1;
		$total_product = 0;

	?>
	<?php foreach($operation_products as $rows){ ?>
	<?php foreach($rows as $index => $row){ ?>
	<?php
	//echo '<pre>'; print_r(var_dump( $row  ));
	?>
		<tr>
			<td><b><?php echo $count++;?></b></td>
			<td class="mixed"><?php echo date('H:i:s',strtotime($row['date'])); ?></td>
			<td><b><?php echo $masters[$row['master_id']]['name'];?></b></td>
			<td><b><?php echo $row['model'];?></b></td>
			<td><b><?php echo $row['code'];?></b></td>
			<td><b><?php echo ((isset($size_group_name)) ? $size_group_name : '') .' '.$sizes[$row['size_id']]['name'].'';?></b></td>
			<td style="text-align: right;"><b><?php echo $row['quantity']; ?></b></td>
			<td style="text-align: right;"><b><?php echo $row['price_invert']; ?></b></td>
			<td style="text-align: right;"><b><?php echo $row['summ']; ?></b></td>
		</tr>
	<?php
		$total_product += (int)$row['quantity'];
		} ?>
	<?php } ?>
	
	<table class="print_header">
    <tr>
		<td class="right" colspan="5">&nbsp;</td>
	</tr>
	
	<tr>
		<td class="right" colspan="4">Всего товаров</td>
		<td class="right"><b><?php echo $total_product; ?></b></td>
	</tr>
	
    <tr>
		<td class="right" colspan="4">Сумма</td>
		<td class="right"><b><?php echo number_format($operation_header['summ'], 2,'.',''); ?></b></td>
	</tr>
	
</table>
	

</table>
