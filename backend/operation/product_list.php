<?php
	include('print/php-barcode-generator-master/src/BarcodeGenerator.php');
	include('print/php-barcode-generator-master/src/BarcodeGeneratorPNG.php');
	$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
	$count = 1;
	
?>
<style>
	.selectable_row:hover{
		background-color: #8E8E8E;
	}
	.selected_row{
		background-color: #8E8E8E;
	}
</style>
<script>
	$(document).keyup(function(e){
		console.log(e.keyCode);
		
		var row_id = $('.selected_row').data('row_id');
		
		if(e.keyCode == 38){ //Вверх
			row_id = row_id - 1;
		}else if(e.keyCode == 40){ //Вниз
			row_id = row_id + 1;
		}else if(e.keyCode == 39){ //Вправо
			$('[data-row_id='+row_id+']').trigger('click');
			return false;
		}
		
		if(row_id < 1) row_id = 1;
		
		$.each($('.selected_row'), function( index, value ) {
			$(this).removeClass('selected_row');
		});
		
		$('[data-row_id='+row_id+']').addClass('selected_row');
		
	});
	
	$(document).on('click','.selectable_row', function(){
		$.each($('.selected_row'), function( index, value ) {
			$(this).removeClass('selected_row');
		});
		
		$(this).addClass('selected_row');	
	});
	
</script>
<?php if(isset($operation_products_grp) AND count($operation_products_grp)>0){ ?>
<?php foreach($operation_products_grp as $index => $ex){ ?>
    <tr id="<?php echo $index;?>" style="height: 65px;" class="selectable_row" data-row_id="<?php echo $count++;?>">
        <td class="mixed"><?php echo $index; ?></td>
		<td><a href="javascript:;" data-barcode="<?php echo $ex['product']['code']; ?>" class="barcode-print">
			<img title="Нажми чтоб напечатать." src="data:image/png;base64,<?php echo base64_encode($generator->getBarcode($ex['product']['code'], $generator::TYPE_EAN_2));?>">
			</a>
		</td>

		<td><?php //echo $Operation->getProductMaster($ex['product']['operation_id'], $index); ?>
			<select class="edit_master" id="master_id<?php echo $index;?>" style="width:70px;">
			<?php foreach($masters as $index2 => $value){?>
				<?php if($index2 == $ex['product']['master_id']){ ?>
					<option value="<?php echo $index2; ?>" selected><?php echo $value['name']; ?></option>
				<?php }else{ ?>
					<option value="<?php echo $index2; ?>"><?php echo $value['name']; ?></option>
				<?php } ?>
			<?php } ?>
		</select>
		
		<!-- Выбор поставщика только для Прихода -->
		<?php if($type_id == 1){ ?>
			</td>
			<td><?php //echo $Operation->getProductMaster($ex['product']['operation_id'], $index); ?>
				<select class="edit_products" id="customer_id<?php echo $index;?>" style="width:70px;">
				<?php foreach($postavs as $index2 => $value){?>
					<?php if($index2 == $ex['product']['customer_id']){ ?>
						<option value="<?php echo $index2; ?>" selected><?php echo $value['name']; ?></option>
					<?php }else{ ?>
						<option value="<?php echo $index2; ?>"><?php echo $value['name']; ?></option>
					<?php } ?>
				<?php } ?>
			</select>
			</td>
		<?php }else{ ?>
			<input type="hidden" value="4" class="edit_products" id="customer_id<?php echo $index;?>">
			</td>
		<?php } ?>
        <td class="left"><!--input type="text" class="edit" id="model<?php echo $index;?>" style="width:150px;" value="<?php echo $ex['product']['model']; ?>"-->
			
			<a href="/backend/index.php?route=product/product.index.php&product_id=<?php echo  $ex['product']['product_id'];?>" target='_blank'>
                <img src="/backend/img/jleditor_ico.png" title="редактировать" width="16" height="16">
            </a>
			<?php echo $ex['product']['model']; ?> *
			<?php echo $ex['product']['model7']; ?>/<?php echo $ex['product']['model8']; ?>/<?php echo $ex['product']['model4']; ?>
			</td>
        <td class="left"><!--input type="text" class="edit" id="code<?php echo $index;?>" style="width:150px;" value="<?php echo $ex['product']['code']; ?>"-->
			<?php echo $ex['product']['code']; ?>
		</td>
        <td class="mixed" style="width: 80px;">
			<img class="product_image" src="/image/<?php echo $ex['product']['image']; ?>">
		</td>
		<td class="left size_wrapper">
				<?php $ProductsOnWare = $Warehouse->getProductsOnWareOnTable($ex['product']['product_id']); ?>
			
				<?php $size_group_id = $ex['product']['size_group_id'] ; ?>
				<?php $row_summ = 0;?>
				
				<?php if($size_group_id > 0){ ?>
					<b><?php echo $size_groups[(int)$size_group_id]['name']; ?></b><br>
						
					<?php foreach($sizes_on_groups[$size_group_id] as $size_id => $value){ ?>
				   
					<?php if(!in_array($value['size_id'], $ProductsOnWare['size_ids']) and $type_id != 1) continue; ?>
				   
						
						<?php if(isset($ex['sizes'][$value['size_id']])){
							$quantity = (int)$ex['sizes'][$value['size_id']]['operation_quantity'];
						}else{
							$quantity = 0;
						}
						?>
						
						
						
						<div class="size-box">
							<span><?php echo $value['name'];?></span>
							<input type="text"
								   required
								   class="size"
								   id="size*<?php echo $index; ?>"
								   data-row_id="<?php echo $index;?>"
								   data-size_id="<?php echo $value['size_id']; ?>"
								   data_product_id="<?php echo $ex['product']['product_id']; ?>"
								   value="<?php echo $quantity;?>">
						</div>
					
						<?php $row_summ += (int)$quantity;?>
					
					<?php } ?>
                <?php }else{ ?>
				
					<?php  $size = array_shift($ex['sizes']);
						$zakup = $size['zakup'];
					?>
					<div class="size-box">
							<span>Без размера</span>
							<input type="text"
								   required
								   class="size"
								   id="size*<?php echo $index; ?>"
								   data-row_id="<?php echo $index;?>"
								   data-size_id="0"
								   data_product_id="<?php echo $ex['product']['product_id']; ?>"
								   value="<?php echo $size['operation_quantity'];?>">
						</div>
					<?php $row_summ += (int)$size['operation_quantity'];?>
				<?php } ?>
                
                <div class="product_quantity_wrapper">
                    
                    <?php echo $ProductsOnWare['html']; ?>
                    
                </div>
                
        </td>
		<td class="total_quantity" id="total_quantity<?php echo $index;?>"><?php echo $row_summ;?></td>
		<td class="right"><input type="text" class="edit zakup right" id="zakup<?php echo $index;?>" style="width:70px;" value="<?php echo number_format($ex['product']['operation_zakup'],2,'.',''); ?>">
			<select class="edit_products currency_id" id="currency_id<?php echo $index;?>" style="width:40px;">
				<?php foreach($currencys as $index2 => $value){?>
					<?php if($index2 == $ex['product']['currency_id']){ ?>
						<option value="<?php echo $index2; ?>" selected><?php echo $value['symbol_left'] . $value['symbol_right']; ?></option>
					<?php }else{ ?>
						<option value="<?php echo $index2; ?>"><?php echo $value['symbol_left'] . $value['symbol_right']; ?></option>
					<?php } ?>
				<?php } ?>
			</select>
	
		
			<?php //echo $currencys[$ex['product']['currency_id']]['symbol_left'] . $currencys[$ex['product']['currency_id']]['symbol_right']; ?>		
		</td>
		
		<td class="right" class="zakup_grn" id="zakup_grn<?php echo $index;?>">
		<?php $zakup_grn = $ex['product']['operation_zakup'] /
							$currencys[$ex['product']['currency_id']]['value']; ?>
			<span class="number"><?php echo number_format($zakup_grn,0,'.',''); ?></span><span> грн</span></td>
        
		<td class="right" class="summ" id="summ_<?php echo $index;?>">
		<?php $summ = $ex['product']['summ'] /
							$currencys[$ex['product']['currency_id']]['value']; ?>
			<span class="number"><?php echo number_format($row_summ * $zakup_grn,0,'.',''); ?></span><span> грн</span></td>
		
		<!--td class="right" class="summ"
				id="summ_<?php echo $index;?>"
				data-currency_simbol="<?php echo $currencys[$ex['product']['currency_id']]['symbol_left'] . $currencys[$ex['product']['currency_id']]['symbol_right']; ?>	"
				><?php echo number_format(($row_summ * $ex['product']['operation_zakup']),2,'.','');?>
			<?php echo $currencys[$ex['product']['currency_id']]['symbol_left'] . $currencys[$ex['product']['currency_id']]['symbol_right']; ?>	
		</td-->
		
        <td class="right"><input type="text" class="edit edit_products price_invert right" id="price_invert<?php echo $index;?>" style="width:70px;" value="<?php echo number_format($ex['product']['price_invert'],2,'.',''); ?>"> грн</td>
        
		<td>        
            <a href="javascript:;" class="dell" data-id="<?php echo $index;?>">
                <img src="/<?php echo TMP_DIR; ?>backend/img/cancel.png" title="удалить" width="16" height="16">
            </a>
           </td>              
    </tr>
<?php } ?>
<?php } ?>