<!-- Sergey Kotlyarov 2016 folder.list@gmail.com -->
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/libs/category_tree/type-for-get.css">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/product.css">
<link rel="stylesheet" type="text/css" href="/<?php echo TMP_DIR;?>backend/css/operation.css">
<script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/libs/category_tree/script-for-get.js"></script>
<script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/product/category_tree.js"></script>
<script>
	window.onscroll = function() {
		//var scrolled = window.pageYOffset || document.documentElement.scrollTop;
		//console.log( scrolled + 'px');
	}
		 
	$(window).scroll(function(){
						
            if ($('#thead').length > 0) {
                
                //if((($(window).scrollTop()+$(window).height())-200)>=$('#thead').offset().top){
                if(($(window).scrollTop()-50) >= $('#thead').offset().top){
					debugger;
					$('#header-t').show();
				}else{
					$('#header-t').hide();
                }
            }
        });
		  
</script>

<?php
$file = explode('/', __FILE__);
if(strpos($_SERVER['PHP_SELF'], $file[count($file)-1]) !== false){
	header("Content-Type: text/html; charset=UTF-8");
	die('Прямой запуск запрещен!');
}
?>

<style>
	h1{
		background-color: <?php echo $types[$type_id]['color']; ?>;
		padding: 10px;
		margin-bottom: 0px;
	}
	.size-box input{
		max-width: 30px;
		text-align: center;
		font-weight: bold;
		font-size: 13px;
		color: red;
		background-color: <?php echo $types[$type_id]['color']; ?>;
	}
	.print_icon_w{
		margin-left: 10px;
		width: 40px;
		top: 10px;
		position: relative;
	}
	#header-t {
		width: 90%;
		display: block;
		position: fixed;
		background: #fff;
		overflow: hidden;
		z-index: 1;
		margin-left: 30px;
		font-size: 12px;
		top: 0;
		
	}
	#header-t div {
		display: table-cell;
		padding: 5px;
		border: 1px solid #ccc;
		border-collapse: collapse;
		font-weight: bold;
		
	}
	#table {
		width: 90%;
		margin-left: 30px;
	}
	#header-t {
		display: none;
	}
	td, th {
		border: 1px solid #ccc;
		border-collapse: collapse;
		padding: 15px;
	}
</style>
<script>
	jQuery(document).ready(function($){
	var $table = $('#table'),
	 $header = $('#header-t'),
		$thead = $('#thead');
		$thead.find('th').each(function(){
			var $newdiv = $('<div />', {
				style: 'width:'+ $(this).width()+'px'
				
			});
			$newdiv.text($(this).text());
			$header.append($newdiv);
		});
		
		 var $viewport = $(window); 
	
		$viewport.scroll(function(){
		 $header.css({
			 left: -$(this).scrollLeft()
		 });
	
		});
	});
</script>
	
<h1>Наличие товаров</h1>

<div style="width: 90%">
<div class="table_body">
	
<?php

	$master_ids = array();
	$warehouse_ids = array();
	$warehouse_ids_colum = array();
	$size_ids = array();
	$size_ids_colum = array();
	
	$products = array();
	
		$sql = 'SELECT product_id, model, model7, model8, model4, code FROM '.DB_PREFIX.'product';
		
		if(isset($_GET['code']) AND $_GET['code'] != ''){
			
			if(isset($_GET['is_code']) AND $_GET['is_code'] == 1){
				$sql .= ' WHERE code LIKE "'.$_GET['code'].'" ';
			}else{
				$sql .= ' WHERE code LIKE "%'.$_GET['code'].'%" ';	
			}
			
		}
		
		$sql .= ' ORDER BY model';
		$r = $mysqli->query($sql) or die($sql);
		
		if($r->num_rows){
			
			while($row = $r->fetch_assoc()){
				
				$ware = $Warehouse->getProductsOnWareOnTableArray($row['product_id']);
				
				if(count($ware) == 0) continue;
				
				foreach($ware as $master_id => $value){
					$master_ids[$master_id] = $master_id;
					foreach($value as $warehouse_id => $value2){
						$warehouse_ids[$warehouse_id] = $warehouse_id;
						foreach($value2 as $size_id => $quantity){
							$size_ids[$size_id] = $size_id;
							
							
							
							$products[$row['product_id']]['product'] = $row;
							$products[$row['product_id']]['warehouse'][$warehouse_id][$master_id][$size_id] = $quantity;
							
							
						}
					}
				}
			}
			
		}
		
		
			?>
		
		<?php $row_1 = $row_2 = ''; ?>
		<style>
			.warehouse_table th{
				vertical-align: middle;
			}
			.warehouse_table{
				font-size: 12px;
			}
			.warehouse_table th{
				background-color: #7DC6ED;
			}
			.filter_warehouse{
				float: left;
				margin: 5px 10px 10px 20px;
			}
		</style>
	
	
		<?php if(count($master_ids)){ ?>
			<form method="GET">
				<input type="hidden" name="route" value="<?php echo $_GET['route']; ?>">
				<input type="hidden" name="filter" value="true">
			<table class="warehouse_table" style="margin-bottom: 20px;">
				<tr>
					<th colspan="3">Фильтр
						<input type="text" name="code" value="<?php echo (isset($_GET['code'])) ? $_GET['code'] : '' ;?>"
								placeholder="Тут поиск по штрих-коду" style="width: 400px;">
						<input type="checkbox" name="is_code" value="1"
							<?php echo (isset($_GET['is_code']) AND $_GET['is_code'] == 1) ? ' checked ' : '' ;?>>
							Четко по коду!
					</th>
				</tr>
				<tr>
					<th>Хозяин</th>
					<th>Склады</th>
					<th></th>
				</tr>	
				<tr>
					<td>
						<?php foreach($master_ids as $id => $val){ ?>
							<?php $checked = ''; ?>
							<?php if((isset($_GET['filter']) AND isset($_GET['master_id'][$id])) OR !isset($_GET['filter'])) $checked = 'checked'?>
							<input type="checkbox" name="master_id[<?php echo $id; ?>]" value="1" <?php echo $checked;?> > <?php echo $masters[$id]['name'];?><br>
						<?php } ?>
					</td>
					<td>
						<?php foreach($shops as $shop_id => $shop){
							$show = 0;
							$row_1_tmp = $shop['name'];
							$row_2_tmp = '';
							
							foreach($warehouses_shop[$shop_id] as $index => $value){
									
									if(in_array($index, $warehouse_ids)){
										$warehouse_ids_colum[$index] = $index;
										
										$checked = '';
										if((isset($_GET['filter']) AND isset($_GET['warehouse_id'][$index])) OR !isset($_GET['filter'])) $checked = 'checked';
							
										$row_2_tmp .= '<input type="checkbox" name="warehouse_id['.$index.']" value="1" '.$checked.'>'.$value['name'].'<br>';
										$show++;
									}
									
							}
							
							if($show){
								$row_1 .= '<div class="filter_warehouse"><b>'.$row_1_tmp.'</b><br>';
								$row_1 .= $row_2_tmp.'</div>';
							}
							
						}?>
						<?php echo $row_1; ?>
					</td>
					<td style="text-align: center;">
						<input type="submit" name="filter_start" value="Фильтровать" style="padding: 20px;">
					</td>
				</tr>
			</form>
		
		<?php } ?>
		
		<?php $row_1 = ''; ?>
			
		<div id="header-t"></div>
		<table class="warehouse_table" id="table">
			<thead id="thead">
			<tr>
				<th rowspan="1">Ид</th>
				<th rowspan="1">Индекс</th>
				<th rowspan="1">Индекс магазинов</th>
				<th rowspan="1">ШтрихКод</th>
				<th rowspan="1">* * *</th>
		
			<?php foreach($shops as $shop_id => $shop){
					$show = 0;
					$row_1_tmp = $shop['name'];
					$row_2_tmp = '';
					
					foreach($warehouses_shop[$shop_id] as $index => $value){
							
							if(in_array($index, $warehouse_ids)){
								
								if((isset($_GET['filter']) AND isset($_GET['warehouse_id'][$index])) OR !isset($_GET['filter'])){
								
									$warehouse_ids_colum[$index] = $index;
									$row_2_tmp .= '<th>'.$value['name'].'
										<a href="/backend/report_product/print.warehouse.php?warehouse_id='.$index.''.(isset($_GET['code']) ? '&code='.$_GET['code'] : '').''.(isset($_GET['is_code']) ? '&is_code='.$_GET['is_code'] : '').'" id="print_operation" class="print_operation" target="_blank">
											<img class="print_icon_w" src="/backend/img/Print_128.png" title="Печать документа" alt="print">
										</a>
									</th>';
									$show++;
								}else{
									unset($warehouse_ids_colum[$index]);
								}
							}
							
					}
					
					if($show){
						$row_1 .= '<th colspan="'.($show).'">'.$row_1_tmp.'</th>';
						$row_2 .= $row_2_tmp;
					}
					
				}
				
				//echo $row_1.'</tr> <tr>'.$row_2;
					
				echo $row_2;
				?>
				
				
			</tr>
			</thead>
			<tbody>
			<?php foreach($products as $product_id => $row){ ?>
				
				<?php $table_row = 0; ob_start();?>
				
				
				<tr>
					<td><?php echo $product_id; ?></td>
					<td><?php echo $row['product']['model']; ?></td>
					<td><?php echo $row['product']['model7']; ?>/<?php echo $row['product']['model8']; ?>/<?php echo $row['product']['model4']; ?></td>
					<td><?php echo $row['product']['code']; ?></td>
					<td><img class="product_image" src="<?php echo '/image/'.$row['image'];?>"></td>	
					<?php foreach($warehouse_ids_colum as $warehouse_id => $tmp){
						echo '<td>';
						
						if(isset($row['warehouse'][$warehouse_id])){
							
							//sort($row['warehouse'][$warehouse_id]);
							
							foreach($row['warehouse'][$warehouse_id] as $master_id => $sizes_a){
								
								if($master_id < 1) $master_id = 1;
								
								if((isset($_GET['filter']) AND isset($_GET['master_id'][$master_id])) OR !isset($_GET['filter'])){
									echo '<b>'.$masters[$master_id]['name'].'</b><br>';
									
									foreach($sizes_a as $size_id => $quantity){
										$table_row = 1;
										echo '['.$sizes[$size_id]['name'].'] : <b>'.$quantity.'</b> ';
									}
									echo '<br>';
								}
							}
						}
						
						echo '</td>';
					} ?>
				</tr>
				
				<?php
					$content = ob_get_contents();
					ob_end_clean();
					
					if($table_row) echo $content;
				?>
			
			<?php } ?>
			
			</tbody>
		</table>