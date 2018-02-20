$(document).on('focus', 'input', function(){
    
    var value = $(this).val();
    
    if(value == '0' || value == '0.0' || value == '0.00'){
        $(this).val('');
        $.data(this, 'old_value', value);
        
    }
    
});

$(document).on('focusout', 'input', function(){
    
    var data = $(this).data('old_value');
    var value = $(this).val();

    console.log(data);
    
    if(value == '' && (data == '0' || data == '0.0' || data == '0.00')){
        $(this).val(data);
    }
    
});

	jQuery(document).on('click','.barcode-print', function(){
		
		var barcode = jQuery(this).data('barcode');
      
		var post = 'barcode='+barcode;
		
		jQuery.ajax({
            type: "GET",
            url: "/backend/print/ajax_zebra_print.php",
            dataType: "text",
            data: post,
            beforeSend: function(){
            },
            success: function(msg){
                
				console.log( msg );
				alert('Принтер не подключен');
            }
        });
	});
	