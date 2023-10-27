<?php

/////////////SHOR CUSTOM META INTO CART PAGE AFTER ITEM TITLE///////////////

// Add custom fields values under cart item name in cart
add_filter( 'woocommerce_cart_item_name', 'custom_cart_item_name', 10, 3 );
function custom_cart_item_name( $item_name, $cart_item, $cart_item_key ) {
    if( ! is_cart() )
        return $item_name;


        //  echo "<pre>";
        //  print_r($cart_item['wdm_name']);
        
        $custom_details = json_decode(stripslashes($cart_item['wdm_name']));

        //  echo "<pre>";
        //  print_r($custom_details);
        //  exit();

        // echo "<br>";

        $html = '';
        if (!empty($custom_details)) {

            $html .= "<table border='1' class='shop_table shop_table_responsive cts_crt_tab' style='font-size:12px;margin-top: 15px;'><tr style='height: auto; background-color: #333645;color: #fff;text-align: center;'><th>Location</th><th>Color</th></tr>";
            foreach ($custom_details as $key => $value) {

                $Location =  $value[0];
                $color = $value[1];
                $arr[$Location] = $color;
                $html .= "<tr style='height: auto;'>
                <th style='text-align: center;'>" . $Location . "</th>
                <th style='text-align: center;'>" . $color . "</th>
            </tr>";

                $item_data[] = array(
                    'key'   => $Location,
                    'value' => $color
                );
            }
            $html .= "</table>";
        }
        echo $html;

    // return $item_name;
}

// ///////////// SAVE CUSTOM DATA INTO DATABASE AFTER ORDER PLACING ///////////////

add_action('woocommerce_checkout_create_order_line_item', 'alie_wdm_add_custom_order_line_item_meta', 10, 4);

function alie_wdm_add_custom_order_line_item_meta($item, $cart_item_key, $values, $order)
{

    if (array_key_exists('wdm_name', $values)) {
        $item->add_meta_data('_wdm_name', $values['wdm_name']);
    }
}


// ////////////////// SHOW CUSTOM META INTO ADMIN ORDER PAGE////////////////////////

add_action('woocommerce_after_order_itemmeta', 'so_32457241_before_order_itemmeta', 10, 3);
function so_32457241_before_order_itemmeta($item_id, $item, $_product)
{

    $get_custom_field = wc_get_order_item_meta($item_id, '_wdm_name', true);

    $custom_details = json_decode(stripslashes($get_custom_field));

    $html = "<table border='1' class='ale_order_item_table' style='font-size:12px'><tr style='height: auto; background-color: #333645;color: #fff;text-align: center;'><th>Location</th><th>Color</th></tr>";

    if (!empty($custom_details)) {
        foreach ( $custom_details as $value1) {
            $Location = $value1[0];
            $color = $value1[1];
            $html .= "<tr style='height: auto;'>
                    <th style='text-align: center;'>" . $Location . "</th>
                    <th style='text-align: center;'>" . $color . "</th>
                </tr>";
            $item_data[] = array(
                'key'   => $Location,
                'value' => $color
            );
        }
    }
    

    $html .= "</table>";

    echo $html;
}

/////////////////////////DISCOUNT ON CART PAGE ////////////////////////////

function get_discount_by_qunat_and_plus_addons($obj, $deco, $index, int $cart_total, $boolen)
{
    
    if ($boolen) {
        // return $cart_total;
        foreach ($obj as $key => $value) {
            $qunt_key = explode("-", $key);
            $start_qunt = (int) $qunt_key[0];
            $end_qunt = (int) $qunt_key[1];
            // $cart_total;
            if ($cart_total >= $start_qunt && $cart_total <= $end_qunt) {
                return $obj[$key];
            }
        }
    }
    if ($deco == 'screen-print-3') {

        // print_r($index);
        $indexes = rtrim($index, ',');
        $addon_cost = 0;

        foreach ($obj as $key => $value) {
            $qunt_key = explode("-", $key);
            $start_qunt = (int) $qunt_key[0];
            $end_qunt = (int) $qunt_key[1];

            if ($cart_total >= $start_qunt && $cart_total <= $end_qunt) {

                foreach (explode(",",$indexes) as $key1 => $value1) {
                    $addon_cost += $obj[$key][$value1 - 1]; 
                }
                return $addon_cost;
            }
            
        }
    } elseif ($deco == 'embroidery-2') {
        foreach ($obj as $key => $value) {
            $qunt_key = explode("-", $key);
            $start_qunt = (int) $qunt_key[0];
            $end_qunt = (int) $qunt_key[1];
            if ($cart_total >= $start_qunt && $cart_total <= $end_qunt) {
                // echo $obj[$key][0] $index;
                return $obj[$key][0] * $index;
                // return $deco;
            }
        }
    }
} //function end

/////////////////////////////////woocommerce_cart_item_price/////////////////////////////////////

add_filter('woocommerce_cart_item_price', 'alie_wpd_show_regular_price_on_cart', 30, 3);
function alie_wpd_show_regular_price_on_cart($price, $values, $cart_item_key){

    global $woocommerce, $wpdb;
    $product_ID = intval($values["product_id"]);
    $variation_parent_id = intval($values["product_id"]);
    $item_qty =  $values["quantity"];
    $_product = $values['data'];
    $jam_decoration = $values['jam_decoration'];
    $ss_print_count = $values['ss_print_count'];
    
    $cart_total_qan = 0;
    $cart_total_qan1 = 0;

    $table_name = $wpdb->prefix . "ss_wc_product_discount";
    $embroidery_db_table = $wpdb->prefix . "ss_embroidery_cart_product";
    $table_screen_print = $wpdb->prefix . "ss_screen_print_cart_product";

    // $get_qty_base_dis_ = $wpdb->get_results("SELECT * FROM $table_name");
    $get_embroidery_pricing = $wpdb->get_results("SELECT * FROM $embroidery_db_table");
    $get_screenP_pricing = $wpdb->get_results("SELECT * FROM $table_screen_print");



    $quantity_base_discount = array();
    $embroidery = array();
    $screen_print = array();

    ///////////////////////////////////GET TAG BASE DISCOUNT CODE ST///////////////////////////////////////

    $product_tags = get_the_terms( $product_ID, 'product_tag' );
    foreach ($product_tags as $value) {
        $result =  $wpdb->get_row(  "SELECT * FROM $table_name WHERE tags_name LIKE '%" . $value->term_id ."%'" );
        if ($result ) {
            $unser_tag_obj = unserialize($result->tags_object);
            $minQTY_get = array_column($unser_tag_obj, 'minQTY');
            array_multisort($minQTY_get, SORT_ASC, $unser_tag_obj);
            break;
        }
    }

    foreach ($unser_tag_obj as $key => $value) {
         $quantity_base_discount[$value['minQTY'] . '-' . $value['maxQTY']] =  $value['discount'];
    }

    ///////////////////////////////////GET TAG BASE DISCOUNT CODE END ///////////////////////////////////////

    if (!empty($get_embroidery_pricing) && is_array($get_embroidery_pricing)) {
        foreach ($get_embroidery_pricing as $key => $value) {
            $embroidery[$value->ss_min_qty . '-' . $value->ss_max_qty] = array($value->ss_discount_in_per);
        }
    }
    if (!empty($get_screenP_pricing) && is_array($get_screenP_pricing)) {
        foreach ($get_screenP_pricing as $key => $value) {
            $screen_print[$value->ss_min_qty_field . '-' . $value->ss_max_qty_field] = explode(",", $value->ss_discount_in_per);
        }
    }
    
    // echo "<pre>";
    // print_r($quantity_base_discount);

    foreach ($woocommerce->cart->get_cart() as $key => $cart_item_val) {
        // var_dump($cart_item_val["product_id"]);
        if ($cart_item_val['jam_decoration'] == 'embroidery-2' && $variation_parent_id === $cart_item_val["product_id"]) {
            $cart_total_qan += intval($cart_item_val['quantity']);
        } 
        elseif ($cart_item_val['jam_decoration'] == 'screen-print-3' && $variation_parent_id === $cart_item_val["product_id"]) {
            $cart_total_qan1 += intval($cart_item_val['quantity']);
        }
    }
	
    
    if ($jam_decoration == 'embroidery-2') {
        $item_total_by_dec =  $cart_total_qan;
    } elseif ($jam_decoration == 'screen-print-3') {
        $item_total_by_dec = $cart_total_qan1;
    } else {
        $item_total_by_dec = intval($values['quantity']);
    }

    // print_r($embroidery);
    ///////////////////////////////////////////////////////////////////
    if (!empty($quantity_base_discount)) {
        $get_discount_val_base_on_qunty = get_discount_by_qunat_and_plus_addons($quantity_base_discount, $jam_decoration, $ss_print_count, $item_total_by_dec, true);
    }
    if (!empty($screen_print) && $jam_decoration == 'screen-print-3') {
        // get price for screen printing addos
        $get_addons_price_base_on_qunty = get_discount_by_qunat_and_plus_addons($screen_print, $jam_decoration, $ss_print_count, $item_total_by_dec, false);
    } elseif ( !empty($embroidery) && $jam_decoration == 'embroidery-2') {
        // get price for screen printing addos
        $get_addons_price_base_on_qunty = get_discount_by_qunat_and_plus_addons($embroidery, $jam_decoration, $ss_print_count, $item_total_by_dec, false);
        // echo $get_addons_price_base_on_qunty;
    } else {
        $get_addons_price_base_on_qunty = 0;
    }

        //   echo $get_discount_val_base_on_qunty.'discount<br>';
        //   echo $get_addons_price_base_on_qunty.'addons_price<br>';

    $product_reg_price = $_product->get_regular_price();
    // echo '<pre>';print_r($product_reg_price);echo '</pre>';

    $dis_price =  round($product_reg_price - ($product_reg_price * $get_discount_val_base_on_qunty / 100), 2) + $get_addons_price_base_on_qunty;

    $values['data']->set_price($dis_price);
    
    // echo "<pre>";
    // print_r($values);
    //  echo '<pre>';print_r($get_discount_val_base_on_qunty);echo '</pre>';
    //  echo '<pre>';print_r($get_addons_price_base_on_qunty);echo '</pre>';

    if ($get_discount_val_base_on_qunty && $get_addons_price_base_on_qunty ){
        $price_ = "$<del class='ss_del_cls'>" . $product_reg_price . "</del> "  . wc_price($dis_price);
    } else {
        $price_ = "$" .$product_reg_price;
    };
    // '<span class="wpd-discount-price" style="text-decoration: line-through; opacity: 0.5; padding-right: 5px;">' . $product_reg_price . '</span>' .  wc_price( $dis_price );


    return $price_;

}
/////////////////////////////////////

// Customizing cart item price subtotal
add_action( 'woocommerce_before_calculate_totals', 'set_cart_item_calculated_price', 10, 1 );
function set_cart_item_calculated_price( $cart_values ) {
 
    
    foreach ( $cart_values->get_cart() as $values ) {
        global $woocommerce, $wpdb;
        $product_ID = intval($values["product_id"]);
        $variation_parent_id = intval($values["product_id"]);
        $item_qty =  $values["quantity"];
        $_product = $values['data'];
        $jam_decoration = $values['jam_decoration'];
        $ss_print_count = $values['ss_print_count'];
        
        $cart_total_qan = 0;
        $cart_total_qan1 = 0;
    
        $table_name = $wpdb->prefix . "ss_wc_product_discount";
        $embroidery_db_table = $wpdb->prefix . "ss_embroidery_cart_product";
        $table_screen_print = $wpdb->prefix . "ss_screen_print_cart_product";
    
        // $get_qty_base_dis_ = $wpdb->get_results("SELECT * FROM $table_name");
        $get_embroidery_pricing = $wpdb->get_results("SELECT * FROM $embroidery_db_table");
        $get_screenP_pricing = $wpdb->get_results("SELECT * FROM $table_screen_print");
    
    
    
        $quantity_base_discount = array();
        $embroidery = array();
        $screen_print = array();
    
        ///////////////////////////////////GET TAG BASE DISCOUNT CODE ST///////////////////////////////////////
    
        $product_tags = get_the_terms( $product_ID, 'product_tag' );
        foreach ($product_tags as $value) {
            $result =  $wpdb->get_row(  "SELECT * FROM $table_name WHERE tags_name LIKE '%" . $value->term_id ."%'" );
            if ($result ) {
                $unser_tag_obj = unserialize($result->tags_object);
                $minQTY_get = array_column($unser_tag_obj, 'minQTY');
                array_multisort($minQTY_get, SORT_ASC, $unser_tag_obj);
                break;
            }
        }
    
        foreach ($unser_tag_obj as $key => $value) {
             $quantity_base_discount[$value['minQTY'] . '-' . $value['maxQTY']] =  $value['discount'];
        }
    
        ///////////////////////////////////GET TAG BASE DISCOUNT CODE END ///////////////////////////////////////
    
        if (!empty($get_embroidery_pricing) && is_array($get_embroidery_pricing)) {
            foreach ($get_embroidery_pricing as $key => $value) {
                $embroidery[$value->ss_min_qty . '-' . $value->ss_max_qty] = array($value->ss_discount_in_per);
            }
        }
        if (!empty($get_screenP_pricing) && is_array($get_screenP_pricing)) {
            foreach ($get_screenP_pricing as $key => $value) {
                $screen_print[$value->ss_min_qty_field . '-' . $value->ss_max_qty_field] = explode(",", $value->ss_discount_in_per);
            }
        }
        
        // echo "<pre>";
        // print_r($quantity_base_discount);
    
        foreach ($woocommerce->cart->get_cart() as $key => $cart_item_val) {
            $quantity = $cart_item_val['quantity'];
            $carttotqty += $quantity;

            // var_dump($cart_item_val["product_id"]);
            if ($cart_item_val['jam_decoration'] == 'embroidery-2' && $variation_parent_id === $cart_item_val["product_id"]) {
                $cart_total_qan += intval($cart_item_val['quantity']);
            } 
            elseif ($cart_item_val['jam_decoration'] == 'screen-print-3' && $variation_parent_id === $cart_item_val["product_id"]) {
                $cart_total_qan1 += intval($cart_item_val['quantity']);
            }
        }
        





        // MINIMUMCHECK HERE
        // if ($cart_total_qan > 0 && $cart_total_qan < 12 || $cart_total_qan1 > 0 && $cart_total_qan1 < 12)
        //     $product_less_then_12 = 1;
        




        if ($jam_decoration == 'embroidery-2') {
            $item_total_by_dec =  $cart_total_qan;
        } elseif ($jam_decoration == 'screen-print-3') {
            $item_total_by_dec = $cart_total_qan1;
        } else {
            $item_total_by_dec = intval($carttotqty);
        }
    
        // print_r($embroidery);
        ///////////////////////////////////////////////////////////////////
        if (!empty($quantity_base_discount)) {
            $get_discount_val_base_on_qunty = get_discount_by_qunat_and_plus_addons($quantity_base_discount, $jam_decoration, $ss_print_count, $item_total_by_dec, true);
        }
        if (!empty($screen_print) && $jam_decoration == 'screen-print-3') {
            // get price for screen printing addos
            $get_addons_price_base_on_qunty = get_discount_by_qunat_and_plus_addons($screen_print, $jam_decoration, $ss_print_count, $item_total_by_dec, false);
        } elseif ( !empty($embroidery) && $jam_decoration == 'embroidery-2') {
            // get price for screen printing addos
            $get_addons_price_base_on_qunty = get_discount_by_qunat_and_plus_addons($embroidery, $jam_decoration, $ss_print_count, $item_total_by_dec, false);
        } else {
            $get_addons_price_base_on_qunty = 0;
        }
    
            //   echo $get_discount_val_base_on_qunty.'discount<br>';
            //   echo $get_addons_price_base_on_qunty.'addons_price<br>';
    
        $product_reg_price = $_product->get_regular_price();
    
        $dis_price =  round($product_reg_price - ($product_reg_price * $get_discount_val_base_on_qunty / 100), 2) + $get_addons_price_base_on_qunty;
    
        $values['data']->set_price($dis_price);
       
    
    }
	 if ($product_less_then_12) echo "<script>alert('No discount will be applicable if the QTY sum of a product is less then 12.'); </script>";
}

//////////////////////////////////////////////////////////////////////////

add_filter( 'woocommerce_cart_item_name', 'customizing_cart_item_data', 10, 3);
function customizing_cart_item_data( $item_name, $cart_item, $cart_item_key ) {

    $variationId = $cart_item['variation_id'];
    $variation = new WC_Product_Variation($variationId);
    $variationName = implode(" / ", $variation->get_variation_attributes());
    $variationName;    foreach( $terms as $term ) $term_names[] = $term->name;

        $item_name = '<p class="ctm_attributes_val" style="margin:12px 0 0; font-size: .875em;">
            <span id="ctm_attributes_val" >' .get_the_title( $cart_item['product_id'] ) . "<br> ".  $variationName. '</span>
        </p>';
    
    return $item_name;
}

add_action('woocommerce_before_checkout_form', 'my_custom_message');
function my_custom_message() {
       wc_print_notice( __('If the product quantity is less than 12 no discount will be applicated.'), 'notice' );
}

