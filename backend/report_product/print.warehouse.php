<?php
include "../config.php";
include "../core.php";
header("Content-Type: text/html; charset=UTF-8");

?>

<?php //if(!isset($_GET['operation_id'])) die('Нет номера операции'); ?>
<!-- Sergey Kotlyarov 2017 folder.list@gmail.com -->

<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/backend.css">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/product.css">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/operation.css">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/print.css">
<header>
	<title>Печать Остатков</title>
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
	/*
	if(isset($_GET['operation_id'])){
		$operation_header = $Operation->getOperation($_GET['operation_id']);
		
		if(isset($_GET['sub_operation_id']) AND (int)$_GET['sub_operation_id'] > 0){
			$operation_products = $Operation->getOperationProducts($_GET['operation_id'], $_GET['sub_operation_id']);
		}else{
			$operation_products = $Operation->getOperationProducts($_GET['operation_id']);	
		}
		
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
	*/
	
	$operation_products_grp = array();
	
		$sql = 'SELECT product_id, model, model7, model8, model4, code, price, zakup FROM '.DB_PREFIX.'product';
		
		if(isset($_GET['code']) AND $_GET['code'] != ''){
			
			if(isset($_GET['is_code']) AND $_GET['is_code'] == 1){
				$sql .= ' WHERE code LIKE "'.$_GET['code'].'" ';
			}else{
				$sql .= ' WHERE code LIKE "%'.$_GET['code'].'%" ';	
			}
			
		}
		
		$sql .= ' ORDER BY model';
		$r = $mysqli->query($sql) or die($sql);
		
		if($r->num_rows){
			
			while($row = $r->fetch_assoc()){
				
				$ware = $Warehouse->getProductsOnWareOnTableArray($row['product_id']);
				
				if(count($ware) == 0) continue;
				
				foreach($ware as $master_id => $value){
					$master_ids[$master_id] = $master_id;
					foreach($value as $warehouse_id => $value2){
						$warehouse_ids[$warehouse_id] = $warehouse_id;
						foreach($value2 as $size_id => $quantity){
							$size_ids[$size_id] = $size_id;
							
							
							
							$operation_products_grp[$row['product_id']]['product'] = $row;
							$operation_products_grp[$row['product_id']]['warehouse'][$warehouse_id][$master_id][$size_id] = $quantity;
							
							
						}
					}
				}
			}
			
		}
	
	ksort($operation_products_grp);
?>

	
<table class="print_header">
    <tr>
		<td class="right">Наличие по складу</td>
		<td class="left"><b><?php echo $warehouses[(int)$_GET['warehouse_id']]['name']; ?></b></td>
		
		<td style="width: 30px;">&nbsp;</td>
		<td class="right">
			<b>Дата</b>
		</td>
		<td class="left"><b><?php echo date('Y-m-d H:i:s'); ?>
		</b></td>
	</tr>
	
	<tr>
		<td class="right">
			Кто распечатал
		</td>
		<td class="left" colspan="5"><b><?php echo $users[$_SESSION['default']['user_id']]['firstname'].' '.$users[$_SESSION['default']['user_id']]['lastname']; ?></b></td>
	</tr>

	
</table>


<table class="print_body">
    <tr>
        <th>id</th>
        <th>Хозяин</th>
		<th>Индекс</th>
		<th>ШтрихКод</th>
		<th>Размерная сетка</th>
	    <th>К-во</th>
	    <th>Закуп</th>
		<th>Розница</th>
		<th>Сумма</th>
    </tr>
	
	<?php
		$count = 1;
		$total_product = 0;
		$total = 0;
	?>
	<?php foreach($operation_products_grp as $index => $row){ ?>
	
	
		<?php if(isset($row['warehouse'][(int)$_GET['warehouse_id']])){
		
		
		foreach($row['warehouse'][(int)$_GET['warehouse_id']] as $master_id => $sizes){
		
			
				$size_group_id = $Size->getProductSizeStandart($row['product']['product_id']);
			
				$row_summ = 0;
				$size_group_name = 'Без размера';
				$print_row_show = 1;
				$print_row = '';
					
					if($size_group_id > 0){ 
						$print_row .= '';
						$size_group_name = $size_groups[(int)$size_group_id]['name'];
						
						foreach($sizes_on_groups[$size_group_id] as $size_id => $value){ 
						
							//if(!in_array($value['size_id'], $ProductsOnWare['size_ids']) and $type_id != 1) continue; 
														
							if(isset($sizes[$value['size_id']])){
								$quantity = (int)$sizes[$value['size_id']];
							}else{
								$quantity = 0;
							}
							
							if($quantity > 0){
								$print_row .= '<tr class="size_row"><td colspan="4"></td>
								 <td style="text-align: right;"><b>';
								$print_row .= ''.$value['name'].'</b></td>
								<td style="text-align: left;"> '.$quantity.'</td>';
								$print_row .= '<td colspan="3"></td></tr>';
							}
							$row_summ += (int)$quantity;
							$total_product += (int)$quantity;
						} 
					}else{
						
						$size = array_shift($sizes);
						$zakup = $row['product']['zakup'];
					
						$print_row_show = 0;
						$row_summ += (int)$size;
						$total_product += (int)$size;
					} 
			?>
		
			<tr>
				<td><b><?php echo $count++;?></b></td>
				<td><b><?php echo $masters[$master_id]['name'];?></b></td>
				<td><b><?php echo $row['product']['model'];?></b></td>
				<td><b><?php echo $row['product']['code'];?></b></td>
				<td><b><?php echo $size_group_name;?></b></td>
				<td style="text-align: right;"><b><?php echo $row_summ; ?></b></td>
				<td style="text-align: right;"><b>
					<?php echo number_format($row['product']['zakup'], 2,'.','')
						.' '.$currencys[$row['product']['currency_id']]['symbol_right']
					; ?>
							
					</b></td>
				<td style="text-align: right;"><b><?php echo number_format($row['product']['price'], 2,'.',''); ?></b></td>
				<td style="text-align: right;"><b><?php echo number_format($row['product']['price'] * $row_summ, 2,'.',''); ?></b></td>
			</tr>
			
			<?php $total += $row['product']['price'] * $row_summ; ?>
			
			<?php if($print_row_show) echo $print_row; ?>
		
		<?php }} ?>
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
		<td class="right"><b><?php echo number_format($total, 2,'.',''); ?></b></td>
	</tr>
	
</table>
	

</table>
