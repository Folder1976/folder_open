$(document).on('click', 'span', function(event){
                           
    var id = event.target.id;
   
    if (id) {
        
        console.log('Дерево');
        
        var name = $('.category_id_'+id).html();
        //$('.selected_category').html(name);
        //$('.selected_category_id').val(id);
        
        if(name == undefined){
            
        }else{
            $('#name_'+$('#target_categ_id').val()).html(name);
            $('#'+$('#target_categ_id').val()).val(id);
            
             $('#'+$('#target_categ_id').val()).trigger('change');
            
            $('#container').hide();
            $('#container_back').hide();
           
            $('#input_category_name').val(name);
           
                                                  
        }
    }else{
                  
        if ($(this).attr('class').search("handle ") != -1) {
                      $(this).toggleClass('closed opened').nextAll('ul').toggle(300);
        }
                  
     }
});