$(function() {
	
	var base_url      = $('#base_url').val();
	
	
	//-------------------------------
	// EVENTS 
	//-------------------------------
	
 	// Toggles Devmode
	$('.toggle-devmode').change(function() {
		
		 
  
		if($(this).prop('checked')){
			window.location.replace(base_url+"C=addons_modules&M=show_module_cp&module=cloudflare&method=toggle_developer_mode&enable=1" );
		} else {
			window.location.replace(base_url+"C=addons_modules&M=show_module_cp&module=cloudflare&method=toggle_developer_mode&enable=0");
		}
       
    });
    
    // Toggles Minify Options
    $('#toggle-minify-js').change(function() {
		 
		
		if($(this).prop('checked')){
			window.location.replace(base_url+"C=addons_modules&M=show_module_cp&module=cloudflare&method=set_minify&type=js&value=1" );
		} else {
			window.location.replace(base_url+"C=addons_modules&M=show_module_cp&module=cloudflare&method=set_minify&type=js&value=0");
		}
    });
    $('#toggle-minify-css').change(function() {
		 
		
		if($(this).prop('checked')){
			window.location.replace(base_url+"C=addons_modules&M=show_module_cp&module=cloudflare&method=set_minify&type=css&value=1" );
		} else {
			window.location.replace(base_url+"C=addons_modules&M=show_module_cp&module=cloudflare&method=set_minify&type=css&value=0");
		}
    });
    $('#toggle-minify-html').change(function() {
		
		
		if($(this).prop('checked')){
			window.location.replace(base_url+"C=addons_modules&M=show_module_cp&module=cloudflare&method=set_minify&type=html&value=1" );
		} else {
			window.location.replace(base_url+"C=addons_modules&M=show_module_cp&module=cloudflare&method=set_minify&type=html&value=0");
		}
    });
         
});