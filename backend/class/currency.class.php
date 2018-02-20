<?php

class Currency
{
	private $db;
	private $pp;
	
    function __construct (){
		
		$this->pp = DB_PREFIX;
		
		//Новое соединение с базой
		$this->db = mysqli_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE) or die("Error db connection "); 
		mysqli_set_charset($this->db,"utf8");
		
	}
		
    public function getCurrencys(){
		$pp = $this->pp;
		
		$sql = 'SELECT
					*
					FROM `'.$pp.'currency` C
					ORDER BY `title`;';
		//echo $sql;
		$r = $this->db->query($sql);
		
		if($r->num_rows > 0){
			$return = array();
			while($tmp = $r->fetch_assoc()){
				
				$tmp['name'] = $tmp['title'];
				
				$return[$tmp['currency_id']] = $tmp;
				
			}
			return $return;
		}
		
		return false;
		
	}

	

	
	
}

?>
