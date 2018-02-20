	$(document).on('click', '.images_list_back', function(){

		$('.images_list').html('');
		$('.images_list_back').hide();
		$('.images_list').hide(500);
		
	});
		
	$(document).on('focus', '.images_list_back', function(){

		$('.attribute_list').hide();
		$('.attribute_list').html('');

	});
	

	$(document).on('click', '.image_item_dell', function(){
		
		var id = $(this).data('id');
		$(this).parent('div').hide(500);
		
		var post = 'key=dell_image';
		post = post + '&id='+id;
		post = post + '&product_id='+$(this).data('product_id');
		
		jQuery.ajax({
            type: "POST",
            url: "/backend/product/ajax_edit.php",
            dataType: "text",
            data: post,
            beforeSend: function(){
            },
            success: function(msg){
                
				console.log( msg );
				
            }
        });
		 
	});
	
	$(document).on('click', '.product_image', function(){
		
		$('.images_list').show(500);
		
		var id = jQuery(this).parent('td').parent('tr').attr('id');
		
		var post = 'key=get_images';
		post = post + '&id='+id;
		
		jQuery.ajax({
            type: "POST",
            url: "/backend/product/ajax_edit.php",
            dataType: "text",
            data: post,
            beforeSend: function(){
            },
            success: function(msg){
               
				$('.images_list').html(msg);
				
				$('.images_list_back').show();
				
            }
        });
		 
	});
	
	jQuery(document).on('change','.edit', function(){
        var id = jQuery(this).parent('td').parent('tr').attr('id');
      
	    var enable_tmp = 0;
         
        if (jQuery('#status'+id).prop('checked')) {
             enable_tmp = 1;
        }

		var post = 'key=edit';
		post = post + '&id='+id;
		post = post + '&mainkey=product_id';
		post = post + '&model='+jQuery('#model'+id).val();
		post = post + '&model7='+jQuery('#model7'+id).val();
		post = post + '&model8='+jQuery('#model8'+id).val();
		post = post + '&model4='+jQuery('#model4'+id).val();
		post = post + '&code='+jQuery('#code'+id).val();
		post = post + '&category_id='+jQuery('#category_id_'+id).val();
		post = post + '&price='+jQuery('#price'+id).val();
		post = post + '&sort_order='+jQuery('#sort_order'+id).val();
		post = post + '&size_group_id='+jQuery('#size_group_id'+id).val();
		post = post + '&zakup='+jQuery('#zakup'+id).val();
		post = post + '&zakup_currency_id='+jQuery('#zakup_currency_id'+id).val();
		post = post + '&manufacturer_id='+jQuery('#manufacturer_id'+id).val();
		post = post + '&table=product';
		post = post + '&status='+enable_tmp;
	    //console.log( post );
		
        jQuery.ajax({
            type: "POST",
            url: "/backend/ajax/ajax_edit_product.php",
            dataType: "text",
            data: post,
            beforeSend: function(){
            },
            success: function(msg){
                console.log( msg );
            }
        });
        
    });
	

	
	jQuery(document).on('click','.add', function(){
       $('#container_back').show();
	    var enable_tmp = 0;
         
        if (jQuery('#status').prop('checked')) {
             enable_tmp = 1;
        }

		var post = 'key=add';
		post = post + '&id=0';
		post = post + '&mainkey=product_id';
		post = post + '&model='+jQuery('#model').val();
		post = post + '&model7='+jQuery('#model7').val();
		post = post + '&model8='+jQuery('#model8').val();
		post = post + '&model4='+jQuery('#model4').val();
		post = post + '&code='+jQuery('#code').val();
		post = post + '&category_id='+jQuery('#category_id_new').val();
		post = post + '&price='+jQuery('#price').val();
		post = post + '&sort_order='+jQuery('#sort_order').val();
		post = post + '&zakup='+jQuery('#zakup').val();
		post = post + '&zakup_currency_id='+jQuery('#zakup_currency_id').val();
		post = post + '&size_group_id='+jQuery('#size_group_id').val();
		post = post + '&manufacturer_id='+jQuery('#manufacturer_id').val();
		post = post + '&table=product';
		post = post + '&status='+enable_tmp;
	  
	  
        jQuery.ajax({
            type: "POST",
            url: "/backend/ajax/ajax_edit_product.php",
            dataType: "text",
            data: post,
            beforeSend: function(){
            },
            success: function(product_id){
                //console.log( product_id );
				
				jQuery.each($('#product_attribute_wrappernew div a'), function(index, value){
			
					//console.log($(value).data('attribute_id'));
					saveAttribute(product_id, 0, $(value).data('attribute_id'), '');
			
				});
				location.reload();
				
            }
        });
        
    });
	
	
	$(document).on('change', '#dellall', function(){
		$.each($('.dell_check'), function( index, value ) {
			
			$(this).prop('checked', $('#dellall').prop('checked'));	
				
		});
		
	});
	
	$(document).on('click', '.dell_key_all', function(){
		
		product_dell();
	});
	
	$(document).on('click', '.dell_key', function(){
		
		var id = jQuery(this).data('id');
		
		$('#dell'+id).prop('checked', true);	
		
		product_dell();
	});
	
	function product_dell() {
		if (confirm('Вы действительно желаете удалить товар?\n\r\n\rНЕ ЗАБЫВАЙТЕ ЧИСТИТЬ ФОТО ПОСЛЕ УДАЛЕНИЯ ТОВАРА!')){
		
			$.each($('.dell_check'), function( index, value ) {
				//debugger;	
				if($(this).prop('checked') == true){
					
					var id = jQuery(this).data('id');
						//console.log($(this).prop('checked')+' '+id);
					jQuery.ajax({
						type: "POST",
						url: "/backend/ajax/ajax_edit_product.php",
						dataType: "text",
						data: "id="+id+"&key=dell",
						beforeSend: function(){
						},
						success: function(msg){
							//console.log( msg );
							jQuery('#'+id).hide();
						}
				
					});
					
				}
				
			});
		}
    }
    
	$(document).on('mouseenter','.model_generator', function(){
		$('.model_generator_msg').show();
	});
	
	$(document).on('mouseleave','.model_generator', function(){
		$('.model_generator_msg').hide();
	});
	
	$(document).on('click','.model_generator', function(){
		var product_id = $(this).data('product_id');
		
		jQuery.ajax({
				type: "POST",
				url: "/backend/ajax/ajax_edit_product.php",
				dataType: "text",
				data: "id="+product_id+"&key=make_model",
				beforeSend: function(){
				},
				success: function(msg){
					jQuery('#model'+product_id).val(msg);
					console.log(msg);
				}
		
		});
		
	});	
	
