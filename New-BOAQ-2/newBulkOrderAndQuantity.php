<?php

/*
Plugin Name: Bulk Quantity & Wc Custom Addons
*/

register_activation_hook(__FILE__, 'ss_create_table_custom'); 
register_activation_hook(__FILE__, 'create_imprint_loc_color_attribute_taxonomies');
register_deactivation_hook( __FILE__, 'ss_pluginprefix_deactivate' );


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////// ONE TIME CODE RAN /////////////////////////////
function ss_pluginprefix_deactivate() {
	delete_option('run_only_once_01');
}

function scratchcode_run_code_one_time() {
	if ( !get_option('run_only_once_01') ):

    // Execute your one time code here      
    function call_back_func_for_geting_val_location_att($value,$key)  {
    	wp_insert_term( $value, 'pa_imprint-location' );
	}

    $loc_Arr = array("Full Back","Full front","Left Chest", "Left Sleeve","Right Chest", "Right Sleeve");
    
	array_walk($loc_Arr,"call_back_func_for_geting_val_location_att");
	
	function call_back_func_for_geting_val_colors_att($value,$key)  {
    	wp_insert_term( $value, 'pa_print-colors' );
    }

    $colors_Arr = array("White","Ivory ( 9200C )","Black", "Black Chrome (B7C)","Hi-Vis Orange (811C)", "Orange (164C)", "Light Orange (158C)");
    
	array_walk($colors_Arr,"call_back_func_for_geting_val_colors_att");
			
    add_option('run_only_once_01', 1); 
  	endif;
}
add_action( 'init', 'scratchcode_run_code_one_time' );

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////// ENQUEE STYLES AND SCRIPTS FOR FRONETEND /////////////////////////////
function alie_ctm_enqueue_script_for_clientSite()
	{
		wp_enqueue_style('ss_custom_frontend_style_', plugin_dir_url(__FILE__) . 'assets/css/style.css');
		wp_enqueue_style('select2-stle', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
		wp_enqueue_script('select2_js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'));
	}

add_action('wp_enqueue_scripts', 'alie_ctm_enqueue_script_for_clientSite', 99);

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////// ENQUEE STYLES AND SCRIPTS FOR ADMIN /////////////////////////////
function alie_ctm_enqueue_script_for_adminSite()
	{
		wp_enqueue_style('ss_custom_admin_style_', plugin_dir_url(__FILE__) . 'assets/css/admin-style.css');
		wp_enqueue_style('select2-stle', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
		wp_enqueue_script('select2_js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'));
		wp_enqueue_script('ss-mycustom', plugin_dir_url(__FILE__) . '/assets/js/mycustom.js', array('jquery', 'select2_js'));
	}

add_action('admin_enqueue_scripts', 'alie_ctm_enqueue_script_for_adminSite', 99);

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////// INCLUDE EXTERNAL FILES /////////////////////////////
include(plugin_dir_path(__FILE__) . '/assets/wcMetas/custm_meta_boxes.php');
include(plugin_dir_path(__FILE__) . '/assets/inc/function_file_code.php');
// include(plugin_dir_path(__FILE__) . '/assets/inc/product_base_discount_meta_box.php');
include(plugin_dir_path(__FILE__) . '/assets/settingPages/qty-base-discount.php');
include(plugin_dir_path(__FILE__) . '/assets/inc/product_page_js.php');
include(plugin_dir_path(__FILE__) . '/assets/inc/cart-page.php');
include(plugin_dir_path(__FILE__) . '/assets/inc/customJs.php');

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////// GET MIN MAX  /////////////////////////////
function get_min_max ($ss_col, $ss_table_name) {
    global $wpdb;
    return $wpdb->get_row("SELECT MIN(CAST(".$ss_col." AS int)) AS ss_min, MAX(CAST(".$ss_col." AS int)) AS ss_max FROM " . $ss_table_name);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////// CREATE DATABASE FUNCITON???????? /////////////////////////////////////////////////////////////

if (!defined('ABSPATH')) {
	exit;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////// BULKCSS  /////////////////////////////////////////////////////////////

add_action('wp_head', 'bulkcss');

function bulkcss() {
	global $product;
	
	if (is_product()) {
	}

}



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////// ss_custom_js_frontend /////////////////////////////

add_action('wp_footer', 'ss_custom_js_frontend');

function ss_custom_js_frontend() {
	if (is_product()) {
		global $product;
		?>
			<script type="text/javascript">

                // DISABLE INPUT BOX WITH NO STOCK CHOICES
				jQuery(window).ajaxComplete(function() {
					setTimeout(function() {
						jQuery('.bulktablebody1 .zerostock input').attr('disabled', true)
						jQuery('.bulktablebody1 .zerostock input').attr('style', 'cursor: not-allowed;')
					}, 500)
				});

                //
				jQuery(document).ready(function() {

					jQuery("form#bulkvariationform .wc-pao-addons-container").remove();

                    // THE DISPLAYING OF THE CONTENT
					if (jQuery('form.variations_form.cart .wc-pao-addons-container').length > 0) {
						jQuery("<div class='wc-pao-addons-container'>" + jQuery('.wc-pao-addons-container').html() + "</div>").insertAfter('#bulkvariationform > table.variations1');
					}

					jQuery("ul.bulkorderradio li:first-child input").trigger('click');

					function qtyval() {
						var elems = [];
						jQuery(".bulktablebody1 input.input-text.qty.text").each(function() {
							var val = jQuery(this).val();
							if (val > 0) {
								var attr = jQuery(this).attr('data-id');
								var newarr = [attr, val]
								elems.push(newarr);
							}
						});
						if (elems.length != 0) {
							jQuery('#attribute_pa_sizes').val(JSON.stringify(elems));
						} else {
							jQuery('#attribute_pa_sizes').val("");
						}
					}

					jQuery('.bulktablebody1 input').click(function() {
						if (jQuery(this).val() < 1) {
							jQuery(this).val('');
						}
					});

					jQuery("button.bulksubmit").click(function(e) {

						// 			other code change price end from here
						var error = false;

						qtyval();

						var checkt = jQuery("input#attribute_pa_sizes").val();

						if (checkt == "") {
							error = true;
						}

						if (error) {
							event.preventDefault();
							alert('Some fields are empty or not selected');
							return false;
						} else {
							return true;
						}
					});

				});
				
                // BULK ORDER RADIO INPUT CLICK FUNCTION
				jQuery('.bulkorderradio input').click(function() {
					let selectvalue = jQuery(this).val();

					jQuery.ajax({
						type: "post",
						dataType: "json",
						url: "<?= admin_url('admin-ajax.php') ?>",
						data: {
							action: "check_availabilty_stock",
							id: <?= $product->get_id(); ?>,
							selectvalue: selectvalue
						},
						beforeSend: function() {
							jQuery("form#bulkvariationform").attr("style", "opacity: 0.5");
						},
						success: function(response) {
							if (response) {
								jQuery(".bulktablebody1 input").attr('placeholder', "0");
								jQuery(".bulktablebody1 input").attr('max', "0");
							}
							let qtyobj = response[0];
							let imgobj = response[1];
							let image = imgobj.fullimage;
							jQuery(".outstock").remove();
							if (image != '') {
								jQuery('.woocommerce-product-gallery .woocommerce-product-gallery__image').attr('data-thumb', image);
								jQuery('.woocommerce-product-gallery img').attr('src', image);
								jQuery('.woocommerce-product-gallery a').attr('href', image);
								jQuery(".woocommerce-product-gallery img.wp-post-image.lazyautosizes.lazyloaded").attr('data-src', image).attr('srcset', image).attr('data-orig-src', image).attr('data-large_image', image).attr('data-srcset', image);
							}
							jQuery('.bulktablebody1 input').removeAttr('disabled')
							.removeAttr('style');
							jQuery("form#bulkvariationform").removeAttr("style");
							jQuery(".bulktablebody1 td").removeClass('AvailableAllTime');
							jQuery(".bulktablebody1 label").removeClass('AvailableAllTime');
							jQuery(".bulktablebody1 td").removeClass('zerostock');
							jQuery('.bulktablebody1 label').addClass('zerostock');

							jQuery.each(qtyobj, function(index, value) {
								jQuery('.bulktablebody1 input').each(function() {
									const getdataid = jQuery(this).data('id');
									if (index == getdataid) {
										jQuery(this).parent().removeClass('zerostock');
										jQuery(this).parent().addClass('AvailableAllTime');
									}
								});

								if (qtyobj && Object.keys(qtyobj).length > 0 ) {
									jQuery(".bulktablebody1 input[data-id='" + index + "']").attr('max', value);
								}

							});
						}
					})

				})

			</script>
		<?php
	}
}











?>