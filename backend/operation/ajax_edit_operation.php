<?php

include('../../config.php');
include('../config.php');
include('../core.php');
	
	$key = 'exit';
    $table = '';
    $id = '';
	$mainkey = 'id';
	$radio_name = '';
    $data = array();
	$find = array('*1*', '@*@');
	$replace = array('=', '&');
    
foreach($_POST as $index => $value){
    
    //echo '++++    '.$index.'='.$value;
 
	
    if($index == 'key'){
        $key = $value;
    }elseif($index == 'table'){
        $table = $value;
    }elseif($index == 'id'){
        $id = str_replace($find,$replace,$value);
    }elseif($index == 'language_id'){
        $language_id = $value;
    }elseif($index == 'mainkey'){
        $mainkey = $value;
    }elseif($index == 'radio_name'){
        $radio_name = $value;
    }else{
        $data[$index] = str_replace($find,$replace,$value);
    }
}

if(isset($_GET['key']) AND $_GET['key'] == 'find_product'){
	
    $filters = array();
	
	if(isset($_GET['model']) AND $_GET['model'] != ''){
		$filters['filter_model'] = $_GET['model'];
	}
	
	if(isset($_GET['code']) AND $_GET['code'] != ''){
		$filters['filter_code'] = $_GET['code'];
	}
	
	if(isset($_GET['shop_id']) AND $_GET['shop_id'] > 0){
		$filters['filter_shop'] = $_GET['shop_id'];
	}
	
	if(isset($_GET['category_id']) AND $_GET['category_id'] > 0){
		$filters['filter_category'] = $_GET['category_id'];
	}
		
	if(isset($_GET['manufacturer_id']) AND $_GET['manufacturer_id'] > 0){
		$filters['filter_manufacturer'] = $_GET['manufacturer_id'];
	}
	
	if(isset($_GET['warehouse_id']) AND $_GET['warehouse_id'] > 0){
		$filters['filter_warehouse'] = $_GET['warehouse_id'];
	}
	
	if(isset($_GET['operation_find_id']) AND $_GET['operation_find_id'] > 0){
		$filters['operation_find_id'] = $_GET['operation_find_id'];
	}
	
	if(isset($_GET['is_code']) AND $_GET['is_code'] > 0){
		$filters['filter_is_code'] = 1;
	}
	
	$filters['start'] = 0;
	$filters['limit'] = 20;
	
	$products = $Product->getProducts($filters);
	$size_key_list = array();
	?>
	
		<?php if(count($products) == 1 AND isset($_SESSION['auto']) AND (int)$_SESSION['auto'] == 1){ ?>
		<style>
			.add_new_product{
				display: none;
			}
			.product_quantity_wrapper{
				display: block;
				margin-top: 45px;
				top: 0;
				left: 0;
				position: fixed;
				width: calc(100% - 8px);
				font-size: 18px;
				height: 35%;
				overflow: auto;
			}
			.div_size_wrapper{
				position: fixed;
				left: 0;
				width: calc(100% - 8px);
				background-color: cornsilk;
				text-align: center;
				font-size: 30px;
				margin-top: 30px;
				border: 4px solid gray;
				top: 38%;
			}
			.size-box{
				margin: 30px 20px;
				padding: 20px 40px;
				cursor: pointer;
				border-radius: 20px;
				border: 3px solid;
				float: left;
			}
			.size-box:hover{
				background-color: #211000;
				color: white;
			}
			.size-box div{
				font-size: 78px;
				font-weight: bold;
			}
			.size-box input{
				font-size: 28px;
				min-width: 60px;
				left: 30px;
				top: 20px;
				position: relative;
			}
			.find_result_close{
				font-size: 28px;
				background-color: brown;
				padding: 15px 80px;
				border-radius: 20px;
				/* z-index: 99999999; */
				position: absolute;
				margin-left: -100px;
				
			}
			.product_info{
				margin-top: 10px;
				margin-bottom: 10px;
				font-size: 24px;
				text-align: left;
				margin-left: 30px;
				float: left;
			}
			.product_info input{
				font-size: 28px;
				margin-left: 50px;
				width: 200px;
			}
			.add_td_1{
				font-size: 28px;
				background-color: #18A000;
				padding: 40px 80px;
				border-radius: 20px;
				margin-top: -40px;
				position: absolute;
				z-index: 99;
				cursor: pointer;
			}
			.product_quantity_wrapper {
				margin-left: 0;
			}

		</style>
		
				<?php foreach($products as $index => $ex){ ?>
					
					
					<div class="div_size_wrapper">
						
						<div class="product_info" style="width: 74%;">
							<?php echo $ex['model']; ?>
							<?php echo $ex['code']; ?>
							
							
							<?php if($_GET['type_id'] != 8){ ?>
								<input type="text" class="edit add_zakup right" id="add_zakup<?php echo $ex['product_id'];?>" value="<?php echo number_format($ex['last_zakup'],2,'.',''); ?>">
							<?php }else{ ?>
								<input type="hidden" class="edit add_zakup right" id="add_zakup<?php echo $ex['product_id'];?>"value="<?php echo number_format($ex['last_zakup'],2,'.',''); ?>">
							<?php } ?>
							<input type="text" class="edit add_price_invert right" id="add_price_invert<?php echo $ex['product_id'];?>" value="<?php echo number_format($ex['price'],2,'.',''); ?>">
						
						</div>
						
						<div class="product_info" style="width: 21%;">
							<div class="add_product_to_operation add_td_1" data-product_id="<?php echo $ex['product_id']; ?>">Добавить</div>
							<div class="find_result_close" style="margin-top: -100px;z-index:99;margin-left: 0;cursor: pointer;">[Закрыть]</div>
							
						
						</div>
						
						<div style="clear: both;"></div>
						
						<div class="total_quantity" id="total_quantity<?php echo $ex['product_id']; ?>"></div>
						
							<?php
							$ProductsOnWare = $Warehouse->getProductsOnWareOnTable($ex['product_id']);
							?>
						
							<div class="add_size_wrapper<?php echo $ex['product_id']; ?>">
								
								<?php $quantity = 0; ?>
								<?php $size_group_id = $ex['size_group_id'] ; ?>
									
								<?php if($size_group_id > 0){ ?>
									<!--b style="font-size: 20px;"><?php echo $size_groups[$size_group_id]['name']; ?></b-->
									
									<div class="size-box" >
										<img class="product_image" src="<?php echo '/image/'.$ex['image'];?>">
									</div>
									
									<?php foreach($sizes_on_groups[$size_group_id] as $size_id => $value){?>
								   
										<?php if(!in_array($value['size_id'], $ProductsOnWare['size_ids'])
												 and $_GET['type_id'] != 1) continue; ?>
										
										<div class="size-box" >
											<div style="margin: 20px;"><?php echo $value['name'];?></div>
											<input type="text"
												   required
												   class="add_size"
												   id="add_size*<?php echo $ex['product_id'].'*'.$value['size_id']; ?>"
												   data-row_id="<?php echo $index;?>"
												   data-size_id="<?php echo $value['size_id']; ?>"
												   data_product_id="<?php echo $ex['product_id']; ?>"
												   
												   value="<?php echo $quantity;?>">
										</div>
									
									<?php } ?>
								<?php }else{ ?>
								
									<?php  //$size = array_shift($ex['sizes']);
										//$zakup = $size['zakup'];
									?>
									<div class="size-box">
											<div>Без размера</div>
											<input type="text"
												   required
												   class="add_size"
												   id="add_size*<?php echo $ex['product_id'].'*0'; ?>"
												   data-row_id="<?php echo $index;?>"
												   data-size_id="0"
												   data_product_id="<?php echo $ex['product_id']; ?>"
												   value="1">
										</div>
									<?php $auto_add = true; ?>
								<?php } ?>
							</div>
							
							<div class="product_quantity_wrapper" style="margin-top: 45px;">
							
								<?php echo $ProductsOnWare['html']; ?>
							
							</div>
					</div>
					
				<?php } ?>
				
				<?php if(isset($auto_add) AND $auto_add){ ?>
						<script>
							
							$(document).ready(function(){
								console.log('1+');
								//$('.add_product_to_operation').trigger('click');
							});
						</script>
						
				<?php } ?>

	<?php }else{ ?>
	
	
	<h2 style="margin-top: 10px;">Результат поиска &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<!--a href="javascript:;" class="find_result_close">[Закрыть]</a></h2-->
		<!--input type="hidden" id="operation_id"  value="<?php echo $_GET['operation_id'];?>"-->

			<div style="width: 100%;margin-left: 0px;">
				<div class="table_body">
				<table class="text find_result" style="width: 94%;margin-left: 3%;margin-right: 3%;">
					<tr>
						<th>#</th>
						<th>Категория</th>
						<th>Индекс</th>
						
						<th>ШтрихКод</th>
						<th>Фото</th>
						<th colspan="2">Количество</th>
						<?php if($_GET['type_id'] != 8){ ?><th>Закуп</th><?php } ?>
						<th>Цена</th>
						<th>* * *</th>
					</tr>
					
					<?php $count = 1; ?>
					<?php foreach($products as $index => $ex){ ?>
					
					<tr class="selectable_row <?php if(count($products) > 1) echo ' link '; ?>  res<?php echo $ex['product_id']; ?>"
								id="<?php echo $ex['product_id']; ?>"
								data-row_id="<?php echo $count++;?>"
								style="height: 65px;">
						<td><?php echo $ex['product_id']; ?></td>
						<?php $category_info = $Category->getCategory($ex['category_id']);?>
						<td><?php echo $category_info['path']; ?></td>
						<td class="mixed"><?php echo $ex['model']; ?> *
			<?php echo $ex['model7']; ?>/<?php echo $ex['model8']; ?>/<?php echo $ex['model4']; ?>
						
						</td>
						<td class="mixed"><?php echo $ex['code']; ?></td>
						<td><img class="product_image" src="<?php echo '/image/'.$ex['image'];?>"></td>
						
						<td class="left size_wrapper add_size_wrapper<?php echo $ex['product_id']; ?>">
						<div class="div_size_wrapper">
							
							<?php if(count($products) == 1 AND isset($_SESSION['auto']) AND (int)$_SESSION['auto'] == 1){ ?>
								<?php echo $ex['model']; ?>
								<?php echo $ex['code']; ?>
								<img class="product_image" src="<?php echo '/image/'.$ex['image'];?>">
							<?php } ?>
							
							<?php
							$ProductsOnWare = $Warehouse->getProductsOnWareOnTable($ex['product_id']);
							?>
							
							<?php $quantity = 0; ?>
							<?php $size_group_id = $ex['size_group_id'] ; ?>
								
							<?php if($size_group_id > 0){ ?>
								<b><?php echo $size_groups[$size_group_id]['name']; ?></b><br>
								
								<?php foreach($sizes_on_groups[$size_group_id] as $size_id => $value){?>
							   
									<?php if(!in_array($value['size_id'], $ProductsOnWare['size_ids'])
											 and $_GET['type_id'] != 1) continue; ?>
									
									<div class="size-box">
										<div><?php echo $value['name'];?></div>
										<input type="text"
											   required
											   class="add_size"
											   id="add_size*<?php echo $ex['product_id'].'*'.$value['size_id']; ?>"
											   data-row_id="<?php echo $index;?>"
											   data-size_id="<?php echo $value['size_id']; ?>"
											   data_product_id="<?php echo $ex['product_id']; ?>"
											   
											   value="<?php echo $quantity;?>">
									</div>
								
								<?php } ?>
							<?php }else{ ?>
							
								<?php  //$size = array_shift($ex['sizes']);
									//$zakup = $size['zakup'];
								?>
								<div class="size-box">
										<div>Без размера</div>
										<input type="text"
											   required
											   class="add_size"
											   id="add_size*<?php echo $ex['product_id'].'*0'; ?>"
											   data-row_id="<?php echo $index;?>"
											   data-size_id="0"
											   data_product_id="<?php echo $ex['product_id']; ?>"
											   value="<?php echo $quantity;?>">
									</div>
							<?php } ?>
							
							<div class="product_quantity_wrapper" style="margin-top: 45px;">
							
								<?php echo $ProductsOnWare['html']; ?>
							
							</div>
						</div>
						</td>
						<!--td class="mixed"><?php echo number_format($ex['last_zakup'], 2, '.', ''); ?></td>
						<td class="mixed"><?php echo number_format($ex['last_price_invert'], 2, '.', ''); ?></td-->
						
						<td class="total_quantity" id="total_quantity<?php echo $ex['product_id']; ?>"></td>
						
						<?php if($_GET['type_id'] != 8){ ?>
							<td class="right"><input type="text" class="edit add_zakup right" id="add_zakup<?php echo $ex['product_id'];?>" style="width:70px;" value="<?php echo number_format($ex['last_zakup'],2,'.',''); ?>"></td>
							<td class="right">
						<?php }else{ ?>
							<td class="right">
							<input type="hidden" class="edit add_zakup right" id="add_zakup<?php echo $ex['product_id'];?>" style="width:70px;" value="<?php echo number_format($ex['last_zakup'],2,'.',''); ?>">
						<?php } ?>
						
						<input type="text" class="edit add_price_invert right" id="add_price_invert<?php echo $ex['product_id'];?>" style="width:70px;" value="<?php echo number_format($ex['price'],2,'.',''); ?>"></td>
					
						<td class="add_product_to_operation add_td">+</td>
					</tr>
					<?php } ?>
					
				</table>
				</div>
			</div>
	
	<?php } ?>
			
	<style>
		.add_td{
			background-color: #BBF78A;
			color: black;
			font-weight: bold;
			font-size: 32px;
			cursor: pointer;
			text-align: center;
		}
		
	</style>
		
	
<?php
}elseif($key == 'get_sizes' OR (isset($_GET['key']) AND $_GET['key'] == 'get_sizes')){

		if(!isset($_GET['product_id'])) $_GET['product_id'] = 0;
		if(!isset($ex['product_id'])) $ex['product_id'] = 0;
		
		$size_group_id = (int)$_GET['size_group_id'] ;
		
		$index = 0;
		$quantity = 0
		?>
						
					<?php if($size_group_id > 0){ ?>
						<b><?php echo $size_groups[$size_group_id]['name']; ?></b><br>
						
						<?php foreach($sizes_on_groups[(int)$size_group_id] as $size_id => $value){?>
					   
							
							
							<div class="size-box">
								<span><?php echo $value['name'];?></span>
								<input type="text"
									   required
									   class="add_size"
									   id="add_size*<?php echo $ex['product_id'].'*'.$value['size_id']; ?>"
									   data-row_id="<?php echo $index;?>"
									   data-size_id="<?php echo $value['size_id']; ?>"
									   data_product_id="<?php echo $ex['product_id']; ?>"
									   
									   value="<?php echo $quantity;?>">
							</div>
						
						<?php } ?>
					<?php }else{ ?>
					
						<?php  //$size = array_shift($ex['sizes']);
							//$zakup = $size['zakup'];
						?>
						<div class="size-box">
								<span>Без размера</span>
								<input type="text"
									   required
									   class="add_size"
									   id="add_size*<?php echo $ex['product_id'].'*0'; ?>"
									   data-row_id="<?php echo $index;?>"
									   data-size_id="0"
									   data_product_id="<?php echo $ex['product_id']; ?>"
									   value="<?php echo $quantity;?>">
							</div>
					<?php } 
	
}elseif($key == 'add_new_product'){

	
	$data['operation_id'] = $data['operation_id'];
	$data['sub_operation_id'] = isset($data['sub_operation_id']) ? $data['sub_operation_id'] : 0;
	$data['product_id'] = $data['product_id'];
	$data['size_id'] = $data['size_id'];
	$data['quantity'] = $data['quantity'];
	$data['currency_id'] = $data['currency_id'];
	$data['type_id'] = $data['type_id'];
	$data['customer_id'] = $data['customer_id'];
	$data['master_id'] = $data['master_id'];
	$data['zakup'] = $data['zakup'];
	$data['price_invert'] = $data['price_invert'];
	
	echo $Operation->addProduct($data);

}elseif($key == 'add_new_operation'){
	
	echo $Operation->addOperation($data);

	
}elseif($key == 'edit_zakup_prihod'){
    
	
	$sql = 'UPDATE ' . DB_PREFIX . 'operation_product
				SET
				zakup = "'.$data['new_zakup'].'"
				WHERE
				operation_id = "'.$data['operation_id'].'" AND
				product_id = "'.$data['product_id'].'" AND
				master_id = "'.$data['master_id'].'" AND
				zakup = "'.$data['zakup'].'" 
				';
	//echo $sql;
	$mysqli->query($sql) or die('sa1423'.$sql);
	
	echo $Operation->updateOperationSumm($data['operation_id']);

	
}elseif($key == 'edit_master_prihod'){
    
	
	$sql = 'UPDATE ' . DB_PREFIX . 'operation_product
				SET
				master_id = "'.$data['new_master_id'].'" 
				WHERE
				operation_id = "'.$data['operation_id'].'" AND
				product_id = "'.$data['product_id'].'" AND
				master_id = "'.$data['master_id'].'" AND
				zakup = "'.$data['zakup'].'" 
				';
	//echo $sql;
	$mysqli->query($sql) or die('sa1423'.$sql);
	
	//echo $Operation->updateOperationSumm($data['operation_id']);
	$Operation->updateWarehouseItems($data['product_id']);

	
}elseif($key == 'get_product'){

	$product = $Product->getProduct((int)$data['product_id']);
	
	$product['category'] = $Category->getCategory($product['category_id']);
	
	$product['attributes'] = $Product->getAttributes((int)$data['product_id']);
		
	echo json_encode($product);


}elseif($key == 'edit_products'){
    
	
	$sql = 'UPDATE ' . DB_PREFIX . 'operation_product
				SET
				price_invert = "'.$data['price_invert'].'",
				master_id = "'.$data['master_id'].'",
				customer_id = "'.$data['customer_id'].'",
				currency_id = "'.$data['currency_id'].'"
				WHERE
				operation_id = "'.$data['operation_id'].'" AND
				product_id = "'.$data['product_id'].'" AND
				zakup = "'.$data['zakup'].'" AND
				master_id = "'.$data['master_id'].'"
				';
	//echo $sql;
	$mysqli->query($sql) or die('sa1423'.$sql);
	
	//Он не влияет на сумму операции
	//echo $Operation->updateOperationSumm($data['operation_id']);
	
}elseif($key == 'dell_sub_operation'){
  	
	$Operation->dellSubOperation($data['operation_id'], $data['sub_operation_id']);
	$Operation->updateOperationSumm($data['operation_id']);
	
}elseif($key == 'dell_operation'){
  	
	$Operation->dellOperation($id);
	
}elseif($key == 'set_beznal_status'){
  	
	$Operation->setBesnalStatus($data);
	
}elseif($key == 'add_kredit'){
    
	/*
	$data['operation_id']
	$data['sub_operation_id']
	$data['customer_id']
	$data['to_warehouse_id']
	$data['comment']
	$data['type_id']
	*/		
	
	//Копируем шапку операции с подменой некоторых значений	
	$operation = $Operation->getOperation($data['operation_id']);
	$operation['customer_id'] = (int)$data['customer_id'];
	$operation['to_warehouse_id'] = (int)$data['to_warehouse_id'];
	$operation['comment'] = $data['comment'];
	$operation['sub_operation_id'] = 0;
	$operation['type_id'] = (int)$data['type_id'];
	$new_operation_id = $Operation->addOperation($operation);
	
	//Перенесем товары из чека в новую операцию.
	$sql = 'UPDATE ' . DB_PREFIX . 'operation_product
				SET
				operation_id = "'.$new_operation_id.'",
				sub_operation_id = "0",
				customer_id = "'.$data['customer_id'].'",
				type_id = "'.(int)$data['type_id'].'"
				WHERE
				operation_id = "'.$data['operation_id'].'" AND
				sub_operation_id = "'.$data['sub_operation_id'].'" 
				';
	//echo $sql;
	$mysqli->query($sql) or die('sa1423'.$sql);
	
	
	
	$Operation->updateOperationSumm($data['operation_id']);
	$Operation->updateOperationSumm($new_operation_id);
	
	
	if((int)$data['kredit_money'] > 0){
		
		$data['operation_id']  = $new_operation_id;
		$data['zdacha']  = 0;
		$data['oplat_summ'] = $data['oplat_money']  = (int)$data['kredit_money'];
		
		echo ', оплата:'.$Operation->addOplat($data);
	}

	
}elseif($key == 'add_new_oplata'){    
	
	
	if((int)$data['summ'] > 0){
		
		$data['operation_id']  = 1;
		$data['sub_operation_id']  = 1;
		$data['zdacha']  = 0;
		$data['oplat_summ'] = $data['oplat_money']  = (int)$data['summ'];
		
		echo ', оплата:'.$Operation->addOplat($data);
	}

	
}elseif($key == 'dell_row'){
    
	/*
	$data['operation_id']
	$data['sub_operation_id']
	$data['product_id']
	$data['zakup']
	*/		
	$Operation->dellProductRow($data);
	
	echo $Operation->updateOperationSumm($data['operation_id']);

	
}elseif($key == 'get_sub_operation_summ'){

	//$data['operation_id'];
	//$data['sub_operation_id']

	echo $Operation->getSubOperationSumm($data);
	
}elseif($key == 'edit_quantity'){
	
	/* $data['operation_id']
	 * $data['sub_operation_id']
	 * $data['product_id']
	 * $data['zakup']
	 * $data['quantity']
	 * $data['size_id']
	 */
	$Operation->updateProductQuantity($data);
	
	echo $Operation->updateOperationSumm($data['operation_id']);
}

?>