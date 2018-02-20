<!-- Sergey Kotlyarov 2016 folder.list@gmail.com -->
<?php
$file = explode('/', __FILE__);
if(strpos($_SERVER['PHP_SELF'], $file[count($file)-1]) !== false){
	header("Content-Type: text/html; charset=UTF-8");
	die('Прямой запуск запрещен!');
}

$key = 'operation_id';

include_once "class/operation.class.php";
$Operation = new Operation();

$sql = 'DELETE FROM product_id_warehouse';
$mysqli->query($sql);

$sql = 'SELECT product_id FROM ' . DB_PREFIX . 'product';
$r = $mysqli->query($sql);

while($row = $r->fetch_assoc()){
	
	$Operation->updateWarehouseItems($row['product_id']);
}

Echo 'готов';
