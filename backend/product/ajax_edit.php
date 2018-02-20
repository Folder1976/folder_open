<?php

include('../../config.php');
include('../config.php');
session_start();

	$key = 'exit';
    $table = '';
    $id = '';
	$mainkey = 'id';
	$radio_name = '';
    $data = array();
	$find = array('*1*', '@*@');
	$replace = array('=', '&');
    
foreach($_POST as $index => $value){
    
    //echo '++++    '.$index.'='.$value;
 
	
    if($index == 'key'){
        $key = $value;
    }elseif($index == 'table'){
        $table = $value;
    }elseif($index == 'id'){
        $id = str_replace($find,$replace,$value);
    }elseif($index == 'language_id'){
        $language_id = $value;
    }elseif($index == 'mainkey'){
        $mainkey = $value;
    }elseif($index == 'radio_name'){
        $radio_name = $value;
    }else{
        $data[$index] = str_replace($find,$replace,$value);
    }
}

if($key == 'get_images'){
    
	$return = array();
	
	$sql = "SELECT image FROM " . DB_PREFIX . "product WHERE product_id='$id' LIMIT 1";
	$r = $mysqli->query($sql) or die('sad54yfljsad bf;j '.$sql);
	
	if($r->num_rows){
		
		$row = $r->fetch_assoc();
		$return['main'] = $row['image'];
		
		
	}
	
	$sql = "SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id='$id'";
	$r = $mysqli->query($sql) or die('sad54yfljsad bf;j '.$sql);
	
	if($r->num_rows){
		
		while($row = $r->fetch_assoc()){
			$return[$row['product_image_id']] = $row['image'];
		}
		
	}
	?>
	
	<div class="load_photo">
		<b style="color:#005100;">Загрузить много фото</b>
		<form enctype="multipart/form-data" method="post"
			 
			  action="/admin/index.php?route=catalog/product/edit&amp;token=<?php echo $_SESSION['default']['token'];?>&amp;product_id=<?php echo $id;?>">
			<input type="hidden" name="MAX_FILE_SIZE" value="1151022592">
			<input type="hidden" name="redirect" value="/backend/msg/add_image.php">
			<input type="hidden" name="type" value="tovar">
			<input type="hidden" name="product_id" value="<?php echo $id;?>">
			<input type="file" min="1" max="999" multiple="true" style="width:200px" name="userfile[]" onchange="submit();">
		</form>
	</div>
	<?php
	foreach($return as $index => $row){ ?>
		
		<div class="image_item" id="img_<?php echo $index; ?>">
			<a href="javascript:;" class="image_item_dell" data-id="<?php echo $index;?>" data-product_id="<?php echo $id;?>">
                <img src="/<?php echo TMP_DIR; ?>backend/img/cancel.png" title="удалить" width="16" height="16">
            </a>
			<img class="image_item_image" src="/image/<?php echo $row; ?>">
		</div>
		
	<?php }
	
	
	//echo json_encode($return);
	
}else if($key == 'dell_image'){
	
	if($id == 'main'){
		$sql = "UPDATE " . DB_PREFIX . "product SET image='' WHERE `product_id` = '" . $data['product_id'] . "'";
	}else{
		$sql = "DELETE FROM " . DB_PREFIX . "product_image WHERE `product_image_id` = '" . $id . "'";	
	}
	
	echo $sql;
	
	$mysqli->query($sql) or die('sadlkjgfljsad bf;j '.$sql);
	
}


// =============================================

if($key == 'set_radio'){
    
	$sql = "UPDATE " . DB_PREFIX . $table . " SET `$radio_name` = '0'";
	$mysqli->query($sql) or die('sadlkjgfljsad bf;j '.$sql);
	
//echo $sql;
	$sql = "UPDATE " . DB_PREFIX . $table . " SET `$radio_name` = '1' WHERE `$mainkey` = '" . $id . "'";
	$mysqli->query($sql) or die('sadlkjgfljsad bf;j '.$sql);
	
//echo $sql;
	
		
}

if($key == 'edit'){
    
	$sql = "UPDATE " . DB_PREFIX . $table . " SET ";
	foreach($data as $index => $value){
		 $sql .= " `$index` = '$value',";		
	}
	$sql = trim($sql, ',');
	$sql .=	" WHERE `$mainkey` = '" . $id . "'";
	
	if(isset($language_id)){
		$sql .=	" AND `language_id` = '" . $language_id . "'";
	}
	
echo $sql;

	$mysqli->query($sql) or die('sadlkjgfljsad bf;j '.$sql);
		
}
if($key == 'copy'){
    
	$mysqli->query("CREATE TEMPORARY TABLE foo AS SELECT * FROM " . DB_PREFIX . $table . " WHERE `$mainkey` = '" . $id . "'") or die('1');
	$mysqli->query("UPDATE foo SET `$mainkey`=NULL;") or die('2');
	$mysqli->query("INSERT INTO " . DB_PREFIX . $table . " SELECT * FROM foo;") or die('3');
	$mysqli->query("DROP TABLE foo;") or die('4');
		
}

if($key == 'dell'){
	
	$sql = "DELETE FROM " . DB_PREFIX . $table ." WHERE `$mainkey` = '" . $id . "'";
	echo $sql;
	$mysqli->query($sql) or die('sadlkjgfljsad bf;j '.$sql);
	
}

?>