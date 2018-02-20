<?php

class Customer
{
	private $db;
	private $pp;
	
    function __construct (){
		
		$this->pp = DB_PREFIX;
		
		//Новое соединение с базой
		$this->db = mysqli_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE) or die("Error db connection "); 
		mysqli_set_charset($this->db,"utf8");
		
	}
	

	
	public function getCustomer($id){
		$pp = $this->pp;
		
		$sql = 'SELECT * FROM `'.$pp.'customer` WHERE customer_id = "'.$id.'";';
		//echo $sql;
		$r = $this->db->query($sql);
		
		if($r->num_rows > 0){
			$tmp = $r->fetch_assoc();
			return $tmp;
		}
		
		return 0;
		
	}


	public function getCustomers($group_id = '0', $filter_data = array()){
		$pp = $this->pp;
		
		$sql = 'SELECT * FROM `'.$pp.'customer` WHERE 1 ';
		
		if(isset($filter_data['name'])){
			$sql .= 'AND ( upper(`firstname`) LIKE "%'.mb_strtoupper(addslashes($filter_data['name']),'UTF-8').'%" OR ';
			$sql .= ' upper(`lastname`) LIKE "%'.mb_strtoupper(addslashes($filter_data['name']),'UTF-8').'%" OR ';
			$sql .= ' upper(`name`) LIKE "%'.mb_strtoupper(addslashes($filter_data['name']),'UTF-8').'%" )';
		}
		
		if($group_id != '0'){
			$sql .= ' AND customer_group_id IN ('.$group_id.') ';
		}
		
		$sql .=	' ORDER BY lastname, firstname';	
		
		if(isset($filter_data['limit'])){
			$sql .= ' LIMIT '.$filter_data['limit'];
		}
		
		//echo $sql;
		$r = $this->db->query($sql);
		
		$return = array();
		if($r->num_rows > 0){
			while($tmp = $r->fetch_assoc()){
				$return[$tmp['customer_id']] = $tmp;
			}
		}
		
		return $return;
		
	}



	public function getCustomerGroups(){
		$pp = $this->pp;
		
		$sql = 'SELECT * FROM `'.$pp.'customer_group` CG
						LEFT JOIN `'.$pp.'customer_group_description` CGD ON CG.customer_group_id = CGD.customer_group_id
						WHERE CGD.language_id = 1 ORDER BY CG.sort_order, CGD.name;';
		
		//echo $sql;
		$r = $this->db->query($sql);
		
		$return = array();
		if($r->num_rows > 0){
			while($tmp = $r->fetch_assoc()){
				$return[$tmp['customer_group_id']] = $tmp;
			}
		}
		
		return $return;
	}
	
}

?>
