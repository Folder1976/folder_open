<?php
session_start();

include('../../config.php');
include('../config.php');
include('../core.php');

include ('../class/attributes.class.php');
$Attributes = new Attributes();
	
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



if($key == 'add' and (int)$id > 0){
	
	$sql = "UPDATE " . DB_PREFIX . $table . " SET ";
		$sql .= "`model` = '".$data['model']."',
				`model7` = '".$data['model7']."',
				`model8` = '".$data['model8']."',
				`model4` = '".$data['model4']."',
				`code` = '".$data['code']."',
				`price` = '".$data['price']."',
				`zakup` = '".$data['zakup']."',
				`zakup_currency_id` = '".$data['zakup_currency_id']."',
				`size_group_id` = '".$data['size_group_id']."',
				`sort_order` = '".$data['sort_order']."',
				`manufacturer_id` = '".$data['manufacturer_id']."',
				`status` = '".$data['status']."'";
	$sql .=	" WHERE `$mainkey` = '" . (int)$id . "'";
//echo $sql;
	$mysqli->query($sql) or die('sadlkjgfljsad bf;j '.$sql);

	$sql = "DELETE FROM " . DB_PREFIX . "product_to_category WHERE `$mainkey` = '" . (int)$id . "'";
	$mysqli->query($sql) or die('sadlkjgfljsad bf;j '.$sql);

	$sql = "DELETE FROM " . DB_PREFIX . "product_attribute WHERE `$mainkey` = '" . (int)$id . "'";
	$mysqli->query($sql) or die('sadlkjgfljsad bf;j '.$sql);

	
	$sql = "INSERT INTO " . DB_PREFIX . "product_to_category SET product_id='".(int)$id."', category_id='".$data['category_id']."'";
	$mysqli->query($sql) or die('sadsdfgad bf;j '.$sql);
	
	echo $id;

}elseif($key == 'add'){
    
	$sql = "INSERT INTO " . DB_PREFIX . $table . " SET ";
	$sql .= "`model` = '".$data['model']."',
				`model7` = '".$data['model7']."',
				`model8` = '".$data['model8']."',
				`model4` = '".$data['model4']."',
				`code` = '".$data['code']."',
				`price` = '".$data['price']."',
				`sort_order` = '".$data['sort_order']."',
				`zakup` = '".$data['zakup']."',
				`zakup_currency_id` = '".$data['zakup_currency_id']."',
				`size_group_id` = '".$data['size_group_id']."',
				`manufacturer_id` = '".$data['manufacturer_id']."',
				`status` = '".$data['status']."'";
//echo $sql;
	$mysqli->query($sql) or die('sawreq444j '.$sql);
	
	$product_id = $mysqli->insert_id;
	
	$sql = "INSERT INTO " . DB_PREFIX . "product_description SET product_id='$product_id', language_id='1'";
	$mysqli->query($sql) or die('sad54yfdfsg '.$sql);
	
	$sql = "INSERT INTO " . DB_PREFIX . "product_to_category SET product_id='$product_id', category_id='".$data['category_id']."'";
	$mysqli->query($sql) or die('sadsdfgad bf;j '.$sql);
	
	$sql = "INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id='$product_id', store_id='0', layout_id='0'";
	$mysqli->query($sql) or die('sad54dsfd bf;j '.$sql);
	
	$sql = "INSERT INTO " . DB_PREFIX . "product_to_store SET product_id='$product_id', store_id='0'";
	$mysqli->query($sql) or die('sad5rbf;j '.$sql);
	
	$Product->saveNewPrice($product_id, $data, 'new');
	
	echo $product_id;
	
}elseif($key == 'edit'){
    
	$Product->saveNewPrice((int)$id, $data);
	
	$sql = "UPDATE " . DB_PREFIX . $table . " SET ";
		$sql .= "`model` = '".$data['model']."',
				`model7` = '".$data['model7']."',
				`model8` = '".$data['model8']."',
				`model4` = '".$data['model4']."',
				`code` = '".$data['code']."',
				`price` = '".$data['price']."',
				`zakup` = '".$data['zakup']."',
				`zakup_currency_id` = '".$data['zakup_currency_id']."',
				`sort_order` = '".$data['sort_order']."',
				`size_group_id` = '".$data['size_group_id']."',
				`manufacturer_id` = '".$data['manufacturer_id']."',
				`status` = '".$data['status']."'";
	$sql .=	" WHERE `$mainkey` = '" . (int)$id . "'";
//echo $sql;
	$mysqli->query($sql) or die('sadlkjgfljsad bf;j '.$sql);

	$sql = "DELETE FROM " . DB_PREFIX . "product_to_category WHERE `$mainkey` = '" . (int)$id . "'";
	$mysqli->query($sql) or die('sadlkjgfljsad bf;j '.$sql);
	
	$sql = "INSERT INTO " . DB_PREFIX . "product_to_category SET ";
	$sql .= "`category_id` = '".$data['category_id']."', `$mainkey` = '" . (int)$id . "'";
//echo $sql;
	$mysqli->query($sql) or die('sadlkjgfljsad bf;j '.$sql);
		
}elseif($key == 'dell' AND isset($id) AND is_numeric($id)){
	
	$Product->dellProduct((int)$id);
		//$Product->dellImages();
	
}elseif($key == 'dell_filters' AND isset($id) AND is_numeric($id)){

	$sql = "DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '".(int)$id."' ";
	
	$mysqli->query($sql) or die('sad54yfljsosdfhg;adbf;j '.$sql);

}elseif($key == 'dell_attribute'){

	$sql = "DELETE FROM " . DB_PREFIX . "product_attribute WHERE
				product_id = '".(int)$data['product_id']."'
				AND attribute_id = '".(int)$data['attribute_id']."' ";
	
	$mysqli->query($sql) or die('sad54yfljsosdfhg;adbf;j '.$sql);

}elseif($key == 'add_attribute_to_tovar'){

	$data['name'] = trim($data['name']);

	//Определим по названию	
	if($data['attribute_id'] == 0){
		
		$sql = 'SELECT * FROM `'.DB_PREFIX.'attribute_group` AG
					LEFT JOIN `'.DB_PREFIX.'attribute_group_description` AGD ON AG.attribute_group_id = AGD.attribute_group_id
					WHERE `enable` = "1" AND `name` LIKE "'.$data['name'].'"';
		
		if($data['attribute_group_id'] > 0){
			$sql .= ' AND AG.attribute_group_id = "'.$data['attribute_group_id'].'"';
		}
		
		$sql .=  ' LIMIT 1;';
		
		$r = $mysqli->query($sql) or die($sql);
		
		$return = array();
		if($r->num_rows > 0){
			
			$row = $r->fetch_assoc();
			$data['attribute_id'] = (int)$row['attribute_id'];
			
		}elseif($data['attribute_group_id'] > 0){
			
			$data['language_id'] = 1;
			$data['filter_name'] = '';
			$data['sort_order'] = 0;
			$data['enable'] = 1;
			$data['attribute_type'] = 1;
			
			$data['attribute_id'] = $Attributes->addAttribute($data);
			
		}
		
		
	}
	
	if((int)$data['attribute_id'] > 0){
		
		if(is_numeric($data['product_id']) AND (int)$data['product_id'] > 0){
	
			$sql = 'INSERT INTO ' . DB_PREFIX . 'product_attribute SET
						product_id="'.$data['product_id'].'",
						attribute_id="'.$data['attribute_id'].'",
						language_id = "1",
						`text`="",
						`value`="",
						attribute_value_id=0
						';
						
			$mysqli->query($sql) or die('sad54yfljsosdfhg;adbf;j '.$sql);
	
		}
	
		$attribute_info = $Attributes->getAttribute((int)$data['attribute_id']); 
	
		if($attribute_info){
			
			$data_attribute = array(
						'product_id' => $data['product_id'],
						'attribute_id' => $attribute_info['attribute_id'],
						'group_name' => $attribute_info['group_name'],
						'name' => $attribute_info['name']
								);
			$Attributes->echoAttributeList1($data_attribute);
			
		}
	
	}
	

}elseif($key == 'make_model'){
		
		
	$product = $Product->getProduct($id);
	$attributes = $Product->getAttributes($id);
		
	foreach($attributes as $index => $row){
		$attr[$row['attribute_group_id']] = $row['index'];
	}
	
	$model = '';
	
	if($product['category_id'] < 10){
		$model .= '0'.$product['category_id'].'-';
	}else{
		$model .= $product['category_id'].'-';
	}
	
	//Сезон
	if(isset($attr[3])){
		$model .= $attr[3].'-';	
	}else{
		$model .= '0-';
	}
	
	//Цвет
	if(isset($attr[1])){
		if($attr[1] < 10){
			$model .= '0'.$attr[1].'-';
		}else{
			$model .= $attr[1].'-';	
		}
		
	}else{
		$model .= '0-';
	}
	
	$sql = 'SELECT model FROM ' . DB_PREFIX . 'product WHERE model LIKE "'.$model.'%"';
	$r = $mysqli->query($sql) or die('sad54yfljsosdfhg;adbf;j '.$sql);
	
	$count = 1;
	
	if($r->num_rows){
		
		while($row = $r->fetch_assoc()){
			
			$indexs[] = (int)str_replace($model,'',$row['model']);
		}
		
		while($count++){
			
			if(!in_array($count, $indexs)){
				break;
			}

		}
	}else{
		$count = 1;
	}
	
	$model .= $count;
	
	echo $model;
	
	$sql = 'UPDATE ' . DB_PREFIX . 'product SET model = "'.$model.'" WHERE product_id = "'.$id.'"';
	$mysqli->query($sql) or die('sad54y3dbf;j '.$sql);
	
			
}elseif($key == 'get_attribute_list'){
	

	$attribute_list = $Attributes->getAttributes((int)$data['attribute_group_id']); ?>
	
	<div class="div_attribute_list_close">
		<a href="javascript:;" class="attribute_list_close">
			<img src="/backend/img/cancel.png" title="удалить" width="18" height="18">
		</a>
	</div>
	
	<div class="div_attribute_list_move handler_attribute_lisr" style="cursor: move;" >
		<a href="javascript:;" class="attribute_list_move">
			<img src="/backend/img/move36.png" title="Переместить" width="18" height="18" style="cursor: move;">
		</a>
	</div>
	
	<?php foreach($attribute_list as $index => $row){ ?>
		
		<a href="javascript:;"
			class="select_attribute"
			data-product_id="<?php echo $data['product_id']; ?>"
			data-attribute_id="<?php echo (int)$index; ?>">
			<?php echo $row['name']; ?></a><br>
		
	<?php }
	
}elseif($key == 'set_reprice'){
	
		$array = array();
		$product_id = (int)$_POST['product_id'];
		$price = (float)$_POST['price'];
	
		if(isset($_POST['array']) AND $_POST['array'] != ''){
			
			$rows = explode('|', trim($_POST['array'],'|'));
			
			foreach($rows as $row){
				list($master_id, $warehouse_id, $size_id) = explode("*", $row);
				
				if(isset($array[$master_id][$warehouse_id][$size_id])){
					$array[$master_id][$warehouse_id][$size_id]++;
				}else{
					$array[$master_id][$warehouse_id][$size_id] = 1;
				}
			}
			
		
		
			//Переоцениваем и перемещаем товар
			foreach($array as $master_id => $values){
				foreach($values as $warehouse_id => $values2){
					foreach($values2 as $size_id => $quantity){
						
						$sql = 'SELECT code, price FROM '.DB_PREFIX.'product WHERE product_id="'.$product_id.'" LIMIT 1';
						$r = $mysqli->query($sql) or die('sad54yfljsosdfhg;adbf;j '.$sql);
						if($r->num_rows){
							
							$row = $r->fetch_assoc();
							
							//Цена не менялась
							
							if($price == $row['price']) continue;
							
							echo 'Цена не совпадает '.$price .'=='. $row['price'];
							
							//Проверим или такой товар уже есть
							$sql = 'SELECT product_id, code, price FROM '.DB_PREFIX.'product WHERE code="'.$row['code'].'@'.(float)$price.'" LIMIT 1';
							$r = $mysqli->query($sql) or die('sad54yfljsosdfhg;adbf;j '.$sql);
							
							if($r->num_rows){
								
								$row1 = $r->fetch_assoc();
								$new_product_id = (int)$row1['product_id'];
								echo 'Продукт существует';
							}else{
								
								$new_product_id = $Product->copyProduct($product_id);
								
								if($new_product_id){
									
									$sql = 'UPDATE '.DB_PREFIX.'product SET
											code="'.$row['code'].'@'.(float)$price.'"
											WHERE product_id="'.$new_product_id.'"';
											
									$mysqli->query($sql) or die('sad54f;j '.$sql);
									
									//Впишем новыую цену
									$data['price'] = $price;
									$Product->saveNewPrice($new_product_id, $data);
									
								}
								echo 'Создал новый продукт - '.$row['code'].'@'.(float)$price;
							}
							
							if($product_id > 0 AND $new_product_id > 0){
								
								echo ' Создаем перемещение';
								
								$data = array();
								$data['operation_id'] = 0;
								$data['sub_operation_id'] = 0;
								$data['product_id'] = $product_id;
								$data['size_id'] = $size_id;
								$data['quantity'] = $quantity;
								$data['currency_id'] = 4; //Гривна
								$data['type_id'] = 1; //Скидываем все на приход
								$data['customer_id'] = 4;//Семен голопупенко
								$data['master_id'] = $master_id;
								$data['zakup'] = 0;
								$data['price_invert'] = $price;
								$data['from_warehouse_id'] = $warehouse_id;
								$data['to_warehouse_id'] = 0;
								
								$Operation->addProduct($data);
								$Operation->updateWarehouseItems($product_id);
								
								$data['product_id'] = $new_product_id;
								$data['from_warehouse_id'] = 0;
								$data['to_warehouse_id'] = $warehouse_id;
								$Operation->addProduct($data);
								$Operation->updateWarehouseItems($new_product_id);
								
								
							
								
							}
							
						}
						
						
						
					}
				}
			}
			
		}
}elseif($key == 'get_selected_sizes'){
	
		$array = array();
	
		if(isset($_POST['array']) AND $_POST['array'] != ''){
			
			$rows = explode('|', trim($_POST['array'],'|'));
			
			foreach($rows as $row){
				list($master_id, $warehouse_id, $size_id) = explode("*", $row);
				
				if(isset($array[$master_id][$warehouse_id][$size_id])){
					$array[$master_id][$warehouse_id][$size_id]++;
				}else{
					$array[$master_id][$warehouse_id][$size_id] = 1;
				}
			}
			
		}
	
		global $warehouses, $masters, $sizes;
		
		$size_ids = array();
		
		$html = '<table class="product_qnt selected_sizes">';
		
		foreach($array as $master_id => $values){
			
			$html .= '<tr><th colspan="2">'.$masters[$master_id]['name'].'</th></tr>';
			
			foreach($values as $warehouse_id => $values2){

				if($warehouse_id != 0){
			
					if($warehouse_id > 0){
						$html .= '<tr><td style="max-width:200px;">'.$warehouses[$warehouse_id]['name'].'</td><td>';
					}else{
						$html .= '<tr><td>В пути на маг.'.$warehouses[$warehouse_id]['name'].'</td><td>';
					}
				
					foreach($values2 as $size_id => $quantity){
							
							if((int)$size_id > 0){
								$html .= '[<b class="product_size minus" data-master_id="'.$master_id.'" data-size_id="'.$size_id.'" data-warehouse_id="'.$warehouse_id.'">'.$sizes[$size_id]['name'].'</b>] : ';
							}else{
								$html .= '[<b class="product_size minus" data-master_id="'.$master_id.'" data-size_id="0" data-warehouse_id="'.$warehouse_id.'">Без размера</b>] : ';
							}
						
						$size_ids[$size_id] = $size_id;
						
						if($quantity > 0){
							$html .= '<span class="qnt_plus">'.$quantity.'</span>&nbsp;&nbsp;';
						}else{
							$html .= '<span class="qnt_minus">'.$quantity.'</span>&nbsp;&nbsp;';
						}
							
							
					}
					
					$html .= '</td></tr>';
				}
			}
			
			//$html .= '</tr>';
		}
		
		$html .= '</table>';
		
		echo $html;

}elseif($key == 'get_sizes'){
	
	$ProductsOnWare = $Warehouse->getProductsOnWareOnTable($id); ?>
			
		<div class="product_quantity_wrapper">
			
			<?php echo $ProductsOnWare['html']; ?>
			
		</div>
	
<?php	
}

?>