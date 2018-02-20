<?php

include('../../config.php');
include('../config.php');
session_start();
	
include __DIR__."/../class/user.class.php";
$User = new User();
	
	
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

if($key == 'set_session'){
    
	$_SESSION[$data['index']] = $data['value']; 
	
}

if($key == 'set_user_session'){
    
	if($data['value'] == 'on') $data['value'] = true;
	
	$_SESSION['default'][$data['index']] = $data['value']; 
	
	echo '<pre>'; print_r(var_dump( $data  ));
	
}

		
if(isset($_GET['key'])){
	
	if($_GET['key'] == 'set_param'){
		
		$User->setParam($_GET['param'], $_GET['value']);
		
	}
	
}
	

?>