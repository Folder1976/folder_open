//Вывод продукта на редактирование
			jQuery(document).on('click','.link', function(){
				
				var product_id = $(this).attr('id');
				jQuery('#edit_product_id').val(product_id);
				
				post = 'key=get_product';
				post = post + '&product_id='+product_id;
				
				
				jQuery.ajax({
					type: "POST",
					url: "/backend/operation/ajax_edit_operation.php",
					dataType: "json",
					data: post,
					beforeSend: function(){
					},
					success: function(msg){
						
						console.log( msg );
						
						$('#model').val(msg.model);
						$('#model7').val(msg.model7);
						$('#model8').val(msg.model8);
						$('#model4').val(msg.model4);
						$('#code').val(msg.code);
						$('#zakup').val(msg.last_zakup);
						$('#price').val(msg.last_price_invert);
						$('#sort_order').val(msg.sort_order);
						$('#category_id_new').val(msg.category_id);
						if(typeof(msg.category.path) != "undefined"){
							$('#name_category_id_new').html(msg.category.path);
						}else{
							$('#name_category_id_new').html('нет');	
						}
						
						//$('#category_path_new').html();
						$('#manufacturer_id').val(msg.manufacturer_id);
						$('#size_group_id').val(msg.size_group_id);
						$('#size_group_id').trigger('change');
						
						$('#product_attribute_wrappernew div').remove();
						jQuery.each(msg.attributes, function(index, value){
							
							//console.log(value);
							
							attr = '';
							attr = attr + '<div><a href="javascript:;" class="dell_attribute" data-product_id="new" data-attribute_id="'+index+'">';
							attr = attr + '<img src="/backend/img/cancel.png" title="удалить" width="12" height="12">';
							attr = attr + '</a><b>'+value.group_name+' : </b>'+value.name+'</div>';
							
							$('#product_attribute_wrappernew').append(attr);
							
						});
						
						
					}
				});
				
			});
			//.add
			jQuery(document).on('click','.add_new_product .add', function(){
				
				$('.find_result_shirm_add').show();
				
				var enable_tmp = 0;
				  
				if (jQuery('#status').prop('checked')) {
					  enable_tmp = 1;
				}
				 
				//Новый продукт или отредактированный
				var product_id = 0;
				if(jQuery('#edit_product_id').val() > 0){
					product_id = jQuery('#edit_product_id').val();
				}
				
				var post = 'key=add';
				post = post + '&id='+product_id;
				post = post + '&mainkey=product_id';
				post = post + '&model='+jQuery('#model').val();
				post = post + '&model7='+jQuery('#model7').val();
				post = post + '&model8='+jQuery('#model8').val();
				post = post + '&model4='+jQuery('#model4').val();
				post = post + '&product_id='+jQuery('#edit_product_id').val();
				post = post + '&code='+jQuery('#code').val();
				post = post + '&category_id='+jQuery('#category_id_new').val();
				post = post + '&sort_order='+jQuery('#sort_order').val();
				post = post + '&price='+jQuery('#price').val();
				post = post + '&price_invert='+jQuery('#price').val();
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
						
						console.log('Создал продукт - '+product_id);
						 
						jQuery.each($('#product_attribute_wrappernew div a'), function(index, value){
							saveAttribute(product_id, 0, $(value).data('attribute_id'), '');
						});
						
						//Добавляем продукт
						var operation_id = $('#operation_id').val();
						
						if(operation_id > 0){
							
							//debugger;
							
							var post = 'key=add_new_product';
							post = post + '&operation_id='+operation_id;
							post = post + '&product_id='+product_id;
							post = post + '&currency_id='+$('#global_currency_id').val();
							post = post + '&master_id='+$('#global_master_id').val();
							post = post + '&customer_id='+$('#global_customer_id').val();
							post = post + '&type_id='+$('#type_id').val();
							post = post + '&zakup='+jQuery('#zakup').val();
							post = post + '&price_invert='+jQuery('#price').val();;
							
							$.each($('.add_size_wrapper_new input'), function(value, index){
								
								//debugger;
								
								
								post_1 = post;
								post_1 = post_1 + '&size_id='+$(index).data('size_id');
								post_1 = post_1 + '&quantity='+$(index).val();
								
								$(index).val();
								
								//console.log(post_1);
								
								if($(index).val() > 0){	
									jQuery.ajax({
										type: "POST",
										url: "/backend/operation/ajax_edit_operation.php",
										dataType: "text",
										data: post_1,
										beforeSend: function(){
										},
										success: function(msg){
											
											//debugger;
											console.log( msg );
											
											$('.find_result_shirm_add').hide();	
											
										}
									});
								}
									
							});
							
							
							jQuery('#model').val('');
							jQuery('#model7').val('');
							jQuery('#model8').val('');
							jQuery('#model4').val('');
							jQuery('#code').val('');
							$('#product_attribute_wrappernew div').remove();
							$('#product_attribute_groupnew').val(0);
							$('#size_group_id').val(0);
							$('#manufacturer_id').val(0);
							$('#zakup').val('0.00');
							$('#price').val('0.00');
							$('#sort_order').val('0');
							$('#size_group_id').trigger('change');
						
							$.each($('.add_size_wrapper_new input'), function(value, index){
								
								$(index).val('0');
							
							});
							
							/*
							jQuery.each($('#product_attribute_wrappernew div a'), function(index, value){
								saveAttribute(product_id, 0, $(value).data('attribute_id'), '');
							});
							*/
						
							$('.find_result_shirm_add').hide();
							jQuery('#edit_product_id').val('');
						}
						
					}
					
				});
				 
			 });
            
   
	
	jQuery(document).on('change','#product_list_order', function(){
		location.href = "/backend/index.php?route="+$('#route').val()+"&operation_id="+$('#operation_id').val()+"&order="+$(this).val();
		//find_product();
	});
	
	jQuery(document).on('keyup','#find_model', function(){
		
		find_product();
	});
	
	jQuery(document).on('focus','#find_model', function(){
		
		find_product();
	});
	
	/*
	jQuery(document).on('keyup','#find_code', function(){
    
		find_product();
	});
	*/
	jQuery(document).on('click keyup','#find_code', function(){
		find_product();
	});
	
	function find_product(){
		$('.find_result_shirm').show();
		
		//debugger;
		
		var post = 'key=find_product';
		post = post + '&model='+$('#find_model').val();
		post = post + '&code='+$('#find_code').val();
		post = post + '&type_id='+$('#type_id').val();
		post = post + '&manufacturer_id='+$('#find_manufacturer_id').val();
		post = post + '&shop_id='+$('#find_shop_id').val();
		post = post + '&product_list_order='+$('#product_list_order').val();
		post = post + '&warehouse_id='+$('#find_warehouse_id').val();
		post = post + '&operation_id='+$('#find_operation_id').val();
		post = post + '&operation_find_id='+$('#invert_operation_id').val();
		
		if($('#global_is_code').prop('checked')){
			post = post + '&is_code=1';
		}
		
		if($('#find_model').val() == '' && $('#find_code').val() == ''){
			$('.find_result_shirm').hide();
			$('.find_result').show();
		}else{
			jQuery.ajax({
				type: "GET",
				url: "/backend/operation/ajax_edit_operation.php",
				dataType: "text",
				data: post,
				beforeSend: function(){
				},
				success: function(msg){
					
					//console.log( msg );
					$('.find_result_shirm').hide();
					$('.find_result_wrapper').html(msg);
					$('.find_result').show();
					//$('.find_result_back').show();
				}
			});
		}
		
		$('#size_group_id').trigger('change');
		
    }
	

	jQuery(document).on('change','#size_group_id', function(){
		
		var post = 'key=get_sizes';
		post = post + '&size_group_id='+$(this).val();
		
		jQuery.ajax({
				type: "GET",
				url: "/backend/operation/ajax_edit_operation.php",
				dataType: "text",
				data: post,
				beforeSend: function(){
				},
				success: function(msg){
					//console.log(msg);
					
					$('.add_size_wrapper_new').html(msg);
					
				}
		});
	
	});
	
	jQuery(document).on('click','.find_result_close', function(){
		$('.find_result').hide();
		$('.find_result_back').hide();
		location.reload();
	});
	
	jQuery(document).on('click','#add_new_operation', function(){
       
		//debugger;
		var post = 'key=add_new_operation';
		post = post + '&type_id='+$('#type_id').val();
		post = post + '&user_id='+$('#user_id').val();
		post = post + '&to_warehouse_id='+$('#to_warehouse_id').val();
		post = post + '&from_warehouse_id='+$('#from_warehouse_id').val();
		post = post + '&customer_id='+$('#customer_id').val();
		post = post + '&comment='+$('#comment').val();
		post = post + '&summ='+$('#summ').val();
		
		if(($('#type_id').val() == 12 || $('#type_id').val() == "") && ($('#summ').val() == 0 || $('#summ').val() == "")){
			console.log($('#type_id').val());
		}else{
		
			jQuery.ajax({
				type: "POST",
				url: "/backend/operation/ajax_edit_operation.php",
				dataType: "text",
				data: post,
				beforeSend: function(){
				},
				success: function(msg){
					
					console.log(msg);
					
					//Оплаты
					if($('#type_id').val() == 12){
						location.href = "/backend/index.php?route="+$('#route').val();
					}else{
						location.href = "/backend/index.php?route="+$('#route').val()+"&operation_id="+msg;
					}
				}
			});
		}
		
    });
	
	jQuery(document).on('click','#add_new_oplata', function(){
       
		//debugger;
		var post = 'key=add_new_oplata';
		post = post + '&type_id='+$('#type_id').val();
		post = post + '&user_id='+$('#user_id').val();
		post = post + '&to_warehouse_id='+$('#to_warehouse_id').val();
		post = post + '&from_warehouse_id='+$('#from_warehouse_id').val();
		post = post + '&customer_id='+$('#customer_id').val();
		post = post + '&comment='+$('#comment').val();
		post = post + '&summ='+$('#summ').val();
		
		if(($('#type_id').val() == 12 || $('#type_id').val() == "") && ($('#summ').val() == 0 || $('#summ').val() == "")){
			console.log($('#type_id').val());
		}else{
		
			jQuery.ajax({
				type: "POST",
				url: "/backend/operation/ajax_edit_operation.php",
				dataType: "text",
				data: post,
				beforeSend: function(){
				},
				success: function(msg){
					
					console.log(msg);
					
					//Оплаты
					if($('#type_id').val() == 12){
						location.href = "/backend/index.php?route="+$('#route').val();
					}else{
						location.href = "/backend/index.php?route="+$('#route').val()+"&operation_id="+msg;
					}
				}
			});
		}
		
    });
	
	jQuery(document).on('click','.add_product_to_operation', function(e){
        	
		//debugger;
		
		console.log(e.target)
			
		var operation_id = $('#operation_id').val();
		var product_id = $(this).parent('tr').attr('id');
		
		if(product_id == undefined){
			product_id = $(this).data('product_id');
		}
		
		sub_operation_id = 0;
		if($('#sub_operation_id').length){
			var sub_operation_id = $('#sub_operation_id').val();
		}
		
		if(operation_id > 0){
			var post = 'key=add_new_product';
			post = post + '&operation_id='+operation_id;
			post = post + '&sub_operation_id='+sub_operation_id;
			post = post + '&product_id='+product_id;
			post = post + '&zakup='+$('#add_zakup'+product_id).val();
			post = post + '&type_id='+$('#type_id').val();
			post = post + '&currency_id='+$('#global_currency_id').val();
			post = post + '&master_id='+$('#global_master_id').val();
			post = post + '&customer_id='+$('#global_customer_id').val();
			post = post + '&price_invert='+$('#add_price_invert'+product_id).val();
			
			
			
			$.each($('.add_size_wrapper'+product_id+' input'), function(value, index){
				
				post_1 = post;
				post_1 = post_1 + '&size_id='+$(index).data('size_id');
				post_1 = post_1 + '&quantity='+$(index).val();
				
				console.log(post_1);
				
				if($(index).val() > 0){	
					jQuery.ajax({
						type: "POST",
						url: "/backend/operation/ajax_edit_operation.php",
						dataType: "text",
						data: post_1,
						beforeSend: function(){
						},
						success: function(msg){
							
							console.log( msg );
							
							if($("#global_auto").prop('checked')){
								
								//$(".find_result_close").trigger("click");
								
							}
							
						}
					});
				}
					
			});
			
			$('.res'+product_id).hide();
		}
    });
    
    jQuery(document).on('click','.dell', function(){
        
		var operation_id = $('#operation_id').val();
		var sub_operation_id = $('#sub_operation_id').val();
		var row_id = $(this).data('id');
		var res = row_id.split('_');
		var product_id = res[0];
		var zakup = res[1];
		
		if (confirm('Вы действительно желаете удалить эту строку?')){
			if(operation_id > 0){
				var post = 'key=dell_row';
				post = post + '&operation_id='+operation_id;
				post = post + '&sub_operation_id='+sub_operation_id;
				post = post + '&product_id='+product_id;
				post = post + '&zakup='+zakup;
				
				jQuery.ajax({
					type: "POST",
					url: "/backend/operation/ajax_edit_operation.php",
					dataType: "text",
					data: post,
					beforeSend: function(){
					},
					success: function(msg){
						
						$('#summ').val(msg+'.00');
						
						console.log( msg );
						
						jQuery('#'+row_id).hide();
					}
				});
			}
		}
    });   
    jQuery(document).on('change','.size', function(){
        
		//debugger;
		
		var operation_id = $('#operation_id').val();
		var sub_operation_id = $('#sub_operation_id').val();
		var row_id = $(this).data('row_id');
		var res = row_id.split('_');
		var product_id = res[0];
		var zakup = res[1];
		var master_id = res[2];
		var size_id = $(this).data('size_id');
		var price_invert = $('#price_invert'+row_id).val();
		var quantity = $(this).val();
		
		if(operation_id > 0){
			var post = 'key=edit_quantity';
			post = post + '&operation_id='+operation_id;
			post = post + '&sub_operation_id='+sub_operation_id;
			post = post + '&product_id='+product_id;
			post = post + '&price_invert='+price_invert;
			post = post + '&zakup='+zakup;
			post = post + '&master_id='+master_id;
			post = post + '&size_id='+size_id;
			post = post + '&quantity='+quantity;
			
			jQuery.ajax({
				type: "POST",
				url: "/backend/operation/ajax_edit_operation.php",
				dataType: "text",
				data: post,
				beforeSend: function(){
				},
				success: function(msg){
					
					$('#summ').val(msg+'.00');
					
					console.log( msg );
					
					sub_summ();
				}
			});
		}
		
		setTotalQuantity(row_id);
		
    });
 
	function sub_summ(){
		
		
		if($('#sub_operation_id').length > 0){
			
			var	sub_operation_id = $('#sub_operation_id').val();
			
			if(sub_operation_id > 0){
				
				var operation_id = $('#operation_id').val();
				
				var post = 'key=get_sub_operation_summ';
				post = post + '&operation_id='+operation_id;
				post = post + '&sub_operation_id='+sub_operation_id;
				
				jQuery.ajax({
					type: "POST",
					url: "/backend/operation/ajax_edit_operation.php",
					dataType: "text",
					data: post,
					beforeSend: function(){
					},
					success: function(msg){
						
						$('#sub_operation_sum').html(msg+'.00');
						
						console.log( msg );
						
					}
				});
			}
			
		}
		
	}
 
	function setTotalQuantity(row_id){
		
		//debugger;
		
		var quantity = 0;
		
		$.each($('#'+row_id+' .size_wrapper input'), function(value, index){
		
			quantity = quantity + parseInt($(index).val());
			
		});
		
		
		$('#total_quantity'+row_id).html(quantity);
		
		var zakup = parseInt($('#zakup'+row_id).val());
		
		var indx = parseInt($('#currency_id'+row_id).val());
		
		var curr = currencys[indx];
		console.log(indx+' '+currencys[indx]);
		
		var zakup_grn = zakup / currencys[indx];
		
		//$('#summ_'+row_id+' .number').html((quantity * zakup_grn)+' '+$('#summ_'+row_id).data('currency_simbol'));
		$('#zakup_grn'+row_id+' .number').html(parseInt(zakup_grn));
		$('#summ_'+row_id+' .number').html(parseInt(quantity * zakup_grn));
	}
 
	jQuery(document).on('change','.zakup', function(){
        
		//debugger;
		
		var operation_id = $('#operation_id').val();
		var row_id = $(this).parent('td').parent('tr').attr('id');
		var res = row_id.split('_');
		var product_id = res[0];
		var zakup = res[1];
		var master_id = res[2];
		var new_zakup = $(this).val();
		
		var currency_id = $('#currency_id'+row_id).val();
		
		
		tmp = new_zakup.split('.');
		new_zakup = tmp[0];
		
		
		if(operation_id > 0){
			var post = 'key=edit_zakup_prihod';
			post = post + '&operation_id='+operation_id;
			post = post + '&product_id='+product_id;
			post = post + '&zakup='+zakup;
			post = post + '&master_id='+master_id;
			post = post + '&new_zakup='+new_zakup;
			post = post + '&currency_id='+currency_id;
			
			jQuery.ajax({
				type: "POST",
				url: "/backend/operation/ajax_edit_operation.php",
				dataType: "text",
				data: post,
				beforeSend: function(){
				},
				success: function(msg){
					$('#summ').val(msg+'.00');
					
					$('#'+row_id).children('td').first().html(product_id+'_'+new_zakup+'_'+master_id);
					$('#'+row_id).attr('id', product_id+'_'+new_zakup+'_'+master_id);
					$('#master_id'+row_id).attr('id', 'master_id'+product_id+'_'+new_zakup+'_'+master_id);
					$('#zakup'+row_id).attr('id', 'zakup'+product_id+'_'+new_zakup+'_'+master_id);
					$('#zakup_grn'+row_id).attr('id', 'zakup_grn'+product_id+'_'+new_zakup+'_'+master_id);
					$('#price_invert'+row_id).attr('id', 'price_invert'+product_id+'_'+new_zakup+'_'+master_id);
					$('#summ_'+row_id).attr('id', 'summ_'+product_id+'_'+new_zakup+'_'+master_id);
					$('#currency_id'+row_id).attr('id', 'currency_id'+product_id+'_'+new_zakup+'_'+master_id);
					
					
					console.log( 'zakup' );
					console.log( msg );
					
					sub_summ();
				}
			});
		}
		
		setTotalQuantity(row_id);
    });
	
	jQuery(document).on('change','.edit_master', function(){
        
		//debugger;
		
		var operation_id = $('#operation_id').val();
		var row_id = $(this).parent('td').parent('tr').attr('id');
		var res = row_id.split('_');
		var product_id = res[0];
		var zakup = res[1];
		var master_id = res[2];
		var new_master_id = $(this).val();
		
		if(operation_id > 0){
			var post = 'key=edit_master_prihod';
			post = post + '&operation_id='+operation_id;
			post = post + '&product_id='+product_id;
			post = post + '&zakup='+zakup;
			post = post + '&master_id='+master_id;
			post = post + '&new_master_id='+new_master_id;
			
			jQuery.ajax({
				type: "POST",
				url: "/backend/operation/ajax_edit_operation.php",
				dataType: "text",
				data: post,
				beforeSend: function(){
				},
				success: function(msg){
					$('#summ').val(msg+'.00');
					
					//debugger;
					
					$('#'+row_id).children('td').first().html(product_id+'_'+zakup+'_'+new_master_id);
					$('#'+row_id).attr('id', product_id+'_'+zakup+'_'+new_master_id);
					
					$('#master_id'+row_id).attr('id', 'master_id'+product_id+'_'+zakup+'_'+new_master_id);
					$('#zakup'+row_id).attr('id', 'zakup'+product_id+'_'+zakup+'_'+new_master_id);
					$('#zakup_grn'+row_id).attr('id', 'zakup_grn'+product_id+'_'+zakup+'_'+new_master_id);
					$('#summ_'+row_id).attr('id', 'summ_'+product_id+'_'+zakup+'_'+new_master_id);
					$('#price_invert'+row_id).attr('id', 'price_invert'+product_id+'_'+zakup+'_'+new_master_id);
					$('#currency_id'+row_id).attr('id', 'currency_id'+product_id+'_'+zakup+'_'+new_master_id);
					
					
					console.log( 'master_id' );
					console.log( msg );
					
					sub_summ();
				}
			});
		}
		
		//setTotalQuantity(row_id);
    });
	  
	   jQuery(document).on('change','.add_size', function(){
			var quantity = 0;
			
			var row_id = $(this).parent('div').parent('td').parent('tr').attr('id');
			if(typeof(row_id) == "undefined"){
				row_id = '_'+$(this).parent('div').parent('div').parent('td').parent('tr').attr('id');
			}
		
			$.each($('.add_size_wrapper'+row_id+' input'), function(value, index){
				quantity = quantity + parseInt($(index).val());
			});
			
			$('#total_quantity'+row_id).html(quantity);

	   });
 
	  jQuery(document).on('change','.edit_products', function(){
        
		var operation_id = $('#operation_id').val();
		var row_id = $(this).parent('td').parent('tr').attr('id');
		var res = row_id.split('_');
		var product_id = res[0];
		var zakup = res[1];
		var price_invert = $(this).val();
		
		var element = $(this);
		
		if(operation_id > 0){
			var post = 'key=edit_products';
			post = post + '&operation_id='+operation_id;
			post = post + '&product_id='+product_id;
			post = post + '&zakup='+zakup;
			post = post + '&price_invert='+price_invert;
			post = post + '&master_id='+$('#master_id'+row_id).val();
			post = post + '&customer_id='+$('#customer_id'+row_id).val();
			post = post + '&currency_id='+$('#currency_id'+row_id).val();
			
			
			
			jQuery.ajax({
				type: "POST",
				url: "/backend/operation/ajax_edit_operation.php",
				dataType: "text",
				data: post,
				beforeSend: function(){
				},
				success: function(msg){
					
					//console.log( msg );
					
					//console.log(element.hasClass('currency_id'));
					
					if(element.hasClass('currency_id')){
				
							$(element).parent('td').children('input').trigger('change');
				
					}
					
					
				}
			});
		}
    });
 
	jQuery(document).on('change','.header_edit', function(){
        
		var operation_id = $('#operation_id').val();
		
		if(operation_id > 0){
			var post = 'key=edit';
			post = post + '&id='+operation_id;
			post = post + '&mainkey=operation_id';
			post = post + '&'+jQuery(this).attr('id')+'='+jQuery(this).val();
			post = post + '&table=operation';
			
			
			jQuery.ajax({
				type: "POST",
				url: "/backend/ajax/ajax_edit_universal.php",
				dataType: "text",
				data: post,
				beforeSend: function(){
				},
				success: function(msg){
					console.log( msg );
				}
			});
		}
    });
            
    $(document).on('change','#shop_id', function(){
	
		location.reload();	
		
	});
	
	$(document).ready(
		function(){
			
			if($('#global_coder').prop('checked')){
				console.log('auto code on');
				$('#find_code').focus();
			}
			
		}
	);