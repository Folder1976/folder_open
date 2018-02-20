<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

//include('../../config.php');
include('../config.php');
	
	$key = 'exit';
    $table = '';
    $id = '';
    $data = array();
    $find = array('*1*', '@*@');
	$replace = array('=', '&');
	
foreach($_POST as $index => $value){
    
    //echo ' '.$index.'='.$value;
    
    if($index == 'key'){
        $key = $value;
    }elseif($index == 'table'){
        $table = $value;
    }elseif($index == 'id'){
        $id = $value;
    }else{
        $data[$index] = str_replace($find,$replace,$value);
    }
}

$attribute_id = $attribute_group_id = $id;
//echo $key;
if($key == 'edit_attr_grp'){
    
	$mysqli->query("UPDATE " . DB_PREFIX . "attribute_group
						SET
						sort_order = '" . (int)$data['sort'] . "',
						enable = '" . (int)$data['enable'] . "'
						WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");

	$mysqli->query("DELETE FROM " . DB_PREFIX . "attribute_group_description WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");

	$mysqli->query("INSERT INTO " . DB_PREFIX . "attribute_group_description
				   SET attribute_group_id = '" . (int)$attribute_group_id . "',
				   description = '" . $data['description'] . "',
				   language_id = '1', name = '" . htmlspecialchars($data['name'],ENT_QUOTES) . "'");
	
	
}

if($key == 'add_attr_grp'){
    
   	$mysqli->query("INSERT INTO " . DB_PREFIX . "attribute_group SET
						enable = '" . (int)$data['enable'] . "',
						sort_order = '" . (int)$data['sort'] . "'");

	$attribute_group_id = $mysqli->insert_id;

	$mysqli->query("INSERT INTO " . DB_PREFIX . "attribute_group_description
						SET attribute_group_id = '" . (int)$attribute_group_id . "',
						description = '" . $data['description'] . "',
						language_id = '1', name = '" . htmlspecialchars($data['name'],ENT_QUOTES) . "'");
	
}

if($key == 'dell_attr_grp'){
   	
	$mysqli->query("DELETE FROM " . DB_PREFIX . "attribute_group WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");
	$mysqli->query("DELETE FROM " . DB_PREFIX . "attribute_group_description WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");

}

if($key == 'add_attr'){
    
	$mysqli->query("INSERT INTO " . DB_PREFIX . "attribute SET
					attribute_group_id = '" . (int)$data['group'] . "',
					filter_name = '" . $data['filter_name'] . "',
					enable = '" . (int)$data['enable'] . "',
					index = '" . (int)$data['index'] . "',
					sort_order = '" . (int)$data['sort'] . "'");

	$attribute_id = $mysqli->insert_id;

	$mysqli->query("INSERT INTO " . DB_PREFIX . "attribute_description SET
						attribute_id = '" . (int)$attribute_id . "',
						language_id = '1',
						name = '" . htmlspecialchars($data['name'],ENT_QUOTES) . "'");
	
}

if($key == 'edit_attr'){
    
	$sql = "UPDATE " . DB_PREFIX . "attribute SET
						attribute_group_id = '" . (int)$data['group'] . "',
						filter_name = '" . $data['filter_name'] . "',
						`enable` = '" . (int)$data['enable'] . "',
						`index` = '" . (int)$data['index'] . "',
						sort_order = '" . (int)$data['sort'] . "' WHERE attribute_id = '" . (int)$attribute_id . "'";
	//echo $sql;
	$mysqli->query($sql);

	$mysqli->query("DELETE FROM " . DB_PREFIX . "attribute_description WHERE attribute_id = '" . (int)$attribute_id . "'");

	$mysqli->query("INSERT INTO " . DB_PREFIX . "attribute_description SET
						attribute_id = '" . (int)$attribute_id . "',
						language_id = '1',
						name = '" . htmlspecialchars($data['name'], ENT_QUOTES) . "'");
		
}

if($key == 'dell_attr'){
	
	$mysqli->query("DELETE FROM " . DB_PREFIX . "attribute WHERE attribute_id = '" . (int)$attribute_id . "'");
	$mysqli->query("DELETE FROM " . DB_PREFIX . "attribute_description WHERE attribute_id = '" . (int)$attribute_id . "'");

}

// ====================================================================================
// ====================================================================================
// ====================================================================================
if($key == 'add_manufacturer'){
    
	$mysqli->query("INSERT INTO " . DB_PREFIX . "manufacturer SET
					country_id = '" . (int)$data['country_id'] . "',
					country_shop_id = '" . (int)$data['country_shop_id'] . "',
					code = '" . translitArtkl(strtolower($data['name'])) . "',
					enable = '" . (int)$data['enable'] . "',
					sort_order = '" . (int)$data['sort'] . "'");

	$manufacturer_id = $mysqli->insert_id;

	$mysqli->query("INSERT INTO " . DB_PREFIX . "manufacturer_description SET
						manufacturer_id = '" . (int)$manufacturer_id . "',
						name = '" . $data['name'] . "',
						title_h1 = '" . $data['name'] . "',
						description = '" . $data['description'] . "',
						meta_title = '" . $data['name'] . "',
						meta_description = '" . $data['name'] . "',
						meta_keyword = '" . $data['name'] . "',
						language_id = '1';");
	
	$mysqli->query("INSERT INTO " . DB_PREFIX . "url_alias SET
						`query` = 'manufacturer_id=" . $manufacturer_id . "',
						`keyword` = 'brand/" . translitArtkl(strtolower($data['name']))  . "'");
}
if($key == 'edit_manufacturer'){
    
	$mysqli->query("UPDATE " . DB_PREFIX . "manufacturer SET
					country_id = '" . (int)$data['country_id'] . "',
					country_shop_id = '" . (int)$data['country_shop_id'] . "',
					code = '" . translitArtkl(strtolower($data['name'])) . "',
					enable = '" . (int)$data['enable'] . "',
					sort_order = '" . (int)$data['sort_order'] . "'
					WHERE
					manufacturer_id = '" . (int)$id . "'");

	$manufacturer_id = $mysqli->insert_id;

	$mysqli->query("UPDATE " . DB_PREFIX . "manufacturer_description SET
						name = '" . $data['name'] . "',
						title_h1 = '" . $data['name'] . "',
						description = '" . $data['description'] . "',
						meta_title = '" . $data['name'] . "',
						meta_description = '" . $data['name'] . "',
						meta_keyword = '" . $data['name'] . "',
						language_id = '1'
						WHERE
						manufacturer_id = '" . (int)$id  . "';");
	
	$mysqli->query("UPDATE INTO " . DB_PREFIX . "url_alias SET
						`keyword` = 'brand/" . translitArtkl(strtolower($data['name']))  . "'
						WHERE
						`query` = 'manufacturer_id=" . $id  . "'");
}

if($key == 'dell_manufacturer'){
    
	$mysqli->query("DELETE FROM " . DB_PREFIX . "manufacturer 
					WHERE
					manufacturer_id = '" . (int)$id . "'");

	$manufacturer_id = $mysqli->insert_id;

	$mysqli->query("DELETE FROM " . DB_PREFIX . "manufacturer_description 
						WHERE
						manufacturer_id = '" . (int)$id . "';");
	
	$mysqli->query("DELETE FROM " . DB_PREFIX . "url_alias 
						WHERE
						`query` = 'manufacturer_id=" . $id . "'");
}
// ====================================================================================
// ====================================================================================
// ====================================================================================
if($key == 'add_country'){
    
	$sql = "INSERT INTO " . DB_PREFIX . "country SET
					name_old = '" . $data['name'] . "',
					iso_code_2 = '" . $data['iso_code_2'] . "',
					iso_code_3 = '" . $data['iso_code_3'] . "',
					enable = '" . (int)$data['enable'] . "',
					sort_order = '" . (int)$data['sort'] . "'";
	$mysqli->query($sql);
echo $sql;
	
	$country_id = $mysqli->insert_id;

	$mysqli->query("INSERT INTO " . DB_PREFIX . "country_description SET
						country_id = '" . (int)$country_id . "',
						name = '" . $data['name'] . "',
						language_id = '1';");
	
}
if($key == 'edit_country'){
    
	$sql = "UPDATE " . DB_PREFIX . "country SET
					name_old = '" . $data['name'] . "',
					iso_code_2 = '" . $data['iso_code_2'] . "',
					iso_code_3 = '" . $data['iso_code_3'] . "',
					enable = '" . (int)$data['enable'] . "',
					sort_order = '" . (int)$data['sort_order'] . "'
					WHERE
					country_id = '" . (int)$id . "'";
	$mysqli->query($sql);
	echo $sql;
	$manufacturer_id = $mysqli->insert_id;

	$mysqli->query("UPDATE " . DB_PREFIX . "country_description SET
						name = '" . $data['name'] . "',
						language_id = '1'
						WHERE
						country_id = '" . (int)$id  . "';");
	
}

if($key == 'dell_country'){
    
	$mysqli->query("DELETE FROM " . DB_PREFIX . "country 
					WHERE
					country_id = '" . (int)$id . "'");

	$manufacturer_id = $mysqli->insert_id;

	$mysqli->query("DELETE FROM " . DB_PREFIX . "country_description 
						WHERE
						country_id = '" . (int)$id . "';");
	
}

// ====================================================================================
// ====================================================================================
// ====================================================================================
if($key == 'add_customer_group'){
    
	$mysqli->query("INSERT INTO " . DB_PREFIX . "customer_group SET
					status = '" . (int)$data['status'] . "',
					sort_order = '" . (int)$data['sort_order'] . "'");

	$customer_group_id = $mysqli->insert_id;

	$mysqli->query("INSERT INTO " . DB_PREFIX . "customer_group_description SET
						customer_group_id = '" . (int)$customer_group_id . "',
						name = '" . $data['name'] . "',
						description = '" . $data['description'] . "',
						language_id = '1';");

}

if($key == 'edit_customer_group'){
    
	$mysqli->query("UPDATE " . DB_PREFIX . "customer_group SET
					status = '" . (int)$data['status'] . "',
					sort_order = '" . (int)$data['sort_order'] . "'
					WHERE
					customer_group_id = '" . (int)$id . "'");

	$manufacturer_id = $mysqli->insert_id;

	$mysqli->query("UPDATE " . DB_PREFIX . "customer_group_description SET
						name = '" . $data['name'] . "',
						description = '" . $data['description'] . "'
						WHERE
						language_id = '1' AND customer_group_id = '" . (int)$id  . "';");
	

}

if($key == 'dell_customer_group'){
    
	$mysqli->query("DELETE FROM " . DB_PREFIX . "customer_group 
					WHERE
					customer_group_id = '" . (int)$id . "'");

	$manufacturer_id = $mysqli->insert_id;

	$mysqli->query("DELETE FROM " . DB_PREFIX . "customer_group_description 
						WHERE
						customer_group_id = '" . (int)$id . "';");

}



	function translitArtkl($str) {
		$rus = array('и','і','є','Є','ї','\"','\'','.',' ','А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
	    $lat = array('u','i','e','E','i','','','','-','A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
		return str_replace($rus, $lat, $str);
	}
	

?>