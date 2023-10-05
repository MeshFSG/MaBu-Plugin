<?php
/*
Plugin Name: Bulk Quantity & Wc Custom Addons
Plugin URI: https://seositesoft.com/
Description: This Plugin Send's multiple orders and quantity also have Wc addon functionalities like Pustomise Design as Per Customer Need
Author: Seositesoft
Version: 4.2.7
*/

register_activation_hook(__FILE__, 'ss_create_table_custom'); //admin/settings/qty-base-discount.php
//  register_activation_hook(__FILE__, 'activate'); //admin/settings/qty-base-discount.php
register_activation_hook(__FILE__, 'create_imprint_loc_color_attribute_taxonomies');

///////////////////////////////////////////////////////////////////////////
//
register_deactivation_hook( __FILE__, 'ss_pluginprefix_deactivate' );

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
	/////////
	     function call_back_func_for_geting_val_colors_att($value,$key)  {
      wp_insert_term( $value, 'pa_print-colors' );
    }
       $colors_Arr = array("White","Ivory ( 9200C )","Black", "	Black Chrome (B7C)","Hi-Vis Orange (811C)", "Orange (164C)", "Light Orange (158C)");
        array_walk($colors_Arr,"call_back_func_for_geting_val_colors_att");
		
		////
      add_option('run_only_once_01', 1); 
  endif;
}
add_action( 'init', 'scratchcode_run_code_one_time' );

/////////////////////////// ENQUEE STYLES AND SCRIPTS FOR FRONETEND /////////////////////////////

function alie_ctm_enqueue_script_for_clientSite()
{
	wp_enqueue_style('ss_custom_frontend_style_', plugin_dir_url(__FILE__) . 'assets/css/style.css');
	wp_enqueue_style('select2-stle', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
	wp_enqueue_script('select2_js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'));
}
add_action('wp_enqueue_scripts', 'alie_ctm_enqueue_script_for_clientSite', 99);

/////////////////////////// ENQUEE STYLES AND SCRIPTS FOR ADMIN /////////////////////////////

function alie_ctm_enqueue_script_for_adminSite()
{
	wp_enqueue_style('ss_custom_admin_style_', plugin_dir_url(__FILE__) . 'assets/css/admin-style.css');
	wp_enqueue_style('select2-stle', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
	wp_enqueue_script('select2_js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'));
	wp_enqueue_script('ss-mycustom', plugin_dir_url(__FILE__) . '/assets/js/mycustom.js', array('jquery', 'select2_js'));
}
add_action('admin_enqueue_scripts', 'alie_ctm_enqueue_script_for_adminSite', 99);

function get_min_max ($ss_col, $ss_table_name) {
    global $wpdb;
    return $wpdb->get_row("SELECT MIN(CAST(".$ss_col." AS int)) AS ss_min, MAX(CAST(".$ss_col." AS int)) AS ss_max FROM " . $ss_table_name);
}

/////////////////////////// INCLUDE EXTERNAL FILES /////////////////////////////

include(plugin_dir_path(__FILE__) . '/assets/wcMetas/custm_meta_boxes.php');
include(plugin_dir_path(__FILE__) . '/assets/inc/function_file_code.php');
// include(plugin_dir_path(__FILE__) . '/assets/inc/product_base_discount_meta_box.php');
include(plugin_dir_path(__FILE__) . '/assets/settingPages/qty-base-discount.php');
include(plugin_dir_path(__FILE__) . '/assets/inc/product_page_js.php');
include(plugin_dir_path(__FILE__) . '/assets/inc/cart-page.php');
include(plugin_dir_path(__FILE__) . '/assets/inc/customJs.php');

/////////////////////////// CREAYE DATABASE FUNCITON /////////////////////////////
if (!defined('ABSPATH')) {
	exit;
}
// add_action('wp_head', 'bulkcss');
// function bulkcss()
// {
// 	global $product;
// 	if (is_product()) {
// 	}
// }
add_action('wp_footer', 'ss_custom_js_frontend');
function ss_custom_js_frontend()
{
	if (is_product()) {
		global $product;
?>
<script type="text/javascript">
	jQuery(window).ajaxComplete(function() {
		setTimeout(function() {
			jQuery('.bulktablebody1 .zerostock input').attr('disabled', true)
			jQuery('.bulktablebody1 .zerostock input').attr('style', 'cursor: not-allowed;')
		}, 500)

	});
	jQuery(document).ready(function() {
		jQuery("form#bulkvariationform .wc-pao-addons-container").remove();
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
			// alert();
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
	}); //ready function end here



	jQuery('.bulkorderradio input').click(function() {
		// alert();
		// jQuery(".value1 input").val(0);
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
// 				 console.log(response);
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
				jQuery(".bulktablebody1 td.value1").removeClass('AvailableAllTime');
				jQuery(".bulktablebody1 td.value1 label").removeClass('AvailableAllTime');


				jQuery(".bulktablebody1 td").removeClass('zerostock');
				jQuery('.bulktablebody1 label').addClass('zerostock');

					 
				jQuery.each(qtyobj, function(index, value) {
					
					jQuery('.bulktablebody1 input').each(function() {
						const getdataid = jQuery(this).data('id');
						if (index == getdataid && value > 0 ) {
							jQuery(this).parent().removeClass('zerostock');
							jQuery(this).parents('td').addClass('AvailableAllTime');
							jQuery(this).parent().addClass('AvailableAllTime');
						}
					});

					if (qtyobj && Object.keys(qtyobj).length > 0 ) {
						jQuery(".bulktablebody1 input[data-id='" + index + "']").attr('max', value);
						// jQuery(".bulktablebody1 input[data-id='" + index + "']").attr('placeholder', value);

						// jQuery(".bulktablebody1 input[data-id='" + index + "']").parent().addClass('AvailableAllTime');
						// jQuery(".bulktablebody1 input[data-id='" + index + "']").show();
						// jQuery(".bulktablebody1 input[data-id='" + index + "'] + .outstock").remove();
						// if (value == 0) {
						// 	jQuery(".bulktablebody1 input[data-id='" + index + "']").parent().addClass('zerostock');
						// 	jQuery(".bulktablebody1 input[data-id='" + index + "']").attr('max', value);jQuery(".bulktablebody1 input[data-id='" + index + "']").attr('placeholder', value);
						// 	jQuery(".bulktablebody1 input[data-id='" + index + "']").after("<span class='outstock'>Out of stock</span>");
						// 	jQuery(".bulktablebody1 input[data-id='" + index + "']").hide();
						// } else {
						// 	jQuery(".bulktablebody1 input[data-id='" + index + "']").attr('max', value);
						// 	jQuery(".bulktablebody1 input[data-id='" + index + "']").attr('placeholder', value);
// 							jQuery(".bulktablebody1 input[data-id='" + index + "']").parents('td').addClass('AvailableAllTime');
						// 	jQuery(".bulktablebody1 input[data-id='" + index + "']").show();
						// 	jQuery(".bulktablebody1 input[data-id='" + index + "'] + .outstock").remove();
						// }
					}
					
					// else {
					// 	console.log("else");
					// 	jQuery(".bulktablebody1 input").attr('placeholder', "0");
						
					// 	// jQuery(".bulktablebody1 input[data-id='" + index + "']").parent().addClass('AvailableAllTime');
					// }
				});
			}
		})
	})
</script>
<?php
	} //isproduct page validation
}


add_action('wp_ajax_check_availabilty_stock', 'check_availabilty_stock');
add_action('wp_ajax_nopriv_check_availabilty_stock', 'check_availabilty_stock');
function check_availabilty_stock()
{
	global $product;
	$second_loop_stoped = false;
	$proid =  $_POST['id'];
	$selectvaue = $_POST['selectvalue'];
	$vailabilityarr = array();
	$fullimage = array();
	$product = wc_get_product($proid);
	// Get available product variations
	$product_variations = $product->get_available_variations();
	foreach ($product_variations as $key1 => $variation) {
		$variation_id = $variation['variation_id'];
		$variation_obj = new WC_Product_Variation($variation_id);
		$variation_obj->get_stock_status();
		$stock_status = $variation_obj->get_stock_status();
		if ($stock_status == 'instock') {
			$stock_qty = $variation_obj->get_stock_quantity() ? $variation_obj->get_stock_quantity() : 99999;
		} else {
			$stock_qty = 0;
		}
		// The attributes taxonomy key and slug value for this variation
		$attributes_arr = $variation_obj->get_attributes();

		foreach ($attributes_arr as $key => $value) {
			if ($attributes_arr[$key] == $selectvaue) {
				$fullimage['fullimage'] = $product_variations[$key1]['image']['url'];
				$matchkey = array();
				$getsizekey = array_filter(array_keys($attributes_arr), function ($var) {
					if (str_contains(strtolower($var), 'size')) {
						return $var;//return array key
					}
				});
				$reindexarr = array_values($getsizekey);
				$size =  $attributes_arr[$reindexarr[0]];
				$vailabilityarr[$size] = $stock_qty;
			}
		} //innser loop
	} //outer loop
	$array = array($vailabilityarr, $fullimage);
	echo json_encode($array);
	die();
}


add_action('woocommerce_before_add_to_cart_form', 'add_bulk_order_button', 1);
function add_bulk_order_button()
{
	global $product, $post;
	if (is_product() && $product->is_type('variable')) :
	$attr =   $product->get_variation_attributes();
	$product_variations = $product->get_available_variations();
	$checkVariationAvailable = count($product_variations);

	if ($checkVariationAvailable > 0) {
?>
<div id='bulkorderformshow'>
	<form id='bulkvariationform' action='' method='post' enctype='multipart/form-data'>
		<input type='hidden' id='attribute_pa_sizes' name='attribute_pa_sizes[]' value=''>
		<input type='hidden' name='proid' value='<?= $product->id; ?>'>
		<table class='variations1' width='100%' cellspacing='0'>
			<tbody class='bulktablebody'>
				<?php echo do_action('Show_Discount_On_Product_Page'); ?>
				<tr>
					<?php
									   $abcd =  $product->get_attributes();

									   foreach ($abcd as $key => $value) {
										   $getData = $abcd[$key]->get_data();
										   $attrName = $getData['name'];
										   sort($getData['options']);
										   $attrIds = $getData['options'];
										   if (str_contains($key, 'size')) {
					?>
					<td class="label bulkorder-size-attr">
						<label><?= str_replace('pa_', '', $key); ?></label>

						<table class='variations1' cellspacing='0'>
							<tbody class='bulktablebody1'>
								<?php
											   //for quantity
											   $table .= '<tr>';
											   foreach ($attrIds as $singleId) {
												
												   $check_if_its_global_or_created = is_integer($singleId);
												   if ($check_if_its_global_or_created) {
													   $obj = get_term_by('id', $singleId, $key);
													//    echo "<pre>";
													//   print_r($obj);
													   $termslug = $obj->slug;
													   $termname = $obj->name;   
												   } else {
													   $termslug = $termname = $singleId;
												   }
								?>
								<td class="value1">
									<label for='<?= trim($termslug); ?>'><strong><?= $termname; ?></strong>
										<input type="number" class="input-text qty text" id="<?= trim($termslug); ?>" data-id="<?= trim($termslug); ?>" step="1" min="0" max="" name="bulk_ord_quantity[]" value="" title="Qty" size="4" placeholder="" inputmode="numeric">
									</label>
								</td>
								<?php } ?>
							</tr>
		</table>
			</td>
		<?php

			} elseif (str_contains($key, 'color')) {
		?>
		<td class="label bulkorder-colors-variation"><label><?= str_replace('pa_', '', $key); ?></label>
			<ul class="bulkorderradio">
				<?php
											   foreach ($attrIds as $singleId) {
												   $obj = get_term_by('id', $singleId, $key);
												   $check_if_its_global_or_created =  is_integer($singleId);
												   if ($check_if_its_global_or_created) {
													   $termname = $obj->name;
													   $termslug = $obj->slug;
													   // $meta = get_term_meta($singleId);
								$meta = get_term_meta($singleId, 'product_attribute_color', true);
 							//	$getcolorobj = unserialize($meta['product_attribute_color'][1]);
							//					$color = $getcolorobj['product_attribute_color'];
								$color = $meta;

													  						  					
													   

													   

// 														   $getcolorobj;
												   } else {
													   $singleId = $color = $termslug = $termname = $singleId;		   

												   }
				?>
				
				
				
				<li>
					<input type="radio" name="bulk_ord_attribute_pa_color" data-code="<?= $color; ?>" id="bulkcolore<?= $singleId; ?>" value="<?= $termslug; ?>" />
					<label for="bulkcolore<?= $singleId; ?>">
						<span class="colorspan" style="background-color:<?= $color; ?>"></span>
						<span class="tooltiptext"><?= $termname; ?></span>
					</label>
				</li>
				<?php
											   }
				?>
			</ul>
			<?php
											
											   $table .= '</td>';
										   } else { //else if end here
			?>
		<td class="label"><label><?= str_replace('pa_', '', $key); ?></label><select id="<?= $key; ?>" class="value" class="" name="bulk_ord_attribute_<?= $key; ?>" data-attribute_name="attribute_<?= $key; ?>" data-show_option_none="yes" style="display: block;">
			<?php
											   foreach ($attrIds as $elsesingleId) {
												   $obj3 = get_term_by('id', $elsesingleId, $key);
												   echo $termname = $obj3->name;
												   $termslug = $obj3->slug;
												   
					?>							   
												   

			
			<option value="<?= $termslug; ?>" class="attached enabled"><?= $termname; ?></option>
			<?php
											   }
	
			?>
			</select></td>
		<?php
										   }
									   } //outer foreach loop
		?>
		</tr>
	</tbody>
</table>

<div class="decshowoption">
	<?php
									   do_action('showdec');
	?>
</div>
	


<div class="bulk_variation_wrap">
	<div id="dec_opt_div">
		

	<?php
									   do_action('sss_jam_custom_html');
	?>
	
	</div>
	
	<button type="submit" class="bulk_add_to_cart_button button alt bulksubmit" name="bulksubmit"><?= __('Add to cart', 'sss_jam'); ?></button>
</div>
</form>
</div>
<?php
									   // echo "<pre>";
									   echo $table;
									  } else { //check variation available end here
		echo '<p class="stock out-of-stock">This product is currently out of stock and unavailable.</p>';
	} //check variation available end here

	endif; //is_product

}

/**
 * Find matching product variation
 *
 * @param $product_id
 * @param $attributes
 * @return int
 */
function find_matching_product_variation_id($product_id, $attributes)
{
	$loadpro =  WC_Data_Store::load('product');
	return  $loadpro->find_matching_product_variation(new \WC_Product($product_id), $attributes);
}

function sss_jam_addtocart_message($count)
{
	// Output success messages
	if (get_option('woocommerce_cart_redirect_after_add') == 'yes') :
	$return_to = (wp_get_referer()) ? wp_get_referer() : home_url();
	$message   = sprintf('<a href="%s" class="button">%s</a> %s', $return_to, _('Continue Shopping &rarr;', 'woocommerce-bulk-variations'), sprintf(_('%s products successfully added to your cart.', 'woocommerce-bulk-variations'), $count));
	else :
	$message = $count . ' products successfully added to your cart <a href="' . get_permalink(wc_get_page_id('cart')) . '" class="button">View cart</a>';
	endif;

	wc_add_notice($message, 'success');
}
add_action('wp_loaded', 'sss_jam_bulk_OrderQuantity', 99);
function sss_jam_bulk_OrderQuantity()
{
	if (isset($_POST['bulksubmit'])) {
		$added_count  = 0;
		$subtract_count  = 0;

		$selectedvariation = json_decode(stripslashes($_POST['attribute_pa_sizes'][0]));
		$color = str_replace(' ', '-', trim($_POST['bulk_ord_attribute_pa_color']));


		$error = false;
		$productId = $_POST['proid'];
		$productobj = wc_get_product($productId);

		$product_variations = $productobj->get_variation_attributes();

		foreach ($selectedvariation as $selectedvariationkey => $selectedvariationvalue) {
			$selectVariationKey = str_replace(' ', '-', trim($selectedvariation[$selectedvariationkey][0]));
			$qty = trim($selectedvariation[$selectedvariationkey][1]);
			$varitionarr = array();

			foreach ($product_variations as $key => $value) {
				echo $key;
				if (str_contains( strtolower($key), 'size')) {
					$varitionarr['attribute_' . $key] = $selectVariationKey;
				} elseif (str_contains( strtolower($key), 'color')) {
					$varitionarr['attribute_' . $key] = $color;
				} else {
					$varitionarr['attribute_' . $key] = $_POST['bulk_ord_attribute_' . $key];
				}
			} // inner foreach loop

			$getvarid = find_matching_product_variation_id(
				$productId,
				$varitionarr
			);



			if ($getvarid) {

				$passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $productId, $qty, $getvarid, $varitionarr);


				if ($passed_validation) {
					$added = WC()->cart->add_to_cart($productId, $qty, $getvarid, $varitionarr);

					if ($added) {
						$added_count++;
					} else {
						$subtract_count++;
					}
				} //pass validation
				else {
					$error = true;
				}
			} //get varidation id
			else {
				$error = true;
			} //get variation id else end here
		} //outer loop foreach
		// 		$url = site_url()."/product/".$productobj->get_slug();
		$url1 = site_url() . "/product/" . $productobj->get_slug();
		if ($error) {
			wp_safe_redirect($url1);
			exit;
		} else {
			if ($added_count) {
				sss_jam_addtocart_message($added_count);
				wp_safe_redirect($url1);
				exit;
			}
		} //if ot error else start from here
	} // isset submit
}
