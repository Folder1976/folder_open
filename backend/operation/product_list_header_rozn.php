    <tr>
        <th colspan="2">id</th>
        <th>Хозяин</th>
        <th>Индекс</th>
		<!--th>Индекс Леся</th>
		<th>Индекс Леся+</th>
		<th>Индекс Твист</th-->
			
		<th>ШтрихКод</th>
		<th>Фото</th>
        <th style="max-width: 50%;" colspan="2">К-во</th>
        <th colspan="2">Закуп</th>
		<th>Сумма</th>
		<th>Розница</th>
        <th>&nbsp;</th>
    </tr>

    <tr style="background-color: <?php echo $types[$type_id]['color']; ?>;" colspan="2">
        <td class="mixed" colspan="3">
			<select name="product_list_order" id="product_list_order">
				<option value="">Без сортировки</option>
				<option value="codeAZ" <?php if(isset($_GET['order']) AND $_GET['order'] == 'codeAZ') echo ' selected';?>>По штрихкоду А-Я</option>
				<option value="codeZA" <?php if(isset($_GET['order']) AND $_GET['order'] == 'codeZA') echo ' selected';?>>По штрихкоду Я-А</option>

				<option value="operationAZ" <?php if(isset($_GET['order']) AND $_GET['order'] == 'operationAZ') echo ' selected';?>>Первый в накладную</option>
				<option value="operationZA" <?php if(isset($_GET['order']) AND $_GET['order'] == 'operationZA') echo ' selected';?>>Последний в накладную</option>

				<option value="baseAZ" <?php if(isset($_GET['order']) AND $_GET['order'] == 'baseAZ') echo ' selected';?>>Первый в базу</option>
				<option value="baseZA" <?php if(isset($_GET['order']) AND $_GET['order'] == 'baseZA') echo ' selected';?>>Последний в базу</option>
				</select>
		</td>
        <td class="mixed"><input type="text" id="find_model" style="width:150px;" value="" placeholder="Индекс"></td>
        <td class="mixed"><input type="text" id="find_code" style="width:150px;" value="" placeholder="Код"></td>
        <td class="mixed" colspan="8">
			<b>Расширенный фильтр : </b>
			
			<input type="hidden" id="category_id_find" style="width:10px;" value="">
			<a href="javascript:;" class="category_tree select_category" data-id="category_id_find">Категория [дерево]</a> (<span class="selected_category" id="name_category_id_find">Все...</span>)
			<input type="hidden" name="category"  id="category_id_find" class="selected_category_id" value="0">
				
			
			<!--input type="text" id="find_manufacturer_id" style="width:50px;" value=""-->
			<select class="header_edit" id="find_manufacturer_id" style="width:100px;">
				<option value="">Фирма</option>
				<?php foreach($brand_list as $index => $value){?>
					<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
				<?php } ?>
			</select>

			<!--input type="text" id="find_shop_id" style="width:50px;" value=""-->
			<select class="header_edit" id="find_shop_id" style="width:100px;">
				<option value="">Магазин</option>
				<?php foreach($shops as $index => $value){?>
					<?php if(isset($_SESSION['find_shop_id']) AND $index == (int)$_SESSION['find_shop_id']){ ?>
						<option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
					<?php }else{ ?>
						<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
					<?php } ?>
				<?php } ?>
			</select>

			
			<!--input type="text" id="find_warehouse_id" style="width:50px;" value=""-->
			<select class="header_edit" id="find_warehouse_id" style="width:100px;">
				<option value="">Склад</option>
				<?php foreach($shops as $shop_id => $shop){?>
					<optgroup label="маг. <?php echo $shop['name']; ?>">
						<?php foreach($warehouses[$shop_id] as $index => $value){?>
							<?php if(isset($_SESSION['find_warehouse_id']) AND $index == (int)$_SESSION['find_warehouse_id']){ ?>
								<option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
							<?php }else{ ?>
								<option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
							<?php } ?>
						<?php } ?>
					</optgroup>
				<?php } ?>
			</select>
		
			
		</td>
	</tr>
    <tr>
        <td colspan="13">&nbsp;</td>
    </tr>