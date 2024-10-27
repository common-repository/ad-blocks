

////////////////////////////////////

jQuery(document).ready(function($){
    
    ////////////////add_datepicker/////////////////
    function add_datepicker(id) {
      $( id ).datepicker({
	    numberOfMonths: 1,
        dateFormat: adb_cmb2_lst.date_format
      });
    }
    
    add_datepicker('.date_input');
    
    $(".date_input, .text-time").on('focus', function(){
      $(this).blur();
    });
    
    ////////////Settings tabs///////
    
    $('.adb-settings-wrap').on('click', '.nav-tab', function(event){
        
        event.preventDefault();
        
        var targ = $(this).data('target');
        
        $('.nav-tab').removeClass('nav-tab-active');
        $('.tab-target').removeClass('tab-target-active');
        $(this).addClass('nav-tab-active');
        $('#'+targ).addClass('tab-target-active');
        
        var ref = $('.adb-settings-wrap input[name="_wp_http_referer"]').val();
        var new_ref = ref;
        
        var query_string = {};
        
        var url_vars = ref.split("?");
        
        if (url_vars.length > 1){
            
            new_ref = url_vars[0] + '?';
            var url_pairs = url_vars[1].split("&");
            for (var i=0;i<url_pairs.length;i++){
                
                var pair = url_pairs[i].split("=");
                
                if (pair[0] != 'setting_tab'){
                    new_ref = new_ref + url_pairs[i] + '&';
                } 
            }
            
        } else { 
            new_ref = new_ref + '?';  
        }
        
        new_ref = new_ref + 'setting_tab=' + targ;
        
        $('.adb-settings-wrap input[name="_wp_http_referer"]').val(new_ref);
        
    });
    
    ///////////////////////////////
    
    $( '.cmb2-wrap > .cmb2-metabox' ).on( 'cmb2_add_row', function( evt, row ) {
            
            //$('.cmb2_select2_parent .cmb2_select').select2();
            
            $( 'input.date_input', row ).each(function(el){
                
                add_datepicker(this);
                
                $(this).on('focus', function(){
                    $(this).blur();
                });
                
            });
		});
        
    $('.cmb2-wrap').on('cmb2_add_group_row_start', function( evt, row ) {
			//$('.cmb2_select2_parent .cmb2_select').select2('destroy');
		});
        
    $('.cmb2-wrap > .cmb2-metabox').on('cmb2_shift_rows_complete', function( evt, row ){
        
       // $('.cmb2_select2_parent .cmb2_select').trigger('change');
        
    });
    
    /////////////////////////////
    

});

/////////////////////////////////////
//////////////////////////////

function alertObj(obj) { 
    var str = ""; 
    for(k in obj) { 
        str += k+": "+ obj[k]+"\r\n"; 
    } 
    alert(str); 
}


