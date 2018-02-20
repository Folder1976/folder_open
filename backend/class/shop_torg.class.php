<?php

class ShopTorg
{
	private $db;
	private $pp;
	private $warehouse, $operation, $warehouses, $warehouses_shop;
	
    function __construct (){
		
		global $Warehouse, $Operation;
		
		$this->pp = DB_PREFIX;
		$this->warehouse = $Warehouse;
		$this->operation = $Operation;
		
		global $warehouses, $warehouses_shop;
		
		$this->warehouses = $warehouses;
		$this->warehouses_shop = $warehouses_shop;
		
		//Новое соединение с базой
		$this->db = mysqli_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE) or die("Error db connection "); 
		mysqli_set_charset($this->db,"utf8");
		
	}
	
	public function getSubOperation($operation_id){
		
		$sql = 'SELECT MAX(sub_operation_id) AS id
					FROM `'.$this->pp.'operation_product` WHERE
					operation_id = '.$operation_id.';';
		
		$r = $this->db->query($sql)or die($sql);
		
		if($r->num_rows > 0){
			$tmp = $r->fetch_assoc();
			return (int)$tmp['id'] + 1;
		}
		
		return 1;
			
	}
	
	public function getOperationShop($operation_id, $is_return = 0){
		$sql = 'SELECT from_warehouse_id, to_warehouse_id FROM `'.$this->pp.'operation` WHERE
					operation_id = '.(int)$operation_id.'
					LIMIT 1;';
		//die($sql);
		$r = $this->db->query($sql)or die($sql);
		
		if($r->num_rows > 0){
			$tmp = $r->fetch_assoc();
			
			//Если это операция возврата то иной склад
			if($tmp['from_warehouse_id'] < 0 or $is_return){
				return (int)$this->warehouses[$tmp['to_warehouse_id']]['shop_id'];
			}else{
				return (int)$this->warehouses[$tmp['from_warehouse_id']]['shop_id'];
			}
		}
		
		return 0;
		
	}
    public function addOperation($shop_id, $type_id = 8){
		$pp = $this->pp;
		
		$from_warehouse_id = -1;
		$to_warehouse_id = -1;
		
		if($type_id == 8){
			$from_warehouse_id = $this->warehouse->getMainWarehouse($shop_id);
		}else{
			$to_warehouse_id = $this->warehouse->getMainWarehouse($shop_id);
		}
		
		$sql = 'SELECT operation_id
					FROM `'.$this->pp.'operation` WHERE
					 DATE_FORMAT( `date` , "%Y-%m-%d" )  = "'.date('Y-m-d').'" AND
					`type_id` = "'.$type_id.'" AND
					from_warehouse_id = '.$from_warehouse_id.' AND
					to_warehouse_id = '.$to_warehouse_id.'
					ORDER BY operation_id 
					LIMIT 1;';
		//die($sql);
		$r = $this->db->query($sql)or die($sql);
		
		if($r->num_rows > 0){
			$tmp = $r->fetch_assoc();
			return $tmp['operation_id'];
		}else{
			
			$data = array();
			$data['customer_id'] = 1;
			$data['comment'] = '';
			$data['sub_operation_id'] = 1;
			$data['type_id'] = $type_id;
			$data['from_warehouse_id'] = $from_warehouse_id;
			$data['to_warehouse_id'] = $to_warehouse_id;
			return $this->operation->addOperation($data);

		}
		
		
		
		return 0;
		
	}
		
	public function getUsers(){
		$pp = $this->pp;
		
		$sql = 'SELECT * FROM `'.$this->pp.'user` ORDER BY lastname, firstname;';
		//echo $sql;
		$r = $this->db->query($sql);
		
		$data = array();
		if($r->num_rows > 0){
			while($row = $r->fetch_assoc()){
				$data[$row['user_id']] = $row;
			}
		}
	
		return $data;
		
	}


	public function getUserMainMenu($user_id){
		$pp = $this->pp;
		
		$sql = 'SELECT modul_group_id, modul_group_nazv FROM `'.$this->pp.'user_moduls_groups` ORDER BY modul_group_poz;';
		//echo $sql;
		$r = $this->db->query($sql);
		
		$data = array();
		if($r->num_rows > 0){
			while($tmp = $r->fetch_assoc()){
				$data[$tmp['modul_group_id']]['name'] = $tmp['modul_group_nazv'];
			}
		}
		
		$sql = 'SELECT
					UR.ua_modul_id AS id,
					MS.ua_modul_nazv AS name,
					MS.ua_modul_dir AS dir,
					MS.is_show,
					MS.ua_modul_mfile AS file,
					MS.ua_modul_icon AS icon,
					MS.ua_modul_icon AS icon,
					MS.modul_group_id
					FROM `'.$this->pp.'user_moduls_rights` UR
					LEFT JOIN `'.$this->pp.'user_moduls_spis` MS ON MS.ua_modul_id = UR.ua_modul_id
					WHERE UR.uadm_flag = "1" AND uadm_id = "'.$user_id.'" AND modul_submenu = "0"
					ORDER BY UR.ua_modul_poz, MS.modul_sort;';
		//echo $sql;
		$r = $this->db->query($sql);
		if($r->num_rows > 0){
			while($tmp = $r->fetch_assoc()){
				$data[$tmp['modul_group_id']]['menu'][$tmp['id']]['id'] = $tmp['id'];
				$data[$tmp['modul_group_id']]['menu'][$tmp['id']]['name'] = $tmp['name'];
				$data[$tmp['modul_group_id']]['menu'][$tmp['id']]['dir'] = $tmp['dir'];
				$data[$tmp['modul_group_id']]['menu'][$tmp['id']]['file'] = $tmp['file'];
				$data[$tmp['modul_group_id']]['menu'][$tmp['id']]['is_show'] = $tmp['is_show'];
				$data[$tmp['modul_group_id']]['menu'][$tmp['id']]['icon'] = $tmp['icon'];
			}
		}
		
		return $data;
		
	}

	public function getUserSubMenu($user_id){
		$pp = $this->pp;
		
		$sql = 'SELECT modul_group_id, modul_group_nazv FROM `'.$this->pp.'user_moduls_groups` ORDER BY modul_group_poz;';
		//echo $sql;
		$r = $this->db->query($sql);
		
		$data = array();
		if($r->num_rows > 0){
			while($tmp = $r->fetch_assoc()){
				$data[$tmp['modul_group_id']]['name'] = $tmp['modul_group_nazv'];
			}
		}
		
		$sql = 'SELECT
					UR.ua_modul_id AS id,
					MS.modul_submenu,
					MS.ua_modul_nazv AS name,
					MS.ua_modul_dir AS dir,
					MS.is_show,
					MS.ua_modul_mfile AS file,
					MS.ua_modul_icon AS icon,
					MS.ua_modul_icon AS icon,
					MS.modul_group_id
					FROM `'.$this->pp.'user_moduls_rights` UR
					LEFT JOIN `'.$this->pp.'user_moduls_spis` MS ON MS.ua_modul_id = UR.ua_modul_id
					WHERE UR.uadm_flag = "1" AND uadm_id = "'.$user_id.'" AND modul_submenu > "0"
					ORDER BY UR.ua_modul_poz, MS.modul_sort;';
		//echo $sql;
		$r = $this->db->query($sql);
		if($r->num_rows > 0){
			while($tmp = $r->fetch_assoc()){
				$data[$tmp['modul_submenu']][$tmp['id']]['id'] = $tmp['id'];
				$data[$tmp['modul_submenu']][$tmp['id']]['name'] = $tmp['name'];
				$data[$tmp['modul_submenu']][$tmp['id']]['dir'] = $tmp['dir'];
				$data[$tmp['modul_submenu']][$tmp['id']]['file'] = $tmp['file'];
				$data[$tmp['modul_submenu']][$tmp['id']]['is_show'] = $tmp['is_show'];
				$data[$tmp['modul_submenu']][$tmp['id']]['icon'] = $tmp['icon'];
			}
		}
		
		return $data;
		
	}

}

?>
