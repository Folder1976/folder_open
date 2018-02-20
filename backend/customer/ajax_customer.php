<?php

include('../../config.php');
include('../config.php');
include('../core.php');

include __DIR__."/../class/customer.class.php";
$Customer = new Customer();


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
if($key == 'get_customers'){
	
	/* $data['name']
	 * $data['limit']
	*/
	
	$data['limit'] = 10;
	
	if(!isset($_POST['group_id'])) $_POST['group_id'] = 0;
	$group_id = $_POST['group_id'];
	
	$customers = $Customer->getCustomers($group_id, $data);
	
	if($customers){
		
		foreach($customers as $customer_id => $row){ ?>
			
			<a href="javascript:;" class="customer_list" data-customer_id="<?php echo $customer_id; ?>"><?php echo $row['firstname'].' '.$row['lastname'].' ('.$row['name'].')';?></a><br><br>
			
		<?php }
		
	}else{
		
		echo 'Не нашел';
		
	}
}

?>