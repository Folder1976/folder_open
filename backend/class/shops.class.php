<?php

class Shops
{
	private $db;
	private $pp;
	
    function __construct (){
		
		$this->pp = DB_PREFIX;
		
		//Новое соединение с базой
		$this->db = mysqli_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE) or die("Error db connection "); 
		mysqli_set_charset($this->db,"utf8");
		
	}
	

	
	public function getShop($id){
		$pp = $this->pp;
		
		$sql = 'SELECT * FROM `'.$pp.'shops` WHERE shop_id = "'.$id.'";';
		//echo $sql;
		$r = $this->db->query($sql);
		
		if($r->num_rows > 0){
			$tmp = $r->fetch_assoc();
			return $tmp;
		}
		
		return 0;
		
	}


	public function getShops(){
		$pp = $this->pp;
		
		$sql = 'SELECT * FROM `'.$pp.'shops` ORDER BY sort_order, name;';
		//echo $sql;
		$r = $this->db->query($sql);
		
		$return = array();
		if($r->num_rows > 0){
			while($tmp = $r->fetch_assoc()){
				$return[$tmp['shop_id']] = $tmp;
			}
		}
		
		return $return;
		
	}
	
	public function isNewPrice(){
		
		if(!isset($_SESSION["default"]['shop_id'])) return 0;
		
		
		$sql = 'SELECT reprice_id FROM '.$this->pp.'product_reprice_print WHERE shop_id="'.(int)$_SESSION["default"]['shop_id'].'"';
		$r = $this->db->query($sql);
		
		if($r->num_rows > 0){
			
			$tmp = $r->fetch_assoc();
			$date = $tmp['reprice_id'];
			
		}else{
			
			$date = 0;
			
		}
		
		$sql = 'SELECT count(reprice_id) as reprice_id FROM '.$this->pp.'product_reprice WHERE reprice_id >"'.(int)$date.'"';
		$r = $this->db->query($sql);
		
		//echo $sql;
		
		if($r->num_rows > 0){
			$row = $r->fetch_assoc();
			return $row['reprice_id'];
		}else{
			return 0;
		}
		
		
	}

}

?>
