<?php

class Attributes
{
	private $db;
	private $pp;
	
    function __construct (){
		$this->pp = DB_PREFIX;
		
		//Новое соединение с базой
		$this->db = mysqli_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE) or die("Error db connection "); 
		mysqli_set_charset($this->db,"utf8");
		
	}
		
    public function getAttributeGroups(){
		
		$sql = 'SELECT * FROM `'.$this->pp.'attribute_group` AG
					LEFT JOIN `'.$this->pp.'attribute_group_description` AGD ON AG.attribute_group_id = AGD.attribute_group_id
					WHERE `enable` = "1"
					ORDER BY sort_order, name
					;';
		$r = $this->db->query($sql) or die($sql);
		
		$return = array();
		if($r->num_rows > 0){
			while($row = $r->fetch_assoc()){
				$return[$row['attribute_group_id']] = $row;
				
			}
		}
		
		return $return;
		
	}


    public function getAttributes($attribute_group_id = 0){
		
		$sql = 'SELECT * FROM `'.$this->pp.'attribute` AG
					LEFT JOIN `'.$this->pp.'attribute_description` AGD ON AG.attribute_id = AGD.attribute_id
					WHERE `enable` = "1" ';
		
		if($attribute_group_id > 0){
			$sql .= ' AND attribute_group_id = "'.$attribute_group_id.'"';
		}
		$sql .= ' ORDER BY sort_order, name;';
		
		$r = $this->db->query($sql) or die($sql);
		
		$return = array();
		if($r->num_rows > 0){
			while($row = $r->fetch_assoc()){
				$return[$row['attribute_id']] = $row;
				
			}
		}
		
		return $return;
		
	}

   public function getAttribute($attribute_id){
		
		$sql = 'SELECT A.attribute_id,
						A.sort_order,
						A.enable,
						A.index,
						A.attribute_group_id,
						AD.name,
						AG.sort_order AS group_sort_order,
						AG.enable AS group_enable,
						AGD.name AS group_name
					FROM `'.$this->pp.'attribute` A
					LEFT JOIN `'.$this->pp.'attribute_description` AD ON AD.attribute_id = A.attribute_id
					LEFT JOIN `'.$this->pp.'attribute_group` AG ON AG.attribute_group_id = A.attribute_group_id
					LEFT JOIN `'.$this->pp.'attribute_group_description` AGD ON AG.attribute_group_id = AGD.attribute_group_id
					WHERE A.`attribute_id` = "'.$attribute_id.'" LIMIT 1;';
		
		
		$r = $this->db->query($sql) or die($sql);
		
		$return = array();
		if($r->num_rows > 0){
			
			$row = $r->fetch_assoc();
			return $row;
			
		}
		
		return false;
		
	}
	
	public function echoAttributeList1($data){
	?>
		<div>
			<a href="javascript:;" class="dell_attribute" data-product_id="<?php echo $data['product_id'];?>" data-attribute_id="<?php echo $data['attribute_id'];?>">
				<img src="/<?php echo TMP_DIR; ?>backend/img/cancel.png" title="удалить" width="12" height="12">
			</a>
			<b><?php echo $data['group_name'];?> : </b><?php echo $data['name'];?>
		</div>
	<?php
	}

	public function addAttribute($data){
		
		$sql = 'INSERT INTO `' . DB_PREFIX . 'attribute` SET
						`attribute_group_id`="'.$data['attribute_group_id'].'",
						`filter_name`="'.$data['filter_name'].'",
						`sort_order`="'.$data['sort_order'].'",
						`enable`="'.$data['enable'].'",
						`index`="'.$data['index'].'",
						`attribute_type`="'.$data['attribute_type'].'"';
						
		$this->db->query($sql) or die('sad54y1;adbf;j '.$sql);
		
		$attribute_id = $this->db->insert_id;
		
		$sql = 'INSERT INTO `' . DB_PREFIX . 'attribute_description` SET
						`attribute_id`="'.$attribute_id.'",
						`language_id`="'.$data['language_id'].'",
						`name`="'.$data['name'].'"';
						
		$this->db->query($sql) or die('sad54yfljs3;adbf;j '.$sql);
		
		return $attribute_id;
		
	}

	
}

?>
