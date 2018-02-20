<?php

class Size
{
	private $db;
	private $pp;
	
    function __construct ($conn = '', $pp = ''){
		$this->pp = DB_PREFIX;
		
		//Новое соединение с базой
		$this->db = mysqli_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE) or die("Error db connection "); 
		mysqli_set_charset($this->db,"utf8");
	}
		
    public function getSizeGroups(){
		$pp = $this->pp;
		
		$sql = 'SELECT * FROM `'.$pp.'size_group` where `enable` = "1" ORDER BY `sort`, `name`;';
		//echo $sql;
		$r = $this->db->query($sql) or die($sql);
		
		if($r->num_rows > 0){
			$return = array();
			while($tmp = $r->fetch_assoc()){
				$return[$tmp['id']] = $tmp;
			}
			return $return;
		}
		
		return 0;
		
	}

	public function getProductSizeStandart($product_id){
		$pp = $this->pp;
		
		$sql = 'SELECT size_group_id
					FROM `'.$pp.'product`
					WHERE product_id = "'.$product_id.'" LIMIT 0,1;';
		//echo $sql;
		$r = $this->db->query($sql) or die($sql);
		
		if($r->num_rows > 0){
			
			$tmp = $r->fetch_assoc();
			return $tmp['size_group_id'];
		}
		
		return 0;
		
	}

	public function resetAllProductQantityOnShopId($shop_id){
		$pp = $this->pp;
		
		$sql = 'SELECT * FROM `'.$pp.'size_group` where `enable` = "1" ORDER BY `sort`, `name`;';
		
	}
	
	
	public function getSizes($group_id = 0){
		$pp = $this->pp;
		
		$sql = 'SELECT * FROM `'.$pp.'size` where `enable` = "1" ';
		
		if($group_id > 0){
			
			$sql .= ' WHERE group_id = "'.$group_id.'" ';
			
		}
		
		$sql .= ' ORDER BY `sort`, `name`;';
		//echo $sql;
		$r = $this->db->query($sql) or die($sql);
		
		if($r->num_rows > 0){
			$return = array();
			
			while($tmp = $r->fetch_assoc()){
				$return[$tmp['size_id']] = $tmp;
			}
			return $return;
		}
		
		return 0;
		
	}

	
	
}

?>
