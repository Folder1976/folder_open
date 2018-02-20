<!-- Sergey Kotlyarov 2016 folder.list@gmail.com -->
<?php
$file = explode('/', __FILE__);
if(strpos($_SERVER['PHP_SELF'], $file[count($file)-1]) !== false){
	header("Content-Type: text/html; charset=UTF-8");
	die('Прямой запуск запрещен!');
}

$key = 'manufacturer_id';
$table = 'manufacturer';

include "class/brand.class.php";
$Brand = new Brand();

include "class/country.class.php";
$Country = new Country();

$List = $Brand->getBrands();
$CountryList = $Country->getCountrys();

?>
<br>
<!--script type="text/javascript" src="/<?php echo TMP_DIR;?>backend/js/backend/ajax_edit_attributes.js"></script-->
<h1>Справочник : Брендов</h1>
<div style="width: 90%">
<div class="table_body">

<table class="text">
    <tr>
        <th>id</th>
        <th>Название</th>
        <th>Страна</th>
        <th>Активный</th>
        <th>Сорт</th>
        <th>Описание</th>
        <th>&nbsp;</th>
    </tr>

    <tr>
        <td class="mixed">новый</td>
        <td class="mixed"><input type="text"        id="name" style="width:300px;" value="" placeholder="Название бренда"></td>
        <td class="mixed"><select id="country_id" style="width:300px;">
                <?php foreach($CountryList as $index => $value){?>
                    <option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
                <?php } ?>
            
                </select>
        </td>
		<td class="mixed"><input type="checkbox"    id="enable"  checked ></td>
        <td class="mixed"><input type="text"        id="sort_order" style="width:100px;" value=""></td>
        <td class="mixed"><input type="text"        id="description" style="width:400px;" value="" placeholder="Описание бренда"></td>
        <td>        
            <a href="javascript:" class="add">
                <img src="/<?php echo TMP_DIR; ?>backend/img/add.png" title="Добавить" width="16" height="16">
            </a>
        </td>              
    </tr>
    <td>
        <td colspan="6">&nbsp;</td>
    </td>

<?php foreach($List as $index => $ex){ ?>
    <tr id="<?php echo $ex[$key];?>">
        <td class="mixed"><?php echo $ex[$key];?></td>
        <td class="mixed"><input type="text" class="edit" id="name<?php echo $ex[$key];?>" style="width:300px;" value="<?php echo $ex['name']; ?>"></td>
        <td class="mixed"><select class="edit" id="country_id<?php echo $ex[$key];?>" style="width:300px;">
                <?php foreach($CountryList as $index => $value){?>
                    <?php if($index == (int)$ex['country_id']){ ?>
                        <option value="<?php echo $index; ?>" selected><?php echo $value['name']; ?></option>
                    <?php }else{ ?>
                        <option value="<?php echo $index; ?>"><?php echo $value['name']; ?></option>
                    <?php } ?>
                <?php } ?>
                </select>
        </td>
		<td class="mixed"><input type="checkbox" class="edit" id="enable<?php echo $ex[$key];?>"  <?php if($ex['enable']) echo 'checked';?>></td>
        <td class="mixed"><input type="text" class="edit" id="sort_order<?php echo $ex[$key];?>" style="width:100px;" value="<?php echo $ex['sort_order']; ?>"></td>
        <td class="mixed"><input type="text" class="edit" id="description<?php echo $ex[$key];?>" style="width:400px;" value="<?php echo $ex['description']; ?>"></td>
        <td>        
            <a href="javascript:;" class="dell" data-id="<?php echo $ex[$key];?>">
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


<script>
	 //======================================================================   
    
    jQuery(document).on('change','.edit', function(){
        var id = jQuery(this).parent('td').parent('tr').attr('id');
        var name = jQuery('#name'+id).val();
        var country_id = jQuery('#country_id'+id).val();
        var enable_tmp = 0;
        var sort = jQuery('#sort_order'+id).val();
        var description = jQuery('#description'+id).val();
        var table = jQuery('#table').val();
        
        if (jQuery('#enable'+id).prop('checked')) {
             enable_tmp = 1;
        }
        
        jQuery.ajax({
            type: "POST",
            url: "/<?php echo TMP_DIR; ?>backend/ajax/ajax_guideuniversal.php",
            dataType: "text",
            data: "id="+id+"&description="+description+"&country_id="+country_id+"&name="+name+"&enable="+enable_tmp+"&sort_order="+sort+"&key=edit_manufacturer",
            beforeSend: function(){
            },
            success: function(msg){
                console.log( msg );
            }
        });
        
    });
 
    jQuery(document).on('click','.add', function(){
		
		//console.log('11 '+name);   
        var id = 0;
        var name = jQuery('#name').val();
        var country_id = jQuery('#country_id').val();
        var enable_tmp = 0;
        var sort = jQuery('#sort_order').val();
        var description = jQuery('#description').val();
        var table = jQuery('#table').val();
        
        if (jQuery('#enable').prop('checked')) {
             enable_tmp = 1;
        }
     
        if (name != "") {
            jQuery.ajax({
                type: "POST",
                url: "/<?php echo TMP_DIR; ?>backend/ajax/ajax_guideuniversal.php",
                dataType: "text",
                data: "id="+id+"&description="+description+"&country_id="+country_id+"&name="+name+"&enable="+enable_tmp+"&sort_order="+sort+"&key=add_manufacturer",
                beforeSend: function(){
                },
                success: function(msg){
                    console.log( msg );
                    location.reload();
                }
            });
        }
        
    });
    
    jQuery(document).on('click','.dell', function(){
        var id = jQuery(this).data('id');
        var table = jQuery('#table').val();
        
        if (confirm('Вы действительно желаете удалить бренд?')){
            jQuery.ajax({
                type: "POST",
                url: "/<?php echo TMP_DIR; ?>backend/ajax/ajax_guideuniversal.php",
                dataType: "text",
                data: "id="+id+"&key=dell_manufacturer",
                beforeSend: function(){
                },
                success: function(msg){
                    console.log( msg );
                    jQuery('#'+id).hide();
                }
            });
        }
    });
    //======================================================================
</script>
