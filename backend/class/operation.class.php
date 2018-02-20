<?php

class Operation
{
	private $db;
	private $pp;
	private $session;
	private $warehouses;
	private $warehouses_shop;
	private $shop_torg_class;
	private $Product;
	
    function __construct (){
		
		if (!isset($_SESSION)) session_start();
		
		$this->session = $_SESSION;
		
		global $warehouses, $warehouses_shop, $ShopTorg;
		global $Product;
		
		$this->Product = $Product;
		$this->warehouses = $warehouses;
		$this->warehouses_shop = $warehouses_shop;
		$this->shop_torg_class = $ShopTorg;
		
		$this->pp = DB_PREFIX;
		
		//Новое соединение с базой
		$this->db = mysqli_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE) or die("Error db connection "); 
		mysqli_set_charset($this->db,"utf8");
		
	}
	public function getKreditSummOnCustomers($customer_id){
		
		
		$sql = 'SELECT SUM(summ) as total, customer_id FROM `'.$this->pp.'operation` WHERE type_id=11 ';
			if($customer_id > 0){
				$sql .= ' AND customer_id='.$customer_id.' ';
			}
		
		$sql .= ' GROUP BY customer_id ';
		
		$r = $this->db->query($sql);
		
		$data = array();
		if($r->num_rows > 0){
			while($row = $r->fetch_assoc()){
				$data[$row['customer_id']] = (int)$row['total'];
			}
		}
	
	
		$sql = 'SELECT SUM(oplat_summ) as total, customer_id FROM `'.$this->pp.'oplata` GROUP BY customer_id ';
		$r = $this->db->query($sql);
		
		if($r->num_rows > 0){
			while($row = $r->fetch_assoc()){
				
				if(!isset($data[$row['customer_id']])) $data[$row['customer_id']] = 0;
				
				$data[$row['customer_id']] = (int)$data[$row['customer_id']] - (int)$row['total'];
				
			}
		}


	
		return $data;
	}
	
    public function getOperations($data){
		$pp = $this->pp;
		
		$sql = 'SELECT * FROM `'.$this->pp.'operation` WHERE 1 ';
		
		if(isset($data['operation_id']) AND (int)$data['operation_id'] > 0){
			$sql .= ' AND operation_id = "'.(int)$data['operation_id'].'" ';
		}
		
		if(isset($data['date']) AND $data['date'] != ''){
			$sql .= ' AND date = "'.$data['date'].'" ';
		}
		
		if(isset($data['type_id']) AND (int)$data['type_id'] > 0){
			$sql .= ' AND type_id = "'.(int)$data['type_id'].'" ';
		}
		
		if(isset($data['comment']) AND $data['comment'] != ''){
			$sql .= ' AND comment LIKE "%'.$data['comment'].'%" ';
		}
		
		if(isset($data['from_warehouse_id']) AND (int)$data['from_warehouse_id'] > 0){
			$sql .= ' AND from_warehouse_id = "'.(int)$data['from_warehouse_id'].'" ';
		}
		
		if(isset($data['to_warehouse_id']) AND (int)$data['to_warehouse_id'] > 0){
			$sql .= ' AND to_warehouse_id = "'.(int)$data['to_warehouse_id'].'" ';
		}
		
		if(isset($data['customer_id']) AND (int)$data['customer_id'] > 0){
			$sql .= ' AND customer_id = "'.(int)$data['customer_id'].'" ';
		}
		
		if(isset($data['user_id']) AND (int)$data['user_id'] > 0){
			$sql .= ' AND user_id = "'.(int)$data['user_id'].'" ';
		}
		
		if(isset($data['edit_date']) AND $data['edit_date'] != ''){
			$sql .= ' AND edit_date = "'.$data['edit_date'].'" ';
		}
		
		if(isset($data['no_type_id']) AND count($data['no_type_id']) > 0){
			$sql .= ' AND type_id NOT IN ('.implode(',',$data['no_type_id']).') ';
		}
		
		$sql .= 'ORDER BY operation_id DESC;';
		//echo $sql;
		$r = $this->db->query($sql);
		
		$data = array();
		if($r->num_rows > 0){
			while($row = $r->fetch_assoc()){
				$data[$row['operation_id']] = $row;
			}
		}
	
		return $data;
		
	}
	
    public function getOperationInRoad(){
		$pp = $this->pp;
		
		$sql = 'SELECT * FROM `'.$this->pp.'operation`
					WHERE type_id = 3 AND invert_operation_id = 0
					ORDER BY operation_id DESC;';
		//echo $sql;
		$r = $this->db->query($sql)or die($sql);
		
		$data = array();
		if($r->num_rows > 0){
			while($row = $r->fetch_assoc()){
				$data[$row['operation_id']] = $row;
			}
		}
	
		return $data;
		
	}
	
    public function getLastZakup($product_id){
		$pp = $this->pp;
		
		$sql = 'SELECT zakup FROM `'.$this->pp.'operation_product`
					WHERE product_id="'.(int)$product_id.'" AND type_id="1"
					ORDER BY operation_id DESC LIMIT 1;';
		//echo $sql;
		$r = $this->db->query($sql);
		
		if($r->num_rows > 0){
			$row = $r->fetch_assoc();
			return (int)$row['zakup'];
			
		}else{
			
			$sql = 'SELECT zakup FROM '.$this->pp.'product WHERE product_id="'.(int)$product_id.'" LIMIT 1';
			$r = $this->db->query($sql);
		
			if($r->num_rows > 0){
				
				$row = $r->fetch_assoc();
				return (int)$row['zakup'];
				
			}
		}
	
		return 0;
		
	}
	
    public function getLastPrice($product_id){
		$pp = $this->pp;
		
		$sql = 'SELECT price_invert FROM `'.$this->pp.'operation_product`
					WHERE product_id="'.(int)$product_id.'"
					ORDER BY operation_id DESC LIMIT 1;';
		//echo $sql;
		$r = $this->db->query($sql);
		
		if($r->num_rows > 0){
			
			$row = $r->fetch_assoc();
			return (int)$row['price_invert'];
			
		}else{
			
			$sql = 'SELECT price FROM '.$this->pp.'product WHERE product_id="'.(int)$product_id.'" LIMIT 1';
			$r = $this->db->query($sql);
		
			if($r->num_rows > 0){
				
				$row = $r->fetch_assoc();
				return (int)$row['price'];
				
			}	
		}
	
		return 0;
		
	}
	
	/* $data['operation_id']
	 * $data['product_id']
	 * $data['zakup']
	 */
	public function dellProductRow($data){
		
		$sql = 'DELETE FROM ' . DB_PREFIX . 'operation_product
				WHERE
				operation_id = "'.(int)$data['operation_id'].'" AND
				sub_operation_id = "'.(int)$data['sub_operation_id'].'" AND
				product_id = "'.(int)$data['product_id'].'" AND
				zakup = "'.$data['zakup'].'" 
				';
		//echo $sql;
		$this->db->query($sql) or die('saddf'.$sql);
		
		$this->updateWarehouseItems($data['product_id']);
		
	}
	
	/* $operation_id
	 */
	public function dellOperation($operation_id){
		
		$sql = 'SELECT distinct product_id FROM ' . DB_PREFIX . 'operation_product
				WHERE operation_id = "'.$operation_id.'"';
		//echo $sql;
		$r = $this->db->query($sql) or die('saевыпddf'.$sql);
		
		$sql = 'DELETE FROM ' . DB_PREFIX . 'operation_product
				WHERE operation_id = "'.$operation_id.'"';
		//echo $sql;
		$this->db->query($sql) or die('saевыпddf'.$sql);
		
		$sql = 'DELETE FROM ' . DB_PREFIX . 'operation
				WHERE operation_id = "'.$operation_id.'"';
		//echo $sql;
		$this->db->query($sql) or die('sad3уууdf'.$sql);
		
		while($row = $r->fetch_assoc()){
			$this->updateWarehouseItems($row['product_id']);
		}
		
	}
	
	public function dellSubOperation($operation_id, $sub_operation_id = 0){
		
		$sql = 'SELECT distinct product_id FROM ' . DB_PREFIX . 'operation_product
				WHERE operation_id = "'.$operation_id.'" AND sub_operation_id = "'.$sub_operation_id.'" ';
		//echo $sql;
		$r = $this->db->query($sql) or die('saевыпddf'.$sql);
		
		$sql = 'DELETE FROM ' . DB_PREFIX . 'operation_product
				WHERE operation_id = "'.$operation_id.'" AND sub_operation_id = "'.$sub_operation_id.'"';
		//echo $sql;
		$this->db->query($sql) or die('saевыпddf'.$sql);
		
		//echo $sql;
		$this->db->query($sql) or die('sad3уууdf'.$sql);
		
		while($row = $r->fetch_assoc()){
			$this->updateWarehouseItems($row['product_id']);
		}
		
	}
	
	/* $data['operation_id']
	 * $data['product_id']
	 * $data['zakup']
	 * $data['quantity']
	 * $data['size_id']
	 */
	public function updateProductQuantity($data){
		
		if(!isset($data['sub_operation_id'])) $data['sub_operation_id'] = 0;
		
		if(!isset($data['from_warehouse_id'])){
			$data['from_warehouse_id'] = '(SELECT from_warehouse_id FROM '.$this->pp.'operation WHERE operation_id="'.$data['operation_id'].'" LIMIT 1)';
		}
		if(!isset($data['to_warehouse_id'])){
			$data['to_warehouse_id'] = '(SELECT to_warehouse_id FROM '.$this->pp.'operation WHERE operation_id="'.$data['operation_id'].'" LIMIT 1)';
		}
	
		
		$price = ' zakup = "'.$data['zakup'].'" ';
		if(isset($data['type_id']) AND $data['type_id'] == 8){
			//$price = ' price_invert = "'.$data['zakup'].'" ';
		}
		
		if((int)$data['quantity'] > 0){
	
			$sql = 'INSERT INTO ' . DB_PREFIX . 'operation_product
						SET
						quantity = "'.(int)$data['quantity'].'",
						operation_id = "'.(int)$data['operation_id'].'",
						sub_operation_id = "'.(int)$data['sub_operation_id'].'",
						product_id = "'.(int)$data['product_id'].'",
						price_invert = "'.(int)$data['price_invert'].'",
						master_id = "'.(int)$data['master_id'].'",
						size_id = "'.(int)$data['size_id'].'",
						from_warehouse_id = '.(int)$data['from_warehouse_id'].',
						to_warehouse_id = '.(int)$data['to_warehouse_id'].',
						'.$price.'
						on duplicate key update
						quantity = "'.(int)$data['quantity'].'"
						';
		
		}else{
			
			$sql = 'DELETE FROM ' . DB_PREFIX . 'operation_product
						WHERE
						operation_id = "'.(int)$data['operation_id'].'" AND
						sub_operation_id = "'.(int)$data['sub_operation_id'].'" AND
						product_id = "'.(int)$data['product_id'].'" AND
						size_id = "'.(int)$data['size_id'].'" AND
						master_id = "'.(int)$data['master_id'].'" AND
						'.$price.' 
						';
			
		}
		//echo $sql;
		$this->db->query($sql) or die('sadlkjg345 '.$sql);
		
		$this->updateWarehouseItems($data['product_id']);
		
	}
	
	public function getShopsMonej(){
		
		$data = array();
		
		foreach($this->warehouses_shop AS $shop_id => $warehouses){
			$data[$shop_id] = $this->getShopMonej($shop_id);
		}
		
		return $data;
		
	}
	
	public function getShopMonej($shop_id){
		
		$sql = 'SELECT O.*
					FROM `'.$this->pp.'oplata` O
					LEFT JOIN `'.$this->pp.'operation` OP ON OP.operation_id = O.operation_id
					WHERE 1 ';
	
		//Склады магазина
		$sql .= ' AND ( ';
		foreach($this->warehouses_shop[$shop_id] as $warehouse_id => $row){
			$sql .= ' OP.from_warehouse_id = "'.$warehouse_id.'" OR ';
		}
		$sql = trim($sql, 'OR ');
		$sql .= ' )';
		
	
		//echo $sql;
		$r = $this->db->query($sql);
		
		$summ = 0;
		
		if($r->num_rows > 0){
			while($row = $r->fetch_assoc()){
				
				if($row['status'] > 0){
					$summ -= (float)$row['oplat_summ'];
				}else{
					$summ += (float)$row['oplat_money'] - abs((float)$row['zdacha']);
				}
				//echo '<br>'.$summ;
			}
		}
	
		return $summ;
		
	}
	
	
	
	public function getShopOperation($data){
		$pp = $this->pp;
		
		if(!isset($data['type_id'])){
			$data['type_id'] = 8;
		}
	
		
		$sql = 'SELECT O.*, O.from_warehouse_id, OP.*, P.code, P.model, P.image
					FROM `'.$this->pp.'operation` O
					LEFT JOIN `'.$this->pp.'operation_product` OP ON OP.operation_id = O.operation_id
					LEFT JOIN `'.$this->pp.'product` P ON OP.product_id = P.product_id
					WHERE O.type_id="'.(int)$data['type_id'].'" ';
		
		if(isset($data['operation_id']) AND (int)$data['operation_id'] > 0){
			$sql .= ' AND O.operation_id = "'.(int)$data['operation_id'].'" ';
		}
		
		if(isset($data['sub_operation_id']) AND (int)$data['sub_operation_id'] > 0){
			$sql .= ' AND O.sub_operation_id = "'.(int)$data['sub_operation_id'].'" ';
		}
		
		if(isset($data['date']) AND $data['date'] != ''){
			$sql .= ' AND O.date = "'.$data['date'].'" ';
		}
		
		if(isset($data['user_id']) AND (int)$data['user_id'] > 0){
			$sql .= ' AND O.user_id = "'.(int)$data['user_id'].'" ';
		}
		
		if(isset($data['product']) AND (int)$data['product'] > 0){
			$sql .= ' AND (P.model LIKE "%'.$data['product'].'%" OR
								P.code LIKE "%'.$data['product'].'%")';
		}
		
		if(isset($data['shop_id']) AND $data['shop_id'] != ''){
			$sql .= ' AND ( ';
			foreach($this->warehouses_shop[$data['shop_id']] as $warehouse_id => $row){
				$sql .= ' O.from_warehouse_id = "'.$warehouse_id.'" OR ';
			}
			$sql = trim($sql, 'OR ');
			$sql .= ' )';
		}
		
		$sql .= 'ORDER BY O.operation_id DESC, OP.sub_operation_id DESC';
		
		if(isset($data['page']) AND $data['page'] > 0){
			$sql .= ' LIMIT '.(((int)$data['page'] * 100) + 1).', 100  ';
		}else{
			//$sql .= ' LIMIT 1, 1000  ';
		}

		//echo $sql;
		$r = $this->db->query($sql);
		
		$data = array();
		if($r->num_rows > 0){
			while($row = $r->fetch_assoc()){
				
				$data[$row['operation_id'].'_'.$row['sub_operation_id']][] = $row;
			}
		}
	
		return $data;
	}
	
	public function setBesnalStatus($data){
		
		//echo '<pre>'; print_r(var_dump( $data  ));
		$sql = 'UPDATE `'.$this->pp.'oplata_beznal` SET
						`status`="'.$data['status'].'",
						`validator_user_id`="'.$_SESSION['default']['user_id'].'"
				WHERE
					operation_id = "'.$data['operation_id'].'" AND
					sub_operation_id = "'.$data['sub_operation_id'].'" 
				';
		$this->db->query($sql) or die($sql);
		
	}
	
	public function getKassa($data){
		$pp = $this->pp;
		
		$sql = 'SELECT O.*, OP.from_warehouse_id, OP.to_warehouse_id,
					(SELECT sum(summ) AS sub_operation_summ FROM `'.$this->pp.'operation_product` OPR WHERE OPR.operation_id = O.operation_id AND OPR.sub_operation_id = O.sub_operation_id) AS sub_operation_summ
					FROM `'.$this->pp.'oplata` O
					LEFT JOIN `'.$this->pp.'operation` OP ON OP.operation_id = O.operation_id
					WHERE 1 ';
		
		if(isset($data['operation_id']) AND (int)$data['operation_id'] > 0){
			$sql .= ' AND O.operation_id = "'.(int)$data['operation_id'].'" ';
		}
		
		if(isset($data['sub_operation_id']) AND (int)$data['sub_operation_id'] > 0){
			$sql .= ' AND O.sub_operation_id = "'.(int)$data['sub_operation_id'].'" ';
		}
		
		if(isset($data['date']) AND $data['date'] != ''){
			$sql .= ' AND O.date = "'.$data['date'].'" ';
		}
		
		if(isset($data['user_id']) AND (int)$data['user_id'] > 0){
			$sql .= ' AND O.user_id = "'.(int)$data['user_id'].'" ';
		}
		
		if(isset($data['status']) AND (int)$data['status'] > 0){
			$sql .= ' AND O.status = "'.(int)$data['status'].'" ';
		}
		
		if(isset($data['shop_id']) AND $data['shop_id'] != ''){
			$sql .= ' AND ( ';
			foreach($this->warehouses_shop[$data['shop_id']] as $warehouse_id => $row){
				$sql .= ' OP.from_warehouse_id = "'.$warehouse_id.'" OR ';
			}
			$sql = trim($sql, 'OR ');
			$sql .= ' )';
		}
		
		$sql .= 'ORDER BY O.operation_id DESC, O.sub_operation_id DESC';
		
		if(isset($data['page']) AND $data['page'] > 0){
			$sql .= ' LIMIT '.(((int)$data['page'] * 100) + 1).', 100  ';
		}else{
			//$sql .= ' LIMIT 1, 1000  ';
		}

		//echo $sql;
		$r = $this->db->query($sql);
		
		$data = array();
		if($r->num_rows > 0){
			while($row = $r->fetch_assoc()){
				
				$data[$row['operation_id'].'_'.$row['sub_operation_id']][] = $row;
			}
		}
	
		return $data;
	}
	
	public function getBeznal($data){
		$pp = $this->pp;
		
		$sql = 'SELECT O.*, OP.from_warehouse_id, OP.to_warehouse_id,
					(SELECT sum(summ) AS sub_operation_summ FROM `'.$this->pp.'operation_product` OPR WHERE OPR.operation_id = O.operation_id AND OPR.sub_operation_id = O.sub_operation_id) AS sub_operation_summ
					FROM `'.$this->pp.'oplata_beznal` O
					LEFT JOIN `'.$this->pp.'operation` OP ON OP.operation_id = O.operation_id
					WHERE 1 ';
		
		if(isset($data['operation_id']) AND (int)$data['operation_id'] > 0){
			$sql .= ' AND O.operation_id = "'.(int)$data['operation_id'].'" ';
		}
		
		if(isset($data['sub_operation_id']) AND (int)$data['sub_operation_id'] > 0){
			$sql .= ' AND O.sub_operation_id = "'.(int)$data['sub_operation_id'].'" ';
		}
		
		if(isset($data['date']) AND $data['date'] != ''){
			$sql .= ' AND O.date = "'.$data['date'].'" ';
		}
		
		if(isset($data['user_id']) AND (int)$data['user_id'] > 0){
			$sql .= ' AND O.user_id = "'.(int)$data['user_id'].'" ';
		}
		
		if(isset($data['status']) AND (int)$data['status'] > 0){
			$sql .= ' AND O.status = "'.(int)$data['status'].'" ';
		}
		
		if(isset($data['shop_id']) AND $data['shop_id'] != ''){
			$sql .= ' AND ( ';
			foreach($this->warehouses_shop[$data['shop_id']] as $warehouse_id => $row){
				$sql .= ' OP.from_warehouse_id = "'.$warehouse_id.'" OR ';
			}
			$sql = trim($sql, 'OR ');
			$sql .= ' )';
		}
		
		$sql .= ' ORDER BY O.operation_id DESC, O.sub_operation_id DESC';
		
		if(isset($data['page']) AND $data['page'] > 0){
			$sql .= ' LIMIT '.(((int)$data['page'] * 100) + 1).', 100  ';
		}else{
			//$sql .= ' LIMIT 1, 1000  ';
		}

		//echo $sql;
		$r = $this->db->query($sql);
		
		$data = array();
		if($r->num_rows > 0){
			while($row = $r->fetch_assoc()){
				
				$data[$row['operation_id'].'_'.$row['sub_operation_id']][] = $row;
			}
		}
	
		return $data;
	}
	
	
	public function getKredit($data){
		$pp = $this->pp;
		
		
		
		$sql = 'SELECT O.*, OP.from_warehouse_id, OP.to_warehouse_id,
					(SELECT sum(summ) AS sub_operation_summ FROM `'.$this->pp.'operation_product` OPR WHERE OPR.operation_id = O.operation_id AND OPR.sub_operation_id = O.sub_operation_id) AS sub_operation_summ
					FROM `'.$this->pp.'oplata` O
					LEFT JOIN `'.$this->pp.'operation` OP ON OP.operation_id = O.operation_id
					WHERE O.customer_id > 0';
		
		if(isset($data['operation_id']) AND (int)$data['operation_id'] > 0){
			$sql .= ' AND O.operation_id = "'.(int)$data['operation_id'].'" ';
		}
		
		if(isset($data['date']) AND $data['date'] != ''){
			$sql .= ' AND O.date = "'.$data['date'].'" ';
		}
		
		if(isset($data['customer_id']) AND (int)$data['customer_id'] > 0){
			$sql .= ' AND O.customer_id = "'.(int)$data['customer_id'].'" ';
		}
	
		if(isset($data['not_customer_id']) AND count($data['not_customer_id']) > 0){
			$sql .= ' AND O.customer_id NOT IN ('.implode(',',$data['not_customer_id']).') ';
		}
	
		if(isset($data['user_id']) AND (int)$data['user_id'] > 0){
			$sql .= ' AND O.user_id = "'.(int)$data['user_id'].'" ';
		}
		
		if(isset($data['status']) AND (int)$data['status'] > 0){
			$sql .= ' AND O.status = "'.(int)$data['status'].'" ';
		}
		
		if(isset($data['shop_id']) AND $data['shop_id'] != ''){
			$sql .= ' AND ( ';
			foreach($this->warehouses_shop[$data['shop_id']] as $warehouse_id => $row){
				$sql .= ' OP.from_warehouse_id = "'.$warehouse_id.'" OR ';
			}
			$sql = trim($sql, 'OR ');
			$sql .= ' )';
		}
		
		$sql .= ' ORDER BY O.operation_id DESC, O.sub_operation_id DESC';
		
		if(isset($data['page']) AND $data['page'] > 0){
			$sql .= ' LIMIT '.(((int)$data['page'] * 100) + 1).', 100  ';
		}else{
			//$sql .= ' LIMIT 1, 1000  ';
		}

		//echo $sql;
		$r = $this->db->query($sql) or die($sql);
		
		$data = array();
		if($r->num_rows > 0){
			while($row = $r->fetch_assoc()){
				
				$data[$row['operation_id'].'_'.$row['sub_operation_id']][] = $row;
			}
		}
	
		return $data;
	}
	
	
	/*
	 *$data['operation_id']
	 *$data['sub_operation_id']
	 *$data['zdacha']
	 *$data['oplat_summ']
	 *$data['oplat_money']
	 *$data['status'] - 0 приход, 1 инкасация
	 */
	public function addOplat($data){
	
		if(!isset($data['status'])) {
			$data['status'] = 0;
		}
		if(!isset($data['operation_id']) OR (int)$data['operation_id'] < 1) {
			echo 'Нет номера операции!';
			return false;
		}
		if(!isset($data['sub_operation_id']) OR ($data['status'] == 0 AND (int)$data['sub_operation_id'] < 1)) {
			echo 'Нет номера чека!';
			return false;
		}
		
	 	if(!isset($data['zdacha'])) {
			$data['zdacha'] = 0;
		}
	 
	 	if(!isset($data['oplat_summ'])) {
			$data['oplat_summ'] = 0;
		}
	 
	 	if(!isset($data['oplat_money'])) {
			$data['oplat_money'] = 0;
		}
	 
		$data['date'] = date('Y-m-d H:i:s');
		$data['user_id'] = (int)$_SESSION['default']['user_id'];
		
		if(!isset($data['customer_id'])) $data['customer_id'] = 0;
		if(!isset($data['comment'])) $data['comment'] = '';
		
			
		
		if(isset($data['cart']) AND $data['cart'] != '') {
			
			$sql = 'INSERT INTO `' . DB_PREFIX . 'oplata_beznal` SET
					`date`="'.$data['date'].'",
					`operation_id`="'.(int)$data['operation_id'].'",
					`sub_operation_id`="'.(int)$data['sub_operation_id'].'",
					`oplat_summ`="'.(float)$data['oplat_summ'].'",
					`oplat_money`="'.(float)$data['oplat_money'].'",
					`zdacha`="'.(float)$data['zdacha'].'",
					`comment`="'.$data['comment'].'",
					`user_id`="'.(int)$data['user_id'].'",
					`customer_id`="'.(int)$data['customer_id'].'",
					`status`=0,
					`cart`="'.(int)$data['cart'].'"';
					
		}else{
			
			$sql = 'INSERT INTO `' . DB_PREFIX . 'oplata` SET
					`date`="'.$data['date'].'",
					`operation_id`="'.(int)$data['operation_id'].'",
					`sub_operation_id`="'.(int)$data['sub_operation_id'].'",
					`oplat_summ`="'.(float)$data['oplat_summ'].'",
					`oplat_money`="'.(float)$data['oplat_money'].'",
					`zdacha`="'.(float)$data['zdacha'].'",
					`comment`="'.$data['comment'].'",
					`customer_id`="'.(int)$data['customer_id'].'",
					`user_id`="'.(int)$data['user_id'].'",
					`status`="'.(int)$data['status'].'"';
					
		}
		$this->db->query($sql) or die("Не удалось создать оплату!\n\rВозможно оплата по этому чеку уже существует ".$sql);		
	}
	
	
	/*
	 *$data['operation_id']
	 *$data['sub_operation_id']
	 */
	public function copyOperation($operation_id, $sub_operation_id, $type_id = 9, $dell_donor=''){
		
		//Нужно будет добавть тут получения нужного $type_id и операции донара
		global $ShopTorg;
		
		$new_operation_id = $ShopTorg->addOperation($_SESSION['default']['shop_id'], $type_id);
		$new_sub_operation_id = $ShopTorg->getSubOperation($new_operation_id );
		
		$products = $this->getOperationProducts($operation_id, $sub_operation_id);
		
		if($dell_donor != ''){
			$this->dellSubOperation($operation_id, $sub_operation_id);
		}
		
		//echo '<pre>'; print_r(var_dump( $products  ));
		foreach($products as $rows){
			foreach($rows as $product_id => $row){
			
				//echo '<pre>'; print_r(var_dump( $row  ));
				
				$row['quantity'] = $row['operation_quantity'];
				$row['operation_id'] = $new_operation_id;
				$row['sub_operation_id'] = $new_sub_operation_id;
				
				//Если это возврат то перевернем склады
				if($type_id == 9){
					
					$tmp = $row['from_warehouse_id'];
					$row['from_warehouse_id'] = $row['to_warehouse_id'];
					$row['to_warehouse_id'] = $tmp;
					
				}
				
				$this->addProduct($row);
				
				$this->updateWarehouseItems($product_id);	
			}
		}
		
		$this->updateOperationSumm($operation_id);
		
		$data = array();
		$data['operation_id'] = $new_operation_id;
		$data['sub_operation_id'] = $new_sub_operation_id;
		
		return $data;
		
	}

	/*
	 *$data['operation_id']
	 *$data['sub_operation_id']
	 *$data['vozvrat_summ']
	 *$data['status'] - Равен сотруднику
	 */
	public function addVozvrat($data){
		$type_id = 9; //Возврат
		
		if(!isset($data['status'])) {
			$data['status'] = 0;
		}
		if(!isset($data['operation_id']) OR (int)$data['operation_id'] < 1) {
			echo 'Нет номера операции!';
			return false;
		}
		if(!isset($data['sub_operation_id']) OR ($data['status'] == 0 AND (int)$data['sub_operation_id'] < 1)) {
			echo 'Нет номера чека!';
			return false;
		}
		
	 	if(!isset($data['vozvrat_summ'])) {
			$data['vozvrat_summ'] = 0;
		}
	 
		$data['date'] = date('Y-m-d H:i:s');
		$data['user_id'] = (int)$_SESSION['default']['user_id'];
		
		$sql = 'SELECT * FROM `' . DB_PREFIX . 'oplata` WHERE
					`operation_id`="'.(int)$data['operation_id'].'" AND
					`sub_operation_id`="'.(int)$data['sub_operation_id'].'"';
		$r = $this->db->query($sql) or die("Не удалось проверить наличие оплаты ".$sql);		

		//скопируем чек
		//Если по чеку нет оплаты - удаляем донора
		if($r->num_rows == 0){
			$res = $this->copyOperation($data['operation_id'], $data['sub_operation_id'], $type_id, 'dell_donor');
		}else{
			$res = $this->copyOperation($data['operation_id'], $data['sub_operation_id'], $type_id);
		}
		$data['operation_id']		= $res['operation_id'];
		$data['sub_operation_id'] 	= $res['sub_operation_id'];
		
				$sql = 'INSERT INTO `' . DB_PREFIX . 'oplata` SET
					`date`="'.$data['date'].'",
					`operation_id`="'.(int)$data['operation_id'].'",
					`sub_operation_id`="'.(int)$data['sub_operation_id'].'",
					`oplat_summ`="'.(float)$data['vozvrat_summ'].'",
					`oplat_money`="0",
					`zdacha`="0",
					`user_id`="'.(int)$data['user_id'].'",
					`status`="'.(int)$data['status'].'"
			';
			
		$this->db->query($sql) or die("Не удалось создать оплату!\n\rВозможно оплата по этому чеку уже существует ".$sql);		
	}
	
	/*
	 *$data['product_id']
	 *$data['size_id']
	 *$data['master_id']
	 *$data['quantity']
	 */
	public function updateWarehouseItems($product_id){
		
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
		$r = $this->db->query($sql) or die('sadlk5356jg345 '.$sql);
		
		$war = array();
		
		if($r->num_rows){
			
			while($row = $r->fetch_assoc()){
				
				//if(!isset($war[$row['master_id']])) $war[$row['master_id']] = array();
				//if(!isset($war[$row['master_id']][$row['size_id']])) $war[$row['master_id']][$row['size_id']] = array();
				if(!isset($war[$row['master_id']][$row['size_id']][$row['from_warehouse_id']])) $war[$row['master_id']][$row['size_id']][$row['from_warehouse_id']] = 0;
				if(!isset($war[$row['master_id']][$row['size_id']][$row['to_warehouse_id']])) $war[$row['master_id']][$row['size_id']][$row['to_warehouse_id']] = 0;
				
				$war[$row['master_id']][$row['size_id']][$row['from_warehouse_id']] -= (int)$row['quantity'];
				$war[$row['master_id']][$row['size_id']][$row['to_warehouse_id']] += (int)$row['quantity'];
				
			}
			
		}
		
		$sql = 'DELETE FROM ' . DB_PREFIX . 'product_warehouse WHERE product_id = "'.$product_id.'";';
		$this->db->query($sql) or die('sadlk5544446jg345 '.$sql);
		
		foreach($war as $master_id => $values){
			
			foreach($values as $size_id => $values2){

				foreach($values2 as $warehouse_id => $quantity){
					if($quantity != 0){
						
						$sql = 'INSERT INTO ' . DB_PREFIX . 'product_warehouse SET
							product_id = "'.$product_id.'",
							master_id = "'.$master_id.'",
							warehouse_id = "'.$warehouse_id.'",
							size_id = "'.$size_id.'",
							quantity = "'.$quantity.'";';
						$this->db->query($sql) or die('sadlk556jg345 '.$sql);
						
						//echo $sql.'  ';
						
					}
				}
			}
		}
		
	}
	
	public function getSubOperationSumm($data){
		
		$sql = 'SELECT SUM(summ) AS summ FROM '.$this->pp.'operation_product
				WHERE
				operation_id = "'.(int)$data['operation_id'].'" AND
				sub_operation_id = "'.(int)$data['sub_operation_id'].'"';
		
		$r = $this->db->query($sql);
		$row = $r->fetch_assoc();
		
		return $row['summ'];
	}
	
	public function updateOperationSumm($operation_id){
	
		$operation = $this->getOperation($operation_id);
	
		//Пресчет сумм
		if((int)$operation['type_id'] == 1){
			$sql = 'UPDATE '.$this->pp.'operation_product
				SET
				summ = (quantity * zakup)
				WHERE
				operation_id = "'.$operation_id.'"';
	
		}else{
			$sql = 'UPDATE '.$this->pp.'operation_product
				SET
				summ = (quantity * price_invert)
				WHERE
				operation_id = "'.$operation_id.'"';
	
		}
		//echo $sql;
		$this->db->query($sql);
	
		//Получим сумму
		$sql = 'SELECT SUM(summ) AS summ FROM '.$this->pp.'operation_product
				WHERE
				operation_id = "'.$operation_id.'"';
		$r = $this->db->query($sql);
		$row = $r->fetch_assoc();
		
		//Запишем новую сумму
		$sql = 'UPDATE '.$this->pp.'operation
				SET
				summ = "'.(int)$row['summ'].'"
				WHERE
				operation_id = "'.$operation_id.'"';
		$this->db->query($sql);
	
		return (int)$row['summ'];
	}
	
    public function getOperation($operation_id){
		$pp = $this->pp;
		
		$sql = 'SELECT * FROM `'.$this->pp.'operation` WHERE operation_id = "'.(int)$operation_id.'" LIMIT 1;';
		//echo $sql;
		$r = $this->db->query($sql);
		
		$return = array();
		if($r->num_rows > 0){
			$row = $r->fetch_assoc();
			return $row;
			
		}
	
		return false;
		
	}

	public function getProductMaster($operation_id, $index){
		
		$pp = $this->pp;
		
		list($product_id, $zakup) = explode('_',$index);
		
		$sql = 'SELECT distinct OP.master_id, M.name
					FROM `'.$this->pp.'operation_product` OP
					LEFT JOIN `'.$this->pp.'master` M ON M.master_id = OP.master_id
					
					WHERE operation_id = "'.(int)$operation_id.'"
					AND product_id = "'.(int)$product_id.'"
					AND zakup = "'.$zakup.'"
					ORDER BY M.name;';
		//echo $sql;
		$r = $this->db->query($sql) or die($sql);
		
		$return = '';
		if($r->num_rows > 0){
			
			while($row = $r->fetch_assoc()){
				$return .= $row['name'].', ';
			}
			
			return trim($return, ', ');
		}
	
		return '';
		
		
		
	}
	
    public function getOperationProducts($operation_id, $sub_operation_id = 0){
		$pp = $this->pp;
		
		$sql = 'SELECT *, OP.quantity AS operation_quantity, OP.zakup AS operation_zakup
					FROM `'.$this->pp.'operation_product` OP
					LEFT JOIN `'.$this->pp.'product` P ON P.product_id = OP.product_id
					LEFT JOIN `'.$this->pp.'product_description` PD ON P.product_id = PD.product_id
					LEFT JOIN `'.$this->pp.'size` S ON S.size_id = OP.size_id
					WHERE operation_id = "'.(int)$operation_id.'" ';
					
					if((int)$sub_operation_id > 0){
						$sql .= ' AND sub_operation_id = "'.(int)$sub_operation_id.'" ';
					}
					
		if(isset($_GET['order']) AND $_GET['order'] != ''){
			
			if($_GET['order'] == 'codeAZ'){
			
				$sql .= ' ORDER BY P.code;';
			
			}elseif($_GET['order'] == 'codeZA'){
			
				$sql .= ' ORDER BY P.code DESC;';
			
			}elseif($_GET['order'] == 'operationAZ'){
			
				$sql .= ' ORDER BY OP.operation_product_id ASC;';
			
			}elseif($_GET['order'] == 'operationZA'){
			
				$sql .= ' ORDER BY OP.operation_product_id DESC;';
			
			}elseif($_GET['order'] == 'baseAZ'){
			
				$sql .= ' ORDER BY P.product_id ASC;';
			
			}elseif($_GET['order'] == 'baseZA'){
			
				$sql .= ' ORDER BY P.product_id DESC;';
			
			}
			
		}else{
			$sql .= ' ORDER BY PD.name;';
		}		
					
		//echo '<br><br><br>'.$sql;
		$r = $this->db->query($sql) or die($sql);
		
		$return = array();
		if($r->num_rows > 0){
			while($row = $r->fetch_assoc()){
				
				$return[$row['product_id'].'_'.$row['operation_zakup'].'_'.$row['master_id']][$row['size_id']] = $row;
			}
			
			return $return;
		}
	
		return false;
		
	}
	
	/*
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
	 */
	 
	public function addProduct($data){
		$pp = $this->pp;
		
		global $Product;
		
		$this->Product = $Product;
		
		if(!isset($data['from_warehouse_id'])){
			$data['from_warehouse_id'] = '(SELECT from_warehouse_id FROM '.$this->pp.'operation WHERE operation_id="'.$data['operation_id'].'" LIMIT 1)';
		}
		if(!isset($data['to_warehouse_id'])){
			$data['to_warehouse_id'] = '(SELECT to_warehouse_id FROM '.$this->pp.'operation WHERE operation_id="'.$data['operation_id'].'" LIMIT 1)';
		}
		
	
		
		if((int)$data['currency_id'] == 0){
			
			$product = $this->Product->getProduct($data['product_id']);
			
			$data['currency_id'] = $product['zakup_currency_id'];
			
		}
		
		$price = ' zakup = "'.$data['zakup'].'", currency_id = "'.$data['currency_id'].'",';
		$priceWHERE = ' zakup = "'.$data['zakup'].'" AND currency_id = "'.$data['currency_id'].'" ';
		if(isset($data['type_id']) AND $data['type_id'] == 8){
			$price = ' zakup = "'.$data['price_invert'].'", currency_id = "4",';
			$priceWHERE = ' zakup = "'.$data['price_invert'].'" AND currency_id = "4" ';
		}
		
		$sql = 'SELECT SUM(quantity) as total FROM ' . $this->pp . 'operation_product
				WHERE
				operation_id = "'.(int)$data['operation_id'].'" AND
				size_id = "'.$data['size_id'].'" AND
				master_id = "'.$data['master_id'].'" AND
				from_warehouse_id = '.$data['from_warehouse_id'].' AND
				to_warehouse_id = '.$data['to_warehouse_id'].' AND
				product_id = "'.(int)$data['product_id'].'" AND
				sub_operation_id = "'.(isset($data['sub_operation_id']) ? $data['sub_operation_id'] : 0 ).'" AND
				'.$priceWHERE.'';
		$r = $this->db->query($sql) or die($sql);
		
		if($r->num_rows){
			
			$row = $r->fetch_assoc();
			$data['quantity'] = (int)$data['quantity'] + (int)$row['total'];
			
			$sql = 'DELETE FROM ' . $this->pp . 'operation_product
				WHERE
				operation_id = "'.(int)$data['operation_id'].'" AND
				size_id = "'.$data['size_id'].'" AND
				master_id = "'.$data['master_id'].'" AND
				from_warehouse_id = '.$data['from_warehouse_id'].' AND
				to_warehouse_id = '.$data['to_warehouse_id'].' AND
				product_id = "'.(int)$data['product_id'].'" AND
				sub_operation_id = "'.(isset($data['sub_operation_id']) ? $data['sub_operation_id'] : 0 ).'" AND
				'.$priceWHERE.'';
			$this->db->query($sql) or die($sql);
			
		}
		
		$sql = 'INSERT INTO ' . $this->pp . 'operation_product
					SET
					quantity = "'.(int)$data['quantity'].'",
					operation_id = "'.(int)$data['operation_id'].'",
					product_id = "'.(int)$data['product_id'].'",
					type_id = (SELECT type_id FROM '.$this->pp.'operation WHERE operation_id="'.$data['operation_id'].'"),
					sub_operation_id = "'.(isset($data['sub_operation_id']) ? $data['sub_operation_id'] : 0 ).'",
					size_id = "'.$data['size_id'].'",
					'.$price.'
					master_id = "'.$data['master_id'].'",
					from_warehouse_id = '.$data['from_warehouse_id'].',
					to_warehouse_id = '.$data['to_warehouse_id'].',
					
					customer_id = "'.$data['customer_id'].'",
					price_invert = "'.$data['price_invert'].'"
					on duplicate key update
					quantity = "'.(int)$data['quantity'].'"
					';
					
		//echo $sql;
		$this->db->query($sql) or die($sql);
		
		$return = $this->db->insert_id;
		
		$this->updateWarehouseItems((int)$data['product_id']);	
		$this->updateOperationSumm($data['operation_id']);
		
		return $return;
		
	}
	
	public function addOperation($data){
		$pp = $this->pp;
		
		if(!isset($data['summ'])) $data['summ'] = 0;
		
		$sql = 'INSERT INTO ' . $this->pp . 'operation
					SET
					date = "'.date('Y-m-d H:i:s').'",
					edit_date = "'.date('Y-m-d H:i:s').'",
					user_id = "'.$this->session['default']['user_id'].'",
					customer_id = "'.$data['customer_id'].'",
					invert_operation_id = "'.(isset($data['invert_operation_id']) ? $data['invert_operation_id'] : 0 ).'",
					/*sub_operation_id = "'.(isset($data['sub_operation_id']) ? $data['sub_operation_id'] : 0 ).'",*/
					summ = "'.(float)$data['summ'].'",
					comment = "'.$data['comment'].'",
					type_id = "'.$data['type_id'].'",
					from_warehouse_id = "'.$data['from_warehouse_id'].'",
					to_warehouse_id = "'.$data['to_warehouse_id'].'"
					';
					
		//echo $sql;
		$this->db->query($sql) or die($sql);
		
		$operation_id = $this->db->insert_id;
		
		if(isset($data['invert_operation_id']) AND $data['invert_operation_id'] > 0){
			
			$sql = 'UPDATE ' . $this->pp . 'operation
					SET invert_operation_id = "'.$operation_id.'"
					WHERE operation_id = "'.(int)$data['invert_operation_id'].'"';
			$this->db->query($sql) or die($sql);
		}
		
		return $operation_id;
	}

	public function getTypes(){
		
		$pp = $this->pp;
		
		$sql = 'SELECT * FROM `'.$this->pp.'operation_type` ORDER BY name;';
		//echo $sql;
		$r = $this->db->query($sql);
		
		$data = array();
		if($r->num_rows > 0){
			while($row = $r->fetch_assoc()){
				$data[$row['type_id']] = $row;
			}
		}
	
		return $data;
		
	}

}

?>
