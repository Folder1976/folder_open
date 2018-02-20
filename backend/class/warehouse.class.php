<?php

class Warehouse
{
	private $db;
	private $pp;
	private $class_shops;
	
	
    function __construct (){
		
		$this->pp = DB_PREFIX;
		
		//Новое соединение с базой
		$this->db = mysqli_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE) or die("Error db connection "); 
		mysqli_set_charset($this->db,"utf8");
		
	}
	

	
	public function getWarehouse($id){
		$pp = $this->pp;
		
		$sql = 'SELECT * FROM `'.$pp.'warehouse` WHERE warehouse_id = "'.$id.'";';
		//echo $sql;
		$r = $this->db->query($sql);
		
		if($r->num_rows > 0){
			$tmp = $r->fetch_assoc();
			return $tmp;
		}
		
		return 0;
		
	}


	public function getWarehouses(){
		$pp = $this->pp;
		
		$sql = 'SELECT * FROM `'.$pp.'warehouse` ORDER BY shop_id, sort_order, name;';
		//echo $sql;
		$r = $this->db->query($sql);
		
		$return = array();
		if($r->num_rows > 0){
			while($tmp = $r->fetch_assoc()){
				$return[$tmp['warehouse_id']] = $tmp;
			}
		}
		
		return $return;
		
	}

	public function getShop($warehouse_id){
		
		$pp = $this->pp;
		
		//Это и есть магазин
		if($warehouse_id < 3){
			
			$sql = 'SELECT * FROM `'.$pp.'shops` WHERE shop_id="'.(int)$warehouse_id.'"ORDER BY sort_order, name LIMIT 1;';
			//echo $sql;
			$r = $this->db->query($sql);
			
			$return = array();
			if($r->num_rows > 0){
				
				return $r->fetch_assoc();
			}
			
		}elseif($warehouse_id > 0){
			
			$sql = 'SELECT S.* FROM `'.$pp.'warehouse` W
					LEFT JOIN `'.$pp.'shops` S ON S.shop_id = W.shop_id
					WHERE W.warehouse_id="'.(int)$warehouse_id.'"ORDER BY sort_order, name LIMIT 1;';
			//echo $sql;
			$r = $this->db->query($sql);
			
			$return = array();
			if($r->num_rows > 0){
				
				return $r->fetch_assoc();
			}
			
		}
		
	return false;	
			
	}
	
	public function getMainWarehouse($shop_id){
		
		$sql = 'SELECT warehouse_id FROM `'.$this->pp.'warehouse` WHERE shop_id = "'.$shop_id.'"
					ORDER BY is_main DESC LIMIT 1';
			
		$r = $this->db->query($sql) or die($sql);
			
			$return = array();
			if($r->num_rows > 0){
				
				$tmp = $r->fetch_assoc();
				return $tmp['warehouse_id'];
			}
	}
	
	public function getProductsOnWareOnTable($product_id){
		
		$result = $this->getProductsOnWare($product_id);
		
		global $warehouses, $masters, $sizes;
		
		$size_ids = array();
		
		$html = '<table class="product_qnt">';
		
		foreach($result as $master_id => $values){
			
			$html .= '<tr><th colspan="2">'.$masters[$master_id]['name'].'</th></tr>';
			
			foreach($values as $warehouse_id => $values2){

				if($warehouse_id != 0){
			
					if($warehouse_id > 0){
						$html .= '<tr><td>'.$warehouses[$warehouse_id]['name'].'</td><td>';
					}else{
						$html .= '<tr><td>В пути на маг.'.$warehouses[$warehouse_id]['name'].'</td><td>';
					}
				
					foreach($values2 as $size_id => $quantity){
							
							if((int)$size_id > 0){
								$html .= '[<b class="product_size" data-master_id="'.$master_id.'" data-size_id="'.$size_id.'" data-warehouse_id="'.$warehouse_id.'">'.$sizes[$size_id]['name'].'</b>] : ';
							}else{
								$html .= '[<b class="product_size" data-master_id="'.$master_id.'" data-size_id="0" data-warehouse_id="'.$warehouse_id.'">Без размера</b>] : ';
							}
						
						$size_ids[$size_id] = $size_id;
						
						if($quantity > 0){
							$html .= '<span class="qnt_plus '.$master_id.'_'.$size_id.'_'.$warehouse_id.'">'.$quantity.'</span>&nbsp;&nbsp;';
						}else{
							$html .= '<span class="qnt_minus '.$master_id.'_'.$size_id.'_'.$warehouse_id.'">'.$quantity.'</span>&nbsp;&nbsp;';
						}
							
							
					}
					
					$html .= '</td></tr>';
				}
			}
			
			//$html .= '</tr>';
		}
		
		$html .= '</table>';
		
		$data['html'] = $html;
		$data['size_ids'] = $size_ids;
		
		return $data;
		
	}
	public function getProductsOnWareOnTableArray($product_id){
		
		$result = $this->getProductsOnWare($product_id);
		
		global $warehouses, $masters, $sizes;
		
		$data = array();
		
		
		foreach($result as $master_id => $values){
			
			foreach($values as $warehouse_id => $values2){

				if($warehouse_id != 0){
			
					foreach($values2 as $size_id => $quantity){
							
							
						
						$data[$master_id][$warehouse_id][$size_id] = $quantity;
							
							
					}
				}
			}
			
		}
		
		return $data;
		
	}
	
	public function getProductsOnWare($product_id){
	
		$sql = 'SELECT OP.product_id,
					OP.master_id,
					OP.quantity,
					OP.size_id,
					OP.from_warehouse_id,
					OP.to_warehouse_id
					FROM ' . DB_PREFIX . 'operation_product OP
					LEFT JOIN ' . DB_PREFIX . 'operation O ON O.operation_id = OP.operation_id
					WHERE OP.product_id = "'.$product_id.'"
					ORDER BY OP.operation_product_id';
		$r = $this->db->query($sql) or die('sadl654k556jg345 '.$sql);
		
		$war = array();
		
		if($r->num_rows){
			
			while($row = $r->fetch_assoc()){
				
				//if(!isset($war[$row['master_id']])) $war[$row['master_id']] = array();
				//if(!isset($war[$row['master_id']][$row['size_id']])) $war[$row['master_id']][$row['size_id']] = array();
				if(!isset($war[$row['master_id']][$row['from_warehouse_id']][$row['size_id']])) $war[$row['master_id']][$row['from_warehouse_id']][$row['size_id']] = 0;
				if(!isset($war[$row['master_id']][$row['to_warehouse_id']][$row['size_id']])) $war[$row['master_id']][$row['to_warehouse_id']] [$row['size_id']]= 0;
				
				$war[$row['master_id']][$row['from_warehouse_id']][$row['size_id']] -= (int)$row['quantity'];
				$war[$row['master_id']][$row['to_warehouse_id']][$row['size_id']] += (int)$row['quantity'];
				
			}
			
		}
		
		return $war;
		
	}
	
}

?>
