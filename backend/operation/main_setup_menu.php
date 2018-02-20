<?php
	
	if(!isset($_SESSION['master_id'])) $_SESSION['master_id'] = 1;
	if(!isset($_SESSION['currency_id'])) $_SESSION['currency_id'] = 1;
	if(!isset($_SESSION['customer_id'])) $_SESSION['customer_id'] = 4;
	
	//echo '<pre>'; print_r(var_dump( $_SESSION  ));
	
?>
<div class="global_setup">
	<div>Общие параметры операции:</div><br>
	
	<div>
	<b>Хозяин : </b>
		<?php if(!isset($_SESSION['master_id'])) $_SESSION['master_id'] = 2; ?>
		<select class="global_setup" id="global_master_id" style="width:100px;">
			<?php foreach($masters as $index => $value){?>
				<?php if($index == $_SESSION['master_id']){ ?>
					<option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
				<?php }else{ ?>
					<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
				<?php } ?>
			<?php } ?>
		</select>
	</div><br>
	<?php if(strpos($_GET['route'],'prihod') !== false){ ?>
	<div>
	<b>Валюта : </b>
		<select class="global_setup" id="global_currency_id" style="width:100px;">
			<?php foreach($currencys as $index => $value){?>
				<?php if($index == $_SESSION['currency_id']){ ?>
					<option value="<?php echo $index; ?>" selected><?php echo $value['title']; ?></option>
				<?php }else{ ?>
					<option value="<?php echo $index; ?>"><?php echo $value['title']; ?></option>
				<?php } ?>
			<?php } ?>
		</select>
	</div><br>
	
	
	<div>
	<b>Поставщик : </b>
		<select class="global_setup" id="global_customer_id" style="width:100px;">
			<?php foreach($postavs as $index => $value){?>
				<?php if($index == $_SESSION['customer_id']){ ?>
					<option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
				<?php }else{ ?>
					<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
				<?php } ?>
			<?php } ?>
		</select>
	</div>
	<?php }else{ ?>
		<input type="hidden" id="global_customer_id" value="<?php echo $_SESSION['customer_id']; ?>">
		<input type="hidden" id="global_currency_id" value="0">
	<?php } ?>

	<div>
		<input type="checkbox" class="global_setup checkbox" id="global_auto"
			<?php if(isset($_SESSION['auto']) AND $_SESSION['auto']){ echo ' checked'; }?>
		><b> : Автозаполнение</b>
	</div>
	<div>
	
		<input type="checkbox" class="global_setup checkbox" id="global_coder"
			<?php if(isset($_SESSION['coder']) AND $_SESSION['coder']){ echo ' checked'; }?>
			<?php if(!isset($_SESSION['coder'])){ echo ' checked'; }?>
		><b> : Сканер бик-бик</b>
	</div>
	<div>
	
		<input type="checkbox" class="global_setup checkbox" id="global_is_code"
			<?php if(isset($_SESSION['is_code']) AND $_SESSION['is_code']){ echo ' checked'; }?>
			<?php if(!isset($_SESSION['is_code'])){ echo ' checked'; }?>
		><b> : Сканер Четко по коду!</b>
	</div>

	<div>
	
		<input type="checkbox" class="global_setup checkbox" id="global_is_warehouse"
			<?php if(isset($_SESSION['is_warehouse']) AND $_SESSION['is_warehouse']){ echo ' checked'; }?>
			<?php if(!isset($_SESSION['is_warehouse'])){ echo ' checked'; }?>
		><b> : Только маг.остатки</b>
	</div>

</div>

<style>
	div.global_setup{
		float: right;
		font-size: 12px;
		/*margin-right: 10px;
		margin-top: -25px;*/
		z-index: 99999;
		background-color: white;
		border: 2px solid gray;
		padding: 10px;
		position: relative;
		border-radius: 10px;
		display: none;
	}
	.global_setup select{
		margin-top: -3px;
	}
</style>

<script>
	jQuery(document).on('change','.global_setup', function(){
		
		var name = $(this).attr('id');
		console.log(name);
		
		name = name.replace('global_', '');
		
		var post = 'key=set_session';
		post = post + '&index='+name;
		
		var value = $(this).val();
	
		if($(this).hasClass('checkbox')){
			value = 0;
			if($(this).prop('checked')){
				value = 1;
			}
		}
		
		post = post + '&value='+value;
		
		jQuery.ajax({
				type: "POST",
				url: "/backend/ajax/ajax_session.php",
				dataType: "text",
				data: post,
				beforeSend: function(){
				},
				success: function(msg){
					console.log(msg);
					//console.log(post);
				}
		});
	
	});
</script>
