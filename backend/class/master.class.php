<?php

class Master
{
	private $db;
	private $pp;
	
    function __construct (){
		
		$this->pp = DB_PREFIX;
		
		//Новое соединение с базой
		$this->db = mysqli_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE) or die("Error db connection "); 
		mysqli_set_charset($this->db,"utf8");
		
	}
		
    public function getMasters(){
		$pp = $this->pp;
		
		$sql = 'SELECT
					*
					FROM `'.$pp.'master` C
					ORDER BY sort_order, `name`;';
		//echo $sql;
		$r = $this->db->query($sql);
		
		if($r->num_rows > 0){
			$return = array();
			while($tmp = $r->fetch_assoc()){
				$return[$tmp['master_id']] = $tmp;
			}
			return $return;
		}
		
		return false;
		
	}

	

	
	
}

?>
