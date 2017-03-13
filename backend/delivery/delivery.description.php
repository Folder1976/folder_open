<!-- Sergey Kotlyarov 2016 folder.list@gmail.com -->
<?php
$file = explode('/', __FILE__);
if(strpos($_SERVER['PHP_SELF'], $file[count($file)-1]) !== false){
	header("Content-Type: text/html; charset=UTF-8");
	die('Прямой запуск запрещен!');
}

//====================================================================================================================				
//====================================================================================================================
if(isset($_POST['key']) AND $_POST['key'] == 'add'){
	
	$insert_id = '';
	
	$sql = 'SELECT MAX(delivery_to_country_id) as last_id FROM '.DB_PREFIX.'delivery_to_country_description;';
	$r = $mysqli->query($sql);
	$row = $r->fetch_assoc();
	$insert_id = (int)$row['last_id'] + 1;
	
	foreach($_POST['text'] as $index => $row){
		
		$sql = 'INSERT INTO '.DB_PREFIX.'delivery_to_country_description SET
				delivery_to_country_id = "'.$insert_id.'",
				language_id = "'.$index.'",
				name="'.$_POST['name'].'",
				text="'.$row.'";';
		$mysqli->query($sql);
		
	}
	?>
	<script>
		$(document).ready(function(){
			location.href = '/backend/index.php?route=delivery/delivery.description.php';	
		});
	</script>
	<?php
}
//====================================================================================================================				
//====================================================================================================================				
//====================================================================================================================


//Языки
$sql = 'SELECT * FROM '.DB_PREFIX.'language ORDER BY name;';
$r = $mysqli->query($sql);
$languages = array();
while($row = $r->fetch_assoc()){
	$languages[$row['language_id']] = $row;
}


$table = 'delivery_to_country_description';
$main_key = 'delivery_to_country_id';

$sql = 'SELECT * FROM '.DB_PREFIX.'delivery_to_country_description 
			ORDER BY delivery_to_country_id ASC, language_id ASC;';
$r = $mysqli->query($sql);

$arr = array();
$description = array();
while($row = $r->fetch_assoc()){
	$arr[$row[$main_key]] = $row;
	$description[$row[$main_key]][$row['language_id']] = $row;
}



?>
<br>
<!--script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/js/backend/ajax_edit_attributes.js"></script-->
<h1>Справочник : Тексты для курьеров</h1>
<div style="width: 90%">
<div class="table_body">

<table class="text">
    <tr>
        <th style="width:70px;">id</th>
        <th style="width:200px;">Название</th>
        <th style="width:650px;">Текст</th>
		<th></th>
    </tr>

    <tr>
		<form action="/backend/index.php?route=delivery/delivery.description.php" method="POST" id="add_form">
			<!--input type="hidden" name="route" value="delivery/delivery.description.php"-->
			<input type="hidden" name="key" value="add">
			<td class="mixed">новый</td>
			<td class="mixed"><input type="text"    name="name"    id="name" style="width:200px;" placeholder="Название шаблона" value=""></td>
			<td class="mixed">
				<?php foreach($languages as $index => $row){ ?>
					<?php echo $row['name'];?> : <input type="text"  name="text[<?php echo $index;?>]" id="text_<?php echo $index;?>" data-language_id="<?php echo $index;?>" style="width:550px;" placeholder="Текст шаблона <?php echo $row['name'];?>" value=""><br>
				<?php } ?>
			</td>
			<td>
				<a href="javascript:" class="add" onClick="$('#add_form').submit();">
					<img src="/<?php echo TMP_DIR; ?>backend/img/add.png" title="Добавить" width="16" height="16">
				</a>
			</td>
		</form>
    </tr>
    <td>
        <td colspan="4">&nbsp;</td>
    </td>

<?php foreach($arr as $index => $ex){ ?>
    <tr id="<?php echo $ex[$main_key];?>">
        <td class="mixed"><?php echo $ex[$main_key];?></td>
		<td class="mixed"><input type="text" class="edit" id="name<?php echo $ex[$main_key];?>" style="width:200px;" value="<?php echo $ex['name']; ?>"></td>
      
		<td class="mixed">
			<?php foreach($languages as $index_l => $row){ ?>
				<?php echo $row['name'];?> : <input type="text"  class="edit" id="text<?php echo $ex[$main_key];?>_<?php echo $index_l;?>" style="width:550px;" value="<?php echo $description[$index][$index_l]['text']; ?>"><br>
			<?php } ?>
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
	
		var name = $('#name'+id).val();
		var post = '';
		
		 <?php foreach($languages as $index => $row){ ?>
		 
		 	post = "id="+id;
			post = post + "&name="+name;
			post = post + "&text="+$('#text'+id+'_<?php echo $index; ?>').val();
			post = post + "&language_id=<?php echo $index; ?>";
		 
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
		
		<?php } ?>
		
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
   
    //======================================================================
</script>
