<?php

add_action('add_meta_boxes', 'create_custom_meta_box');
if (!function_exists('create_custom_meta_box')) {
    function create_custom_meta_box()
    {
        add_meta_box(
            'custom_product_meta_box',
            __('Quantity Base Discount Settings', 'cmb'),
            'add_custom_content_meta_box',
            'product',
            'normal',
            'default'
        );
    }
}
if (!function_exists('add_custom_content_meta_box')) {
    function add_custom_content_meta_box()
    {
        global $post;

?>
        <div id="general_product_data" class="panel woocommerce_options_panel" style="">
            <div class="options_group show_if_simple show_if_external show_if_variable" style="">
                <p class="form-field">
                    <label for="product_qty">Product QTY</label>
                    <input type="number" name="product_qty" id="product-qty">
                </p>
                <p class="form-field">
                    <label for="discount_percentage">Discount Percentage</label>
                    <input type="number" name="discount_percentage" id="discount-percentage">%
                </p>
                <!-- <p class="form-field">
                    <label for="product_price">Product Price</label>
                    <input type="number" name="product_price" id="product-price" value="">
                </p> -->
                <button type="button" class="button  button-primary add-discount-slab" id="add-discount-slab">Add Discount Slab</button>
            </div><br>
            <div class="options_group" style="">
                <table id="slab-container">
                    <tr>
                        <th>Quantity</th>
                        <th>Discount %</th>
                        <th>Delete</th>
                    </tr>

                    <?php
                    $all_bulk_slabs = get_post_meta($post->ID, 'ss_qty_discount_per_');
                    // echo "<pre>";
                    //  print_r($all_bulk_slabs);
                    if (!empty($all_bulk_slabs) && !empty($all_bulk_slabs['0'])) {
                        $bulk_sale_slabs = json_decode(get_post_meta($post->ID, 'ss_qty_discount_per_')['0']);
                        foreach ($bulk_sale_slabs as $key => $bulk_sale_slab) {
                            // echo "<style>th {display: revert;}</style>";
                            echo "<tr class='form-field bulk-purchase-slab'><td>" . $bulk_sale_slab['0'] . "</td><td>" . $bulk_sale_slab['1'] . "</td><td><svg class='delete_field' xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' viewBox='0 0 16 16'><path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z'></path><path fill-rule='evenodd' d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z'></path></svg></td></tr>";
                        }
                    }
                    ?>
                </table>
            </div>
            <input type="hidden" name="ss_qty_discount_per_" class="ss_qty_discount_per_" id="ss_qty_discount_per_">
        </div>
        <style>
            .delete_field {
                margin: 0 0 0 10px;
                cursor: pointer;
            }

            #slab-container th {
                display: none;
            }

            #slab-container td,
            #slab-container th {
                border: 1px solid #dddddd;
                text-align: left;
                padding: 8px;
            }
        </style>
        <script>
            jQuery(document).ready(function() {
                // console.log(jQuery('#ss_qty_discount_per_').val());
                cstm_arr_add_remove();
                jQuery("#add-discount-slab").click(function() {
                    var product_qty = jQuery("#product-qty").val();
                    var discount_percentage = jQuery("#discount-percentage").val();
                    //var product_price = jQuery("#product-price").val();
                    if (jQuery("#product-qty").val() && jQuery("#discount-percentage").val()) {
                        jQuery("#slab-container").append("<tr class='form-field bulk-purchase-slab'><td>" + product_qty + "</td><td>" + discount_percentage + "</td><td><svg class='delete_field' xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' viewBox='0 0 16 16'><path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z'></path><path fill-rule='evenodd' d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z'></path></svg></td></tr>");
                        jQuery("#custom_product_meta_box").css({
                            'border': '1px solid #c3c4c7'
                        });
                        jQuery("#product-qty").val("");
                        jQuery("#discount-percentage").val("");
                        jQuery("th").css({
                            'display': 'revert'
                        });
                        cstm_arr_add_remove();
                    } else {
                        alert('All fields are required in (Additional Product Information) meta box.');
                        jQuery("#custom_product_meta_box").css({
                            'border': '1px solid red'
                        });
                    }

                });

                // display table header <th>
                if (jQuery('.bulk-purchase-slab').length > 0) {
                    jQuery("th").css({
                        'display': 'revert'
                    });
                };

                // delete one by one
                jQuery("#slab-container").on("click", ".delete_field", function() {
                    jQuery(this).parent().parent().remove(); // remove parent of parent
                    cstm_arr_add_remove();
                    // console.log(jQuery('.bulk-purchase-slab').length);
                    if (jQuery('.bulk-purchase-slab').length < 1) {
                        jQuery("th").css({
                            'display': 'none'
                        });
                    };
                })
            });

            function cstm_arr_add_remove() {
                var elems = [];
                jQuery(".bulk-purchase-slab").each(function() {
                    var qty = jQuery(this).find('td:nth-child(1)').text();
                    var discount = jQuery(this).find('td:nth-child(2)').text();
                    if (qty.length > 0) {
                        var newarr = [qty, discount]
                        elems.push(newarr);
                    }
                });
                if (elems.length > 0) {
                    jQuery('#ss_qty_discount_per_').val(JSON.stringify(elems));
                } else {
                    jQuery('#ss_qty_discount_per_').val('');
                }

            }
        </script>

<?php
    }
}


// add_action('save_post', 'save_product_qty_base_discount');
add_action('woocommerce_process_product_meta', 'save_product_qty_base_discount');

function save_product_qty_base_discount($post_id){

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;

    if( isset( $_POST['ss_qty_discount_per_'] ) ) { 
        update_post_meta($post_id, 'ss_qty_discount_per_', $_POST['ss_qty_discount_per_']);
    }
}
