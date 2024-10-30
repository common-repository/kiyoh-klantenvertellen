jQuery(document).ready(function($){

    var kk_provider_field = $('select[name="kk_plugin_settings[kk_provider]"]');

    show_hide_kk_plugin_sections();

    kk_provider_field.change(function(){
        show_hide_kk_plugin_sections();
    });

    function show_hide_kk_plugin_sections(){
        var val = kk_provider_field.val();

        $('.kk-plugin-settings-box-show-hide').hide();
        if (val == 'klantenvertellen') {
            $('#kk-plugin-klantenvertellen-settings').show();
        } else if (val == 'klantenvertellen_mobiliteit') {
            $('#kk-plugin-klantenvertellen-mobiliteit-settings').show();
        } else if (val == 'klantenvertellen_v2') {
            $('#kk-plugin-klantenvertellen-v2-settings').show();
        } else if (val == 'kiyoh_com') {
            $('#kk-plugin-kiyoh-com-settings').show();
        } else if (val == 'kiyoh_api') {
            $('#kk-plugin-kiyoh-api-settings').show();
        } else if (val == 'klantenvertellen_api') {
            $('#kk-plugin-klantenvertellen-api-settings').show();
        } else {
            $('#kk-plugin-kiyoh-settings').show();
        }
    }
	
	var main_settings_id_check = 'kk-plugin-default-settings';
    var main_settings_el = $('.kk-plugin-main-settings div[id*="'+main_settings_id_check+'"]');

    $('#wpbody').on('change', '.kk-plugin-main-settings div[id*="'+main_settings_id_check+'"] select', function(){
        kk_do_conditional(main_settings_el);
    });

    kk_do_conditional(main_settings_el);

    function kk_do_conditional(main_settings_el)
    {
		var check_stars_size = main_settings_el.find('select[name$="[stars_size]"]').closest('tr');
		var check_stars_theme = main_settings_el.find('select[name$="[stars_theme]"]').closest('tr');
		var hide_stars=false;
		var hide_stars_average=false;
                    
        main_settings_el.find('input[type!="checkbox"], select').each(function(){

                var input_name = $( this ).attr('name');
                var input_value = $(this).val();

				if (input_name.indexOf('show_average_stars') !== -1) {

                   if (input_value == 'no') {
                        hide_stars_average=true;
                    } else {
                        hide_stars_average=false; 
                    }
                }
				if (input_name.indexOf('show_stars') !== -1) {
                   if (input_value == 'no') {
                        hide_stars=true;
                    } else {
                        hide_stars=false; 
                    }
                }

                if (input_name.indexOf('layout') !== -1) {
                    var check_el = main_settings_el.find('select[name$="[auto_slide]"]').closest('tr');
                    if (input_value != 'slider') {
                        check_el.hide();
                    } else {
                        check_el.show();
                    }
                }

                if (input_name.indexOf('show_logo') !== -1) {
                    var check_el = main_settings_el.find('select[name$="[logo_type]"]').closest('tr');
                    if (input_value == 'no') {
                        check_el.hide();
                    } else {
                        check_el.show();
                    }
                }

                if (input_name.indexOf('do_show_reviews') !== -1) {
                    var check_el = main_settings_el.find('input[name$="[show_reviews_amount]"]').closest('tr');
                    var check_el_a = main_settings_el.find('select[name$="[show_review_rating]"]').closest('tr');
                    var check_el_b = main_settings_el.find('input[name$="[start_with_review]"]').closest('tr');
                    var check_el_c = main_settings_el.find('input[name$="[limit_review_length]"]').closest('tr');
                    if (input_value == 'no') {
                        check_el.hide();
                        check_el_a.hide();
                        check_el_b.hide();
                        check_el_c.hide();
                    } else {
                        check_el.show();
                        check_el_a.show();
                        check_el_b.show();
                        check_el_c.show();
                    }
                }

        });
		
		if(hide_stars==true && hide_stars_average==true) {
			check_stars_size.hide();
			check_stars_theme.hide();
		} else {
			check_stars_size.show();
			check_stars_theme.show();
		}

    }


});