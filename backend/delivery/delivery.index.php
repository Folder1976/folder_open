<!-- Sergey Kotlyarov 2016 folder.list@gmail.com -->
<?php
$file = explode('/', __FILE__);
if(strpos($_SERVER['PHP_SELF'], $file[count($file)-1]) !== false){
	header("Content-Type: text/html; charset=UTF-8");
	die('Прямой запуск запрещен!');
}

//====================================================================================================================				
//====================================================================================================================				
//====================================================================================================================				
//====================================================================================================================				
//====================================================================================================================


if(isset( $_FILES['import_file']['tmp_name'])){
	
	$tmpFilename = $_FILES['import_file']['tmp_name'];
	require_once ('libs/docs/PHPExcel/IOFactory.php');
	
	$worksheet = PHPExcel_IOFactory::load($tmpFilename)->getSheet(0);
	
	if(!$worksheet) {die('<h2>Ошибка: лист c данными не найден</h2>');}
	
	$rows = $worksheet->getHighestRow();

	$count =2;
	while('' != $worksheet->getCellByColumnAndRow(1,$count)->getValue()){
		
		//Прочитаесм строчку
		$x = 0;
		$row = array();
		while('' != $worksheet->getCellByColumnAndRow($x,1)->getValue()){
			$row[$worksheet->getCellByColumnAndRow($x,1)->getValue()] = $worksheet->getCellByColumnAndRow($x,$count)->getCalculatedValue();
			$x++;
		}
		$count++;
		
		if(!isset($row[$main_key])){
			$sql = 'INSERT INTO 	'.DB_PREFIX.'citys SET
						`Domain` = "'.htmlentities($row['Domain'],ENT_QUOTES, 'UTF-8').'",
						`is_domain` = "'.(int)$row['IsDomain'].'",
						`CityLable` = "'.htmlentities($row['CityLable'],ENT_QUOTES, 'UTF-8').'",
						`CityLableKuda` = "'.htmlentities($row['CityLableKuda'],ENT_QUOTES, 'UTF-8').'",
						`CityLablePoChemu` = "'.htmlentities($row['CityLablePoChemu'],ENT_QUOTES, 'UTF-8').'",
						`CityLableChego` = "'.htmlentities($row['CityLableChego'],ENT_QUOTES, 'UTF-8').'",
						`Region` = "'.htmlentities($row['Region'],ENT_QUOTES, 'UTF-8').'",
						`poRegionu` = "'.htmlentities($row['poRegionu'],ENT_QUOTES, 'UTF-8').'",
						`ChegoRegiona` = "'.htmlentities($row['ChegoRegiona'],ENT_QUOTES, 'UTF-8').'",
						`People` = "'.htmlentities($row['People'],ENT_QUOTES, 'UTF-8').'",
						`LitlleCity` = "'.htmlentities($row['LitlleCity'],ENT_QUOTES, 'UTF-8').'",
						`KodGoroda` = "'.htmlentities($row['KodGoroda'],ENT_QUOTES, 'UTF-8').'",
						`Population` = "'.htmlentities($row['Population'],ENT_QUOTES, 'UTF-8').'"
		
						
						
						;';
			//echo '<br>'.$sql;
		}else{
			$sql = 'UPDATE 	'.DB_PREFIX.'citys SET
						`Domain` = "'.htmlentities($row['Domain'],ENT_QUOTES, 'UTF-8').'",
						`is_domain` = "'.(int)$row['IsDomain'].'",
						`CityLable` = "'.htmlentities($row['CityLable'],ENT_QUOTES, 'UTF-8').'",
						`CityLableKuda` = "'.htmlentities($row['CityLableKuda'],ENT_QUOTES, 'UTF-8').'",
						`CityLablePoChemu` = "'.htmlentities($row['CityLablePoChemu'],ENT_QUOTES, 'UTF-8').'",
						`CityLableChego` = "'.htmlentities($row['CityLableChego'],ENT_QUOTES, 'UTF-8').'",
						`Region` = "'.htmlentities($row['Region'],ENT_QUOTES, 'UTF-8').'",
						`poRegionu` = "'.htmlentities($row['poRegionu'],ENT_QUOTES, 'UTF-8').'",
						`ChegoRegiona` = "'.htmlentities($row['ChegoRegiona'],ENT_QUOTES, 'UTF-8').'",
						`People` = "'.htmlentities($row['People'],ENT_QUOTES, 'UTF-8').'",
						`LitlleCity` = "'.htmlentities($row['LitlleCity'],ENT_QUOTES, 'UTF-8').'",
						`KodGoroda` = "'.htmlentities($row['KodGoroda'],ENT_QUOTES, 'UTF-8').'",
						`Population` = "'.htmlentities($row['Population'],ENT_QUOTES, 'UTF-8').'"
		
						WHERE `CityID` = '.(int)$row[$main_key].';';
			//echo '<br>'.$sql;
		}
		$mysqli->query($sql) or die('<br>Ошибка импорта : '.$sql);
		
	}

}
//====================================================================================================================				
//====================================================================================================================				
//====================================================================================================================				
//====================================================================================================================				
//====================================================================================================================
//города
$sql = 'SELECT * FROM '.DB_PREFIX.'citys ORDER BY CityLable;';
$r = $mysqli->query($sql);
$citys = array();
while($row = $r->fetch_assoc()){
	$citys[$row['CityID']] = $row;
}

//курьер
$sql = 'SELECT * FROM '.DB_PREFIX.'delivery ORDER BY name;';
$r = $mysqli->query($sql);
$delivery = array();
while($row = $r->fetch_assoc()){
	$delivery[$row['delivery_id']] = $row;
}
//валюта
$sql = 'SELECT * FROM '.DB_PREFIX.'currency ORDER BY title;';
$r = $mysqli->query($sql);
$currency = array();
while($row = $r->fetch_assoc()){
	$currency[$row['currency_id']] = $row;
}

//страны
$sql = 'SELECT * FROM '.DB_PREFIX.'country C
			LEFT JOIN '.DB_PREFIX.'country_description CD ON (CD.country_id = C.country_id)
			WHERE CD.language_id = 1
			ORDER BY name ;';
$r = $mysqli->query($sql) or die($sql);
$country = array();
while($row = $r->fetch_assoc()){
	$country[$row['country_id']] = $row;
}

//Тексты
$sql = 'SELECT * FROM '.DB_PREFIX.'delivery_to_country_description 
			WHERE language_id = 1
			ORDER BY name ;';
$r = $mysqli->query($sql) or die($sql);
$delivery_text = array();
while($row = $r->fetch_assoc()){
	$delivery_text[$row['delivery_to_country_id']] = $row;
}


$table = 'delivery_to_country';
$main_key = 'delivery_to_country_id';

$sql = 'SELECT CC.* FROM '.DB_PREFIX.$table.' CC
			LEFT JOIN '.DB_PREFIX.'citys C ON CC.CityID = C.CityID
			ORDER BY CC.country_id, C.CityLable;';
$r = $mysqli->query($sql);

$arr = array();
while($row = $r->fetch_assoc()){
	$arr[$row[$main_key]] = $row;
}



?>
<br>
<!--script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/js/backend/ajax_edit_attributes.js"></script-->
<h1>Справочник : Доставка</h1>
<div style="width: 90%">
<div class="table_body">

<!-- ============================================================ -->
	<div class="top_header">
		<h1 style="margin-bottom: 10px;" class="header">Импорт / экспорт информации по доставке</h1>
	</div>

    <div style="max-width: 1375px;">
    <div class="table_body">
        
        <!--div class="navigation">
            <form name="import_exel_carfit" method="post" enctype="multipart/form-data">
            
			        <div class="top_select">
						ИМПОРТ
                       <div class="select_top get_file_wrapper"  style="margin-top: 10px;">
                            <label class="select_lable">Фаил</label>
                            <input type="file" name="import_file"> 
                                <div class="ajax-respond"></div>
                        </div>
					    <br>
						<div class="select_top shop">
                            <label class="select_lable">Фаил</label>
                            <input type="submit" value="Загрузить" style="width:300px;">
                        </div>
					</div>
            </form>                
                    <div class="top_select">
                        <div class="select_top shop">
							ЭКПОРТ<br><br>
                            <button style="width:300px;height: 50px;" id="export">Сказать фаил</button>
						</div>
                    </div>
       
					
       
        </div-->
		<div style="clear: both"></div>

<script>
    $(document).on('click', '#export', function(){  
      location.href = '/<?php echo TMP_DIR; ?>backend/city/get_excel.php';
    });

</script>
<!-- ============================================================ -->



<table class="text">
    <tr>
        <th style="width:70px;" rowspan="2">id</th>
        <th rowspan="2">Активен</th>
        <th rowspan="2">Страна</th>
        <th rowspan="2">Город (для России)</th>
		<th rowspan="2">Курьер</th>
     	<th rowspan="2">Фикс. цена</th>
        <th rowspan="2">Валюта</th>
	<th colspan="11">Цена за посылку по весу в выбраной валюте.</th>
		<th rowspan="2">Бесплатная доставка от $</th>
		<th rowspan="2">Макс. вес коробки кг</th>
		<th rowspan="2">Мин дней</th>
		<th rowspan="2">Макс дней</th>
		<th rowspan="2">Текст для сайта (Тут редактор текстов)</th>
		<th rowspan="2"></th>
    </tr>
	<tr>
		<th>0,5 кг</th>
		<th>1,0 кг</th>
		<th>1,5 кг</th>
		<th>2,0 кг</th>
		<th>2,5 кг</th>
		<th>3,0 кг</th>
		<th>3,5 кг</th>
		<th>4,0 кг</th>
		<th>4,5 кг</th>
		<th>5,0 кг</th>
		<th>+ 1 кг</th>
		
	</tr>

    <tr>
        <td class="mixed">новый</td>
		<td class="mixed"><input type="checkbox" class="edit" id="status" checked></td>
        <td class="mixed">
			<select id="country_id" style="width:150px;">
				<option value="0">Выбрать страну</option>
				<?php foreach($country as $index => $row){ ?>
				<option value="<?php echo $index;?>"><?php echo $row['name'];?></option>
				<?php } ?>
			</select>
		</td>
		<td class="mixed">
			<select id="CityID" style="width:150px;">
				<option value="0">Выбрать город</option>
				<?php foreach($citys as $index => $row){ ?>
				<option value="<?php echo $index;?>"><?php echo $row['CityLable'];?></option>
				<?php } ?>
			</select>
		</td>
		<td class="mixed">
			<select id="delivery_id" style="width:150px;">
				<option value="0">Выбрать курьера</option>
				<?php foreach($delivery as $index => $row){ ?>
				<option value="<?php echo $index;?>"><?php echo $row['name'];?></option>
				<?php } ?>
			</select>
		</td>
        <td class="mixed"><input type="text"        id="price" style="width:70px;" value="0.00"></td>
		<td class="mixed">
			<select id="currency_id" style="width:70px;">
				<option value="0">Валюта</option>
				<?php foreach($currency as $index => $row){ ?>
				<option value="<?php echo $index;?>"><?php echo $row['title'];?></option>
				<?php } ?>
			</select>
		</td>
        <td class="mixed"><input type="text"        id="price_05" style="width:40px;" value="0.0"></td>
		<td class="mixed"><input type="text"        id="price_10" style="width:40px;" value="0.0"></td>
		<td class="mixed"><input type="text"        id="price_15" style="width:40px;" value="0.0"></td>
		<td class="mixed"><input type="text"        id="price_20" style="width:40px;" value="0.0"></td>
		<td class="mixed"><input type="text"        id="price_25" style="width:40px;" value="0.0"></td>
		<td class="mixed"><input type="text"        id="price_30" style="width:40px;" value="0.0"></td>
		<td class="mixed"><input type="text"        id="price_35" style="width:40px;" value="0.0"></td>
		<td class="mixed"><input type="text"        id="price_40" style="width:40px;" value="0.0"></td>
		<td class="mixed"><input type="text"        id="price_45" style="width:40px;" value="0.0"></td>
		<td class="mixed"><input type="text"        id="price_50" style="width:40px;" value="0.0"></td>
		<td class="mixed"><input type="text"        id="price_p1" style="width:40px;" value="0.0"></td>
		<td class="mixed"><input type="text"        id="order_sum" style="width:50px;" value="0.0"></td>
		<td class="mixed"><input type="text"        id="max_kg" style="width:70px;" value="0"></td>
		<td class="mixed"><input type="text"        id="min_days" style="width:70px;" value="0"></td>
		<td class="mixed"><input type="text"        id="max_days" style="width:70px;" value="0"></td>
		<!--td class="mixed"><input type="text"        id="text" style="width:150px;" value="0"></td-->
		<td class="mixed">
			<select id="text" style="width:150px;">
				<option value="0">Выбрать текст для доставик</option>
				<?php foreach($delivery_text as $index => $row){ ?>
				<option value="<?php echo $index;?>"><?php echo $row['name'];?></option>
				<?php } ?>
			</select>
		</td>
    

        <td>        
            <a href="javascript:" class="add">
                <img src="/<?php echo TMP_DIR; ?>backend/img/add.png" title="Добавить" width="16" height="16">
            </a>
        </td>              
    </tr>
    <td>
        <td colspan="6">&nbsp;</td>
    </td>

<?php foreach($arr as $index => $ex){ ?>
    <tr id="<?php echo $ex[$main_key];?>">
        <td class="mixed"><?php echo $ex[$main_key];?> <a href="javascript:;" class="copy">copy</a></td>
		<td class="mixed"><input type="checkbox" class="edit" id="status<?php echo $ex[$main_key];?>" <?php if($ex['status'] == 1) echo ' checked '; ?>></td>
        <td class="mixed">
			<select class="edit" id="country_id<?php echo $ex[$main_key];?>" style="width:150px;">
				<?php foreach($country as $index => $row){ ?>
					<?php if($ex['country_id'] == $index){ ?>
					<option value="<?php echo $index;?>" selected><?php echo $row['name'];?></option>
					<?php }else{ ?>
					<option value="<?php echo $index;?>"><?php echo $row['name'];?></option>
					<?php } ?>
				<?php } ?>
			</select>
		</td>
        <td class="mixed">
			<select class="edit" id="CityID<?php echo $ex[$main_key];?>" style="width:150px;">
				<option value="0">Выбрать город</option>
				<?php foreach($citys as $index => $row){ ?>
					<?php if($ex['CityID'] == $index){ ?>
					<option value="<?php echo $index;?>" selected><?php echo $row['CityLable'];?></option>
					<?php }else{ ?>
					<option value="<?php echo $index;?>"><?php echo $row['CityLable'];?></option>
					<?php } ?>
				<?php } ?>
			</select>
		</td>
        <td class="mixed">
			<select class="edit" id="delivery_id<?php echo $ex[$main_key];?>" style="width:150px;">
				<option value="0">Выбрать курьера</option>
				<?php foreach($delivery as $index => $row){ ?>
					<?php if($ex['delivery_id'] == $index){ ?>
					<option value="<?php echo $index;?>" selected><?php echo $row['name'];?></option>
					<?php }else{ ?>
					<option value="<?php echo $index;?>"><?php echo $row['name'];?></option>
					<?php } ?>
				<?php } ?>
			</select>
		</td>
        <td class="mixed"><input type="text" class="edit" id="price<?php echo $ex[$main_key];?>" style="width:70px;" value="<?php echo number_format($ex['price'],2,'.',''); ?>"></td>
        <td class="mixed">
			<select class="edit" id="currency_id<?php echo $ex[$main_key];?>" style="width:70px;">
				<option value="0">Валюта</option>
				<?php foreach($currency as $index => $row){ ?>
					<?php if($ex['currency_id'] == $index){ ?>
					<option value="<?php echo $index;?>" selected><?php echo $row['title'];?></option>
					<?php }else{ ?>
					<option value="<?php echo $index;?>"><?php echo $row['title'];?></option>
					<?php } ?>
				<?php } ?>
			</select>
		</td>
		<td class="mixed"><input type="text" class="edit" id="price_05<?php echo $ex[$main_key];?>" style="width:40px;" value="<?php echo number_format($ex['price_05'],1,'.',''); ?>"></td>
        <td class="mixed"><input type="text" class="edit" id="price_10<?php echo $ex[$main_key];?>" style="width:40px;" value="<?php echo number_format($ex['price_10'],1,'.',''); ?>"></td>
     	<td class="mixed"><input type="text" class="edit" id="price_15<?php echo $ex[$main_key];?>" style="width:40px;" value="<?php echo number_format($ex['price_15'],1,'.',''); ?>"></td>
        <td class="mixed"><input type="text" class="edit" id="price_20<?php echo $ex[$main_key];?>" style="width:40px;" value="<?php echo number_format($ex['price_20'],1,'.',''); ?>"></td>
     	<td class="mixed"><input type="text" class="edit" id="price_25<?php echo $ex[$main_key];?>" style="width:40px;" value="<?php echo number_format($ex['price_25'],1,'.',''); ?>"></td>
        <td class="mixed"><input type="text" class="edit" id="price_30<?php echo $ex[$main_key];?>" style="width:40px;" value="<?php echo number_format($ex['price_30'],1,'.',''); ?>"></td>
     	<td class="mixed"><input type="text" class="edit" id="price_35<?php echo $ex[$main_key];?>" style="width:40px;" value="<?php echo number_format($ex['price_35'],1,'.',''); ?>"></td>
        <td class="mixed"><input type="text" class="edit" id="price_40<?php echo $ex[$main_key];?>" style="width:40px;" value="<?php echo number_format($ex['price_40'],1,'.',''); ?>"></td>
     	<td class="mixed"><input type="text" class="edit" id="price_45<?php echo $ex[$main_key];?>" style="width:40px;" value="<?php echo number_format($ex['price_45'],1,'.',''); ?>"></td>
        <td class="mixed"><input type="text" class="edit" id="price_50<?php echo $ex[$main_key];?>" style="width:40px;" value="<?php echo number_format($ex['price_50'],1,'.',''); ?>"></td>
     	<td class="mixed"><input type="text" class="edit" id="price_p1<?php echo $ex[$main_key];?>" style="width:40px;" value="<?php echo number_format($ex['price_p1'],1,'.',''); ?>"></td>
        <td class="mixed"><input type="text" class="edit" id="order_sum<?php echo $ex[$main_key];?>" style="width:50px;" value="<?php echo number_format($ex['order_sum'],1,'.',''); ?>"></td>
        <td class="mixed"><input type="text" class="edit" id="max_kg<?php echo $ex[$main_key];?>" style="width:70px;" value="<?php echo number_format($ex['max_kg'],0,'.',''); ?>"></td>
        <td class="mixed"><input type="text" class="edit" id="min_days<?php echo $ex[$main_key];?>" style="width:70px;" value="<?php echo number_format($ex['min_days'],0,'.',''); ?>"></td>
        <td class="mixed"><input type="text" class="edit" id="max_days<?php echo $ex[$main_key];?>" style="width:70px;" value="<?php echo number_format($ex['max_days'],0,'.',''); ?>"></td>
        <!--td class="mixed"><input type="text" class="edit" id="text<?php echo $ex[$main_key];?>" style="width:300px;" value="<?php echo $ex['text']; ?>"></td-->
        <td class="mixed">
			<select class="edit" id="text<?php echo $ex[$main_key];?>" style="width:150px;">
				<option value="0">Выбрать текст для доставик</option>
				<?php foreach($delivery_text as $index => $row){ ?>
					<?php if($ex['text'] == $index){ ?>
					<option value="<?php echo $index;?>" selected><?php echo $row['name'];?></option>
					<?php }else{ ?>
					<option value="<?php echo $index;?>"><?php echo $row['name'];?></option>
					<?php } ?>
				<?php } ?>
			</select>
		</td>
	
	    <td>        
            <a href="javascript:;" class="dell" data-id="<?php echo $ex[$main_key];?>">
                <img src="/<?php echo TMP_DIR; ?>backend/img/cancel.png" title="удалить" width="16" height="16">
            </a>
        </td>              
    </tr>
<?php } ?>

</table>
<input type="hidden" id="table" value="<?php echo $table; ?>">
<script>

    
</script>



</div>

</div>

<style>
	.top_select{
		float: left;
		padding: 10px;
		border: solid 1px #aacfe4;
		margin: 10px;
        height: 100px;
	}
	.table_body{
		margin: 10px;
	}
    .top_header{
        margin: 10px;
    }
	
	body{
		overflow: auto;
	}
</style>

<script>
	 //======================================================================   
    
    $(document).on('change','.edit', function(){
		var id = jQuery(this).parent('td').parent('tr').attr('id');
	//debugger;
		var status = 0;
		var delivery_id = $('#delivery_id'+id).val();
		var country_id = $('#country_id'+id).val();
		var currency_id = $('#currency_id'+id).val();
		var CityID = $('#CityID'+id).val();
		var price = $('#price'+id).val();
		var price_05 = $('#price_05'+id).val();
		var price_10 = $('#price_10'+id).val();
		var price_15 = $('#price_15'+id).val();
		var price_20 = $('#price_20'+id).val();
		var price_25 = $('#price_25'+id).val();
		var price_30 = $('#price_30'+id).val();
		var price_35 = $('#price_35'+id).val();
		var price_40 = $('#price_40'+id).val();
		var price_45 = $('#price_45'+id).val();
		var price_50 = $('#price_50'+id).val();
		var price_p1 = $('#price_p1'+id).val();
		var order_sum = $('#order_sum'+id).val();
		var max_kg = $('#max_kg'+id).val();
		var max_days = $('#max_days'+id).val();
		var min_days = $('#min_days'+id).val();
		var text = $('#text'+id).val();
		
		if ($('#status'+id).prop('checked') == true) {
            status = 1;
        }
	
		//name = name.replace('&','@*@');
		//href = href.replace('&','@*@');
		
		var post = "id="+id;
		 post = post + "&status="+status;
		 post = post + "&delivery_id="+delivery_id;
		 post = post + "&country_id="+country_id;
		 post = post + "&currency_id="+currency_id;
		 post = post + "&CityID="+CityID;
		 post = post + "&price="+price;
		 post = post + "&price_05="+price_05;
		 post = post + "&price_10="+price_10;
		 post = post + "&price_15="+price_15;
		 post = post + "&price_20="+price_20;
		 post = post + "&price_25="+price_25;
		 post = post + "&price_30="+price_30;
		 post = post + "&price_35="+price_35;
		 post = post + "&price_40="+price_40;
		 post = post + "&price_45="+price_45;
		 post = post + "&price_50="+price_50;
		 post = post + "&price_p1="+price_p1;
		 post = post + "&order_sum="+order_sum;
		 post = post + "&max_kg="+max_kg;
		 post = post + "&max_days="+max_days;
		 post = post + "&min_days="+min_days;
		 post = post + "&text="+text;
		
		
		$.ajax({
			type: "POST",
			url: "/<?php echo TMP_DIR; ?>backend/ajax/ajax_edit_universal.php",
			dataType: "text",
			data: post+"&mainkey=<?php echo $main_key;?>&table=<?php echo $table; ?>&key=edit",
			beforeSend: function(){
			},
			success: function(msg){
			  console.log(  msg );
			}
		});
	});
	
	//Add
	$(document).on('click','.add', function(){
	
		var status = 0;
		var delivery_id = $('#delivery_id').val();
		var country_id = $('#country_id').val();
		var currency_id = $('#currency_id').val();
		var CityID = $('#CityID').val();
		var price = $('#price').val();
		var price_05 = $('#price_05').val();
		var price_10 = $('#price_10').val();
		var price_15 = $('#price_15').val();
		var price_20 = $('#price_20').val();
		var price_25 = $('#price_25').val();
		var price_30 = $('#price_30').val();
		var price_35 = $('#price_35').val();
		var price_40 = $('#price_40').val();
		var price_45 = $('#price_45').val();
		var price_50 = $('#price_50').val();
		var price_p1 = $('#price_p1').val();
		var order_sum = $('#order_sum').val();
		var max_kg = $('#max_kg').val();
		var max_days = $('#max_days').val();
		var min_days = $('#min_days').val();
		var text = $('#text').val();
		
		if ($('#status').prop('checked') == true) {
            status = 1;
        }
		
		//name = name.replace('&','@*@');
		//href = href.replace('&','@*@');
			
		var post = "";
		 post = post + "status="+status;
		 post = post + "&delivery_id="+delivery_id;
		 post = post + "&country_id="+country_id;
		 post = post + "&currency_id="+currency_id;
		 post = post + "&CityID="+CityID;
		 post = post + "&price="+price;
		 post = post + "&price_05="+price_05;
		 post = post + "&price_10="+price_10;
		 post = post + "&price_15="+price_15;
		 post = post + "&price_20="+price_20;
		 post = post + "&price_25="+price_25;
		 post = post + "&price_30="+price_30;
		 post = post + "&price_35="+price_35;
		 post = post + "&price_40="+price_40;
		 post = post + "&price_45="+price_45;
		 post = post + "&price_50="+price_50;
		 post = post + "&price_p1="+price_p1;
		 post = post + "&order_sum="+order_sum;
		 post = post + "&max_kg="+max_kg;
		 post = post + "&max_days="+max_days;
		 post = post + "&min_days="+min_days;
		 post = post + "&text="+text;
		
		$.ajax({
			type: "POST",
			url: "/<?php echo TMP_DIR; ?>backend/ajax/ajax_edit_universal.php",
			dataType: "text",
			data: post+"&mainkey=<?php echo $main_key;?>&table=<?php echo $table; ?>&key=add",
			beforeSend: function(){
			},
			success: function(msg){
			  //console.log(  msg );
			  location.reload();
			}
		});
		
	});
	
   	//Удаление
   $(document).on('click','.dell', function(){
       var id = jQuery(this).parent('td').parent('tr').attr('id');
      
	  if (confirm('Вы действительно желаете удалить доставку?')){
		$.ajax({
		  type: "POST",
		  url: "/<?php echo TMP_DIR; ?>backend/ajax/ajax_edit_universal.php",
		  dataType: "text",
		  data: "id="+id+"&table=<?php echo $table; ?>&mainkey=<?php echo $main_key;?>&key=dell",
		  beforeSend: function(){
			
		  },
		  success: function(msg){
			console.log(  msg );
			//location.reload;
			jQuery('#'+id).hide()
		  }
		});
	  }
    });
    //Копирование
	$(document).on('click','.copy', function(){
       var id = jQuery(this).parent('td').parent('tr').attr('id');
      
	  $.ajax({
		  type: "POST",
		  url: "/<?php echo TMP_DIR; ?>backend/ajax/ajax_edit_universal.php",
		  dataType: "text",
		  data: "id="+id+"&table=<?php echo $table; ?>&mainkey=<?php echo $main_key;?>&key=copy",
		  beforeSend: function(){
			
		  },
		  success: function(msg){
			//debugger;
			//console.log(  msg );
			window.location.reload();
		  }
		});
	  
    });
    //======================================================================
</script>
