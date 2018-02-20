<?php
include "../config.php";
include "../core.php";
header("Content-Type: text/html; charset=UTF-8");
?>

<!-- Sergey Kotlyarov 2017 folder.list@gmail.com -->

<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/backend.css">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/product.css">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/operation.css">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/print.css">
<header>
	<title>Печать новых цен</title>
</header>

<?php

//=====================================================================================================================
//=====================================================================================================================
//=====================================================================================================================
//=====================================================================================================================\

		if($_GET['key'] == 'price'){
			$price_f = 'PRP.price As rep_price,';
			$price_t = 'product_reprice';
		}else{
			$price_f = 'PRP.zakup As rep_price,';
			$price_t = 'product_rezakup';
		}

		$sql = 'SELECT PRP.date As rep_date,
					'.$price_f.'
					PRP.reprice_id,
					P.code AS product_code,
					P.*,
					U.*
				FROM '.DB_PREFIX.$price_t.' PRP
				LEFT JOIN '.DB_PREFIX.'product P ON P.product_id = PRP.product_id
				LEFT JOIN '.DB_PREFIX.'user U ON U.user_id = PRP.user_id
				WHERE PRP.product_id IN ('.$_GET['product_ids'].')
				ORDER BY PRP.date DESC
				';
		$r = $mysqli->query($sql) or die($sql);
		//echo $sql;
		
		$reprice_id = 0;
		
		$products = array();
		
		 while($row = $r->fetch_assoc()){
			
			$products[$row['product_code']][] = $row;
			
			if($reprice_id < $row['reprice_id']) $reprice_id = (int)$row['reprice_id'];
			
		}

//echo '<pre>'; print_r(var_dump( $users  ));

?>

<table class="print_body">
	<tr>
		<th colspan="5">Распечатано <?php echo date('Y-m-d H:i:s'). ' ' . $users[$_SESSION['default']['user_id']]['lastname'];?> </th>
	</tr>
    <tr>
		<!--th>Картинка</th-->
		<th>Индекс</th>
		<th>ШтрихКод</th>
		<th>Дата</th>
		<th>Цена</th>
		<th>Изменил</th>
	</tr>
	
	<?php foreach($products as $rows){ 
				
		$tmp = $rows;
		$row = array_shift($tmp);
		unset($tmp);
		
		?>
		<tr>
			<!--td><img class="product_image" src="<?php echo '/image/'.$ex['image'];?>"></td-->	
			<td><?php echo $row['model']; ?></td>
			<td><?php echo $row['product_code']; ?></td>
			<td><?php echo $row['rep_date']; ?></td>
			<td style="text-align: right;font-weight: bold;"><?php echo number_format($row['rep_price'],2,'.',' '); ?></td>
			<td><?php echo $row['firstname'].' '.$row['lastname']; ?></td>
		</tr>
		
	<?php } ?>
	

</table>

