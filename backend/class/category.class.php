<?php

class Category
{
	private $db;
	private $pp;
	
    
    function __construct ($conn=false, $pp=false){
		
		$this->pp = DB_PREFIX;
		
		//Новое соединение с базой
		$this->db = mysqli_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE) or die("Error db connection "); 
		mysqli_set_charset($this->db,"utf8");
	}
	
	public function getCategoryIdOnAlternativeName($id, $name, $shop_id){
		$pp = $this->pp;
		
		$sql = 'SELECT CA.category_id AS id, CD.name FROM `'.$pp.'category_alternative` CA
					LEFT JOIN `'.$pp.'category_description` CD ON CD.category_id = CA.category_id
					WHERE
				alt_category_id = "'.$id.'" AND
				shop_id = "'.$shop_id.'" ORDER BY id DESC;';
		//echo '<br>'.$sql;
		$r = $this->db->query($sql);
		
		if($r->num_rows > 0){
			$tmp = $r->fetch_assoc();
			return $tmp;
		}
	
		
		$sql = 'SELECT CA.category_id AS id, CD.name FROM `'.$pp.'category_alternative` CA
							LEFT JOIN `'.$pp.'category_description` CD ON CD.category_id = CA.category_id
							WHERE
				(upper(`alt_category_name`) = "'.mb_strtoupper(addslashes($name),'UTF-8').'" AND	shop_id = "'.$shop_id.'") OR
				(upper(`alt_category_name`) = "'.mb_strtoupper(addslashes($name),'UTF-8').'" AND	shop_id = "0");';
		//echo $sql;
		$r = $this->db->query($sql);
		
		if($r->num_rows > 0){
			$tmp = $r->fetch_assoc();
			return $tmp;
		}
		
		return false;
		
	}

	
	public function getCategory($category_id){
		
		$sql = "SELECT DISTINCT *, code as keyword,
							(SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;')
				FROM " . DB_PREFIX . "category_path cp
				LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id AND
							cp.category_id != cp.path_id) WHERE cp.category_id = c.category_id AND
							cd1.language_id = '1'
						GROUP BY cp.category_id) AS path /*,
				(SELECT DISTINCT keyword FROM " . DB_PREFIX . "url_alias
					WHERE query = 'category_id=" . (int)$category_id . "') AS keyword */
				FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd2 ON
						(c.category_id = cd2.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND
						cd2.language_id = '1'";
		$r = $this->db->query($sql);

		if($r->num_rows > 0){
			$tmp = $r->fetch_assoc();
			
			$tmp['path'] .= '&nbsp;&nbsp;&gt;&nbsp;&nbsp;'.$tmp['name'];
			
			return $tmp;
		}
		
		return false;
		
	}

	
	
}

?>
