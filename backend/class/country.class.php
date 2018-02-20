<?php

class Country
{
	private $db;
	private $pp;
	
    function __construct (){
		
		$this->pp = DB_PREFIX;
		
		//Новое соединение с базой
		$this->db = mysqli_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE) or die("Error db connection "); 
		mysqli_set_charset($this->db,"utf8");
		
	}
		
    public function getCountrys(){
		$pp = $this->pp;
		
		$sql = 'SELECT
					*
					FROM `'.$pp.'country` C
					LEFT JOIN `'.$pp.'country_description` CD ON CD.country_id = C.country_id AND language_id = 1
					ORDER BY `sort_order`,`name`;';
		//echo $sql;
		$r = $this->db->query($sql);
		
		if($r->num_rows > 0){
			$return = array();
			while($tmp = $r->fetch_assoc()){
				$return[$tmp['country_id']] = $tmp;
			}
			return $return;
		}
		
		return false;
		
	}

	
    public function getCountry($country_id){
		$pp = $this->pp;
		
		$sql = 'SELECT
					*
					FROM `'.$pp.'country` C
					LEFT JOIN `'.$pp.'country_description` CD ON CD.country_id = C.country_id AND language_id = 1
					
					WHERE C.country_id = "'.$country_id.'";';
		//echo $sql;
		$r = $this->db->query($sql);
		
		if($r->num_rows > 0){
			$tmp = $r->fetch_assoc();
			return $tmp;
		}
		
		return false;
		
	}

	
	
}

?>
