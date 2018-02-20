<?php

include __DIR__."/class/user.class.php";
$User = new User();
$users = $User->getUsers();

include __DIR__."/class/master.class.php";
$Master = new Master();
$masters = $Master->getMasters();

include __DIR__."/class/size.class.php";
$Size = new Size();
$sizes = $Size->getSizes();
$size_group_list = $size_groups = $Size->getSizeGroups();
$sizes_on_groups = array();
foreach($sizes as $row){
	$sizes_on_groups[$row['group_id']][]= $row;
}

$sizes[0]['name'] = 'Без размера';

include __DIR__."/class/warehouse.class.php";
$Warehouse = new Warehouse();
$warehouses = $Warehouse->getWarehouses();

include __DIR__."/class/currency.class.php";
$Currency = new Currency();
$currencys = $Currency->getCurrencys();

include __DIR__."/class/shops.class.php";
$Shops = new Shops();
$shops = $Shops->getShops();
$shops[-1]['name'] = 'Приход/Возвраты';

//С деревом магазинов
foreach($warehouses as $row){
	$warehouses_shop[$row['shop_id']][$row['warehouse_id']] = $row;
}
$warehouses_shop[-1] = array('name' => 'Приход/Возвраты');

$warehouse_to_shop = array();
foreach($warehouses as $row){
	$warehouse_to_shop[$row['warehouse_id']] = $row['shop_id'];
}
$warehouse_to_shop[-1] = "-1";

$warehouses[0]['name'] = 'Приход';
$warehouses[-1]['name'] = 'Продажа';
$warehouses[-2]['name'] = 'Брак';
$warehouses[-3]['name'] = 'Кредит - Лежит у нас';
$warehouses[-4]['name'] = 'Примерка - Забрали';
$warehouses[-5]['name'] = 'Резерв - Лежит у нас';

include_once __DIR__."/class/operation.class.php";
$Operation = new Operation();

//Добавим магазины в общий список складов	
foreach($shops as $index => $shop){

	$warehouses[-($index)]['name'] = '<b>'.$shop['name'].'</b>';
}


include __DIR__."/class/product.class.php";
$Product = new Product();
	
include __DIR__."/class/category.class.php";
$Category = new Category();
