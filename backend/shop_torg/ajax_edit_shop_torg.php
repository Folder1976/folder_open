<?php

include('../../config.php');
include('../config.php');
include('../core.php');

include "../class/shop_torg.class.php";
$ShopTorg = new ShopTorg();

	
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

if($key == 'add_oplat'){
	
	
	$Operation->addOplat($data);
	
}elseif($key == 'add_vozvrat'){
	
	
	$Operation->addVozvrat($data);
	

}

?>