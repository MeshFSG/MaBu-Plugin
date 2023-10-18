<?php
    ////////////////CUSTOM HOOK USE FOR ENQUEE CODE////////////////////////

    add_action("sss_jam_custom_html", "ale_before_add_to_cart_btn");

    function ale_before_add_to_cart_btn()
    {
		global $wpdb;
    	$table_screen_print = $wpdb->prefix . "ss_screen_print_cart_product";

        ////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////// SENDS USER BACK IF THE PRODUCT ISNT A VARIABLE //////////////////////////
        if (!is_product() && $product->get_type() != "variable") {
            return;
        }

        //============================================================================//
        //============================================================================//
            global $wpdb,$product;
            $post_id = $product->get_id();
            $product_tags = $product->get_tag_ids();
            $table_name = $wpdb->prefix . "ss_gloablly_decoration_settings";
        //============================================================================//
        //============================================================================//
            foreach ($product_tags as $value) {
                $result =  $wpdb->get_row(  "SELECT * FROM $table_name WHERE tags_name LIKE '%" . $value ."%'" );
                if ($result ) {
                    $unser_tag_obj = unserialize($result->tags_object);
                    break;
                }
            }
        //============================================================================//
        //============================================================================//
            $get_deco_type = $unser_tag_obj[0]['pa_imprint_type'];
            $imprint_location = $unser_tag_obj[0]['pa_imprint_location'];
            $imprint_color = $unser_tag_obj[0]['pa_imprint_color'];
            $product_data = wc_get_product($post_id);
            $get_price = $product_data->get_price();
        //============================================================================//
        //============================================================================//

        //////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////// We do not currently support grouped or external products //////////////////////////
        if (
            "grouped" === $product->get_type() ||
            "external" === $product->get_type()
        ) {
            return;
        }
        if (empty($get_deco_type)) {
            return;
        }
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////// DISPLAYS THE DECORATION OPTIONS ON THE PRODUCT PAGE //////////////////////////
        if (!empty($get_deco_type)) {
            echo "<div class='ctm-select-decoration-wrapper'>
            <label>Decoration * </label>
            <select class='ctm-select-decoration' id='ctm-select-decoration'>";

            if ( has_term( 'SDNS', 'product_tag') ) {
                echo "<option value='blank'> Blank </option>"
            }

            foreach ($get_deco_type as $key => $value) {
                echo "<option value=" .
                    $value .
                    ">" .
                    str_replace("-", " ", $value) .
                    "</option>";
            }
            echo "</select></div>";
        }

        if (empty($get_deco_type)) {
            return;
        }

        ?>
        
        <!-- THE LOCATION OF THE ON PRODUCT DISPLAYED STUFF -->

        <div class="ale_wrap_container" id="ale_wrap_container">
            <div class="ale_field_wrapper23">
                <p class="location_names">Location</p>
                <p class="ale_clr_names">Type</p>
            </div>

            <!-- ======================== -->
            <!-- PRODUCT DETAILS LOCATION -->
            <div class="field_wrapper">

                <div class="ale_field_wrapper"> 
                    <input type='hidden' id='ale_pro_price' name='ale_pro_price' value='<?php echo $get_price; ?>'>
                    <input type='hidden' id='ale_combo' name='combo' value=''>
                    <input type='hidden' id='ss_print_count' name='ss_print_count' value=''>
                    <input type='hidden' id='jam_decoration' name='jam_decoration' value=''>

                    <div class="ale_loc_wraper" id="ale_loc_wraper">
                        <select name="ale_location_field_" class="ale_location_field">
                            <option value="">Select Location</option>
                            <?php foreach ( $imprint_location as $key => $value ) {
                                $term_name_by_id = get_term( intval($value), "pa_imprint-location" )->name;
                                echo '<option value="' . $term_name_by_id . '" >' . $term_name_by_id . "</option>";
                            } ?>
                        </select>
                    </div>

                    <div class="ale_color_wraper" id="ale_color_wraper">
                        <select name="ale_color_field_" class="ale_color_field">
                            <option value="">Amount of Colors</option>
                            <?php
								$max_color_amount = $wpdb->get_row("SELECT MAX(ss_amountof_color) AS max_color_amount FROM " . $table_screen_print)->max_color_amount;
                                while ($i++ < $max_color_amount) echo '<option value="' . $i . '" >' . $i . "</option>";
							?>
                        </select>
                    </div>

                    <a href="javascript:void(0);" class="add_button" style="background:#2CC64D;color:white;" title="Confirm Location">+ Add</a>
                </div>

            </div>


        </div>

        <!-- ///// -->
        <div class='ale_options_group' style="display: none;">
            <select id="ale_none_field" class="ale_color_field" name="ale_none_field[]" multiple="multiple">

                <?php foreach ($imprint_color as $key => $value) {
                    $meta = get_term_meta(intval($value));
                    $getcolorobj = unserialize($meta["_fusion"][0]);
                    $color = $getcolorobj["attribute_color"];
                    $get_term_name_by = get_term(
                        intval($value),
                        "pa_print-colors"
                    )->name;
                    echo "<option value=" .
                        $get_term_name_by .
                        " data-code=" .
                        $color .
                        ">" .
                        $get_term_name_by .
                        "</option>";
                } ?>

            </select>
        </div>
        <!-- /// -->

        <?php
    }




    //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////// ADD CUSTOM META FROM SINGLE PRODUCT PAGE //////////////////////////
        add_filter("woocommerce_add_cart_item_data", "wdm_add_item_data", 10, 3);

        function wdm_add_item_data($cart_item_data, $product_id, $variation_id) {
            $cart_item_data["wdm_name"] = $_POST["combo"];
            $cart_item_data["ss_print_count"] = $_POST["ss_print_count"];
            $cart_item_data["jam_decoration"] = $_POST["jam_decoration"];
            return $cart_item_data;
        }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////// CUSTOM JS //////////////////////////
    add_action("wp_footer", "ale_custom_js_func", 100);

    function ale_custom_js_func() {
        if (!is_product()) {
            return;
        } ?>
        
        <script type="text/javascript">

            ///////////////////////////////////////////////////////////////////////
            ///////////// CART STUFF ????? ////////////////////////////////////////
            function cstm_arr_add_remove() {
                var elems = [];
                jQuery(".ale_field_wrapper1").each(function() {
                    var val1 = jQuery(this).find('p:nth-child(1)').text();
                    var val2 = jQuery(this).find('p:nth-child(2)').text();
                    if (val1.length > 0) {
                        var newarr = [val1, val2]
                        elems.push(newarr);
                    }
                });
                jQuery('#ale_combo').val(JSON.stringify(elems));
            }

            /////////////////////////////////////////////////////////////////////////////////
            /////////////////////////////// select2append ///////////////////////////////////
            function AddColors(state) {
                if (!state.id) {
                    return state.text;
                }
                var colorId = state.id;
                var colorText = state.text;
                var colorcode = jQuery('option[value^="' + colorId + '"]').attr('data-code')
                var color = (colorcode) ? colorcode : 'transparent';
                var $state = jQuery(
                    '<span><span style="width: 20px;height: 20px; display: inline-block;background-color:' + color + '"></span>  ' + state.text + '</span>'
                );

                return $state;
            };

            function select2append() {
                jQuery('select#ale_none_field').select2({
                    templateResult: AddColors
                });
            }

            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            function decoration_based_appned_filed_and_check() {

                var getlocvalue = jQuery(".ale_location_field").val();
                var getcolorvalue = jQuery(".ale_color_field").val();
                var dec_val = jQuery('#ctm-select-decoration').val();

                jQuery('.ale_color_field').val(null).trigger('change');

                let maxField_product_sum = countMaxfields();
                let maxField = maxField_product_sum[0];
                let product_sum = maxField_product_sum[1];

                ///////// variation product count each loop end ////////////
                // THE NOTIFICATION FOR HAVING NOTHING IN YOUR SELECTION BEFORE CLICKING ADD
                var colorsswatches;

                if (product_sum == 0) {
                    alert('please fill quantity fields');
                    return;
                }

                if (typeof getcolorvalue == 'object') {
                    var colorsswatches = getcolorvalue.join(", ").replace(/-/g, ' ')
                } else {
                    var colorsswatches = getcolorvalue;
                }

                ////////////////////////////////////////////////////////////////////////
                var wrapper = jQuery('.field_wrapper'); //Input field wrapper

                ////////////////////////////////////////////////////////////////////////
                ///////// THE ADDITION OF THE NEW LOCATION THAT IS SELECTED //////////
                var fieldHTML = '<div class="ale_field_wrapper1" data-deco="' + dec_val + '">' +
                    '<p class="location_name" data-attr=' + getlocvalue + '>' + getlocvalue.replace('-', ' ') + " <span class='labelbadge'>" + jQuery('#ctm-select-decoration').children(':selected').text() + "</span>" + '</p>' +
                    '<p class="ale_clr_name" data-attr=' + getcolorvalue + '>' + colorsswatches + '</p>' +
                    '<a href="javascript:void(0);" class="remove_button" title="Add field">Remove</a></div>';
                
                //Check maximum number of input fields

                if (dec_val == 'embroidery-2') {
                    maxField = 9999
                } else {
                    maxField;
                }

                if (jQuery('.ale_field_wrapper1').length < maxField) {
                    jQuery(wrapper).append(fieldHTML); //Add field html
                }
            }

            /////////////////////////////////////////////////////////////////////////////////
            ////////////////////// ON countMaxfields JS CODE ////////////////////////////////
            function countMaxfields() {
                var product_sum = 0;
                jQuery(".bulktablebody1 input.input-text.qty.text").each(function() {
                    product_sum += Number(jQuery(this).val());
                });

                var ss_min_qty_arr = JSON.parse(jQuery("#ss_min_qty_arr").val());
                var ss_max_qty_arr = JSON.parse(jQuery("#ss_max_qty_arr").val());
                var ss_amountof_color_arr = JSON.parse(jQuery("#ss_amountof_color_arr").val());

				var maxField = 1;

				for (var i = 1; i <= ss_max_qty_arr.length; i++) {
					if (product_sum >= ss_min_qty_arr[i] && product_sum <= ss_max_qty_arr[i]) {
						maxField = ss_amountof_color_arr[i];
					} else if(product_sum >= ss_max_qty_arr[i]) {
                        maxField = ss_amountof_color_arr[i];
                    }
				}

                return [maxField, product_sum];
            }

            ////////////////////////////////////////////////////////////////////////
            ////////////////////// ON READY JS CODE ////////////////////////////////
            jQuery(document).ready(function() {
                jQuery('select#ale_none_field').select2();
                resetAllFields();
                let maxField_product_sum = countMaxfields();
                let show = maxField_product_sum[0];
                jQuery('select.ale_color_field option').show();

                // Hides the other screen print color amounts if it doenst reach the limit
                setTimeout(function() {
                    jQuery('select.ale_color_field option').each(function(i, index) {
                        if (i > show) {
                            jQuery(this).hide();
                        }
                    });
                }, 500);


                // checks if a color is selected
                var attr_color = jQuery('input[name="bulk_ord_attribute_pa_color"]:checked').val();
                var get_deco_val = jQuery(this).val();

                var get_deco_val = jQuery("#ctm-select-decoration option").val();
                var attr_vals = jQuery('.ale_options_group').html();
                var wrap_select = jQuery('.ale_color_field').wrap('<select/>').parent().html();

                if (get_deco_val == "embroidery-2") {
                    jQuery("#ale_color_wraper").empty().html(attr_vals);
                    jQuery('.ale_options_group').empty();
                } else {
                    jQuery("#ale_color_wraper").empty().html(wrap_select);
                }

                if (get_deco_val == "embroidery-2") {
                    jQuery(".ale_clr_names").text("Colors");
                    jQuery('.ale_aft_Adt_btn').css('display', 'none');
                } else if (get_deco_val == "screen-print-3") {
                    jQuery(".ale_clr_names").text("Amount of Colors");
                    jQuery('.ale_aft_Adt_btn').css('display', 'block');
                }

                select2append();
                //New input field html

                var x = 0; //Initial field counter is 1
                //Once add button is clicked
                
                jQuery('.add_button').unbind().click(function() {
                    var getlocvalue = jQuery(".ale_location_field").val();
                    var getcolorvalue = jQuery(".ale_color_field").val();
                    var clramount = jQuery("#ale_none_field").val();
                    var check_already = jQuery('#ale_combo').val();
                    ////////////// ------------------------------------------------------- /////////////////
                    if(check_already !== "") {
                        check_already = JSON.parse(check_already);
                    }
                    ////////////// ------------------------------------------------------- /////////////////
                    for(var count = 0; count < check_already.length; count++ ){
                        if (check_already[count][0].includes(getlocvalue)) {
                            alert('Location already exist.');
                            return;
                        }
                    }
                    ////////////////////////////////////////////////////////////////////////////////////////
                    var dec_val = jQuery('#ctm-select-decoration').val();

                    ////////////////////////////////////////////////////////////////////////////////////////
                    ////////////// THE NOTIFICATION TELL YOU TO SELECT A COMBO OR LOCATION /////////////////
                    if (dec_val == 'screen-print-3') {
                        if (getlocvalue != '' && getcolorvalue != "") {
                            decoration_based_appned_filed_and_check();
                        } else {
                            if(jQuery('.ale_color_field > option'). length > 1){
                                alert('Please select a Combination.');
                            } else{
                                decoration_based_appned_filed_and_check();
                            }
                        }
                    } else if (dec_val == 'embroidery-2') {
                        if (getlocvalue != '') {
                            decoration_based_appned_filed_and_check();
                        } else {
                            alert('Please select a location');
                        }
                    }

                    ////////////////////////////////////////////////////////////////////////////////////////
                    ////// ////// RESET THE SELECT FIELDS AFTER ADD ST /////////////////////////////////////
                    jQuery(".ale_location_field").val("");
                    jQuery(".ale_color_field").val("");
                    jQuery("#ale_none_field").val("");

                    ////////////////////////////////////////////////////////////////////////////////////////
                    ////// ////// ADD SELECTED FIELDS INTO HIDDEN INPUT ARRAY ST ///////////////////////////
                    cstm_arr_add_remove();
                    
                });


                ////////////////////////////////////////////////////////////////////////////////////////
                //////////////////// ONCE REMOVE BUTTON IS CLICKED /////////////////////////////////////
                jQuery('.field_wrapper').on('click', '.remove_button', function(e) {
                    e.preventDefault();

                    var sel_val = jQuery(this).parent('div').find(".location_name").attr("data-attr");
                    var color_val = jQuery(this).parent('div').find(".ale_clr_name").attr("data-attr");

                    jQuery("#bulkvariationform .wc-pao-addon-location input").each(function() {
                        if (jQuery(this).val() == sel_val) {
                            jQuery(this).prop('checked', false);
                        }

                        jQuery("#bulkvariationform .wc-pao-addon-location").val(sel_val).prop('checked', false);
                    }); //

                    jQuery("#bulkvariationform .wc-pao-addon-colors input").each(function() {
                        if (jQuery(this).val() == color_val) {
                            jQuery(this).prop('checked', false);
                        }
                    });

                    jQuery(this).parent('div').remove(); //Remove field html
                    x--; //Decrement field counter
                    cstm_arr_add_remove();
                });


                ////////////////////////////////////////////////////////////////////////////////////////
                ////////////////////////////////////////////////////////
                jQuery("#ctm-select-decoration").change(function() {
                    resetAllFields();

                    let maxField_product_sum = countMaxfields();
                    let show = maxField_product_sum[0];

                    jQuery('select.ale_color_field option').show();

                    setTimeout(function() {
                        jQuery('select.ale_color_field option').each(function(i, index) {

                            if (i > show) {
                                jQuery(this).hide();
                            }
                        });
                    }, 500);

                    var attr_color = jQuery('input[name="bulk_ord_attribute_pa_color"]:checked').val();
                    var get_deco_val = jQuery(this).val();

                    ////////////////////////////////////////////
                    if("<?php echo get_option("ss_show_hide_color") ?>" == "off") {
						if (jQuery(this).val() == 'embroidery-2') { 
							jQuery('.ale_clr_names').css('display', 'none');
						} else if ( jQuery(this).val() == 'screen-print-3' ){
							jQuery('.ale_clr_names').css('display', 'block'); 
						}
					}

                    ////////////////////////////////////////////
                    if (get_deco_val == "embroidery-2") {
                        jQuery("#ale_color_wraper").empty().html(attr_vals);
                        jQuery('.ale_options_group').empty();

                    } else {
                        jQuery("#ale_color_wraper").empty().html(wrap_select);
                    }

                    ////////////////////////////////////////////
                    if (get_deco_val == "embroidery-2") {
                        jQuery(".ale_clr_names").text("Colors");
                        jQuery('.ale_aft_Adt_btn').css('display', 'none');
                    } else if (get_deco_val == "screen-print-3") {
                        jQuery(".ale_clr_names").text("Amount of Colors");
                        jQuery('.ale_aft_Adt_btn').css('display', 'block');
                    }

                    select2append();

                });

                ////////////////////////////////////////////////////////////////////////////////////////
                ////////////////////////////////////////////////////////
                jQuery("button.bulksubmit").click(function(e) {

                    var productQuantity = 0;
                    jQuery(".bulktablebody1 input.input-text.qty.text").each(function() {
                        productQuantity += Number(jQuery(this).val());
                    });

                    var dec_val = jQuery('#ctm-select-decoration').val();

                    var screen_print_cost = '';
                    jQuery(".ale_field_wrapper1").each(function() {
                        screen_print_cost += jQuery(this).find('p:nth-child(2)').text() + ',';
                    });
                    
                    var embr_length = jQuery('.ale_field_wrapper1[data-deco^="embroidery-2"]').length;
                    if (dec_val == "screen-print-3") {
                        screen_print_cost = screen_print_cost;
                    } else {
                        screen_print_cost = embr_length;
                    }

                    jQuery("input#ss_print_count").val(screen_print_cost);
                    jQuery('input#jam_decoration').val(dec_val);


                    if (dev_val == "blank") {
                        let checkvalue = "blankgood";
                    }
                    else if (dec_val == "screen-print-3" || dec_val == "embroidery-2") {
                        let checkvalue = jQuery("input#ale_combo").val();
                        if (checkvalue == "") {
                            alert('Please Select a Combinations');
                            e.preventDefault();
                        }
                    }



                }); //Click function end here

                ////////////////////////////////////////////////////////////////////////////////////////
                ////////////////////////////////////////////////////////
                function resetAllFields() {
                    jQuery(".ale_field_wrapper1").remove();
                    jQuery("input#ss_print_count,input#jam_decoration,input#ale_combo").val('');

                }

                ////////////////////////////////////////////////////////////////////////////////////////
                ////////////////////////////////////////////////////////
                jQuery('tbody.bulktablebody1 input').blur(function() {
                    resetAllFields();
                    let maxField_product_sum = countMaxfields();
                    let show = maxField_product_sum[0];
                    jQuery('select.ale_color_field option').show();
                    setTimeout(function() {
                        jQuery('select.ale_color_field option').each(function(i, index) {
                            if (i > show) {
                                jQuery(this).hide();
                            }
                        });
                    }, 500);
                }); //blur function end here

            }); // ready

        </script>

    <?php
    }
    ?>