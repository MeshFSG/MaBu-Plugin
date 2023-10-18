<?php

    add_action("wp_footer", "alie_custom_js_file_", 100);

   function alie_custom_js_file_(){
    if ( is_product() ) { ?>
        <script type="text/javascript">
            
            // GRABS THE LAST LISTED DISPLAY PRICE 
            last_li_dis_price = jQuery("#bulkvariationform .main-sh-li ul li:last-child p:last-child").text();
	
            // SEES IF IT EXISTS AND IF IT DOES IT SHOWS THE LAST PRICE 
            if(last_li_dis_price) {
                jQuery(".summary .price").html('From: <span class="woocommerce-Price-amount amount">'+last_li_dis_price+'</span>');
	        }

            // THIS APPENDS THE AMOUNT OF COLORS AND MIN QUANTITY TABLE TO SCREEN PRINT DEC
            jQuery('#ale_wrap_container').append('<div id="custom-table-sh" class="ale_aft_Adt_btn"><div class="custom-table-inner-sh"><p>Amount of Colors</p><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span></div><div class="custom-table-inner-sh"><p>Minimum Quantity</p><span>12</span><span>23</span><span>48</span><span>96</span><span>144</span><span>280</span></div></div>');
            

                // jQuery('#ale_wrap_container').append('<div id="customtwo-table-sh" class="ale_aft_Adt_btntwo"><div class="customtwo-table-inner-sh"><p>Product Quantity</p><span>12</span><span>24</span><span>48</span><span>96</span><span>144</span><span>300</span></div><div class="customtwo-table-inner-sh"><p>Minimum Quantity</p><span>$7.95</span><span>$6.50</span><span>$5.95</span><span>$5.50</span><span>$4.75</span><span>$4.35</span></div></div>');
                // <div id="customtwo-table-sh" class="ale_aft_Adt_btntwo">


            // THIS DISPLAYS THE STOCK QUANTITY UNDER THE SIZE
            jQuery(document).ajaxComplete(function(){
                jQuery('.stockavail').remove();
                jQuery("label.AvailableAllTime input").each(function(){
                    const getmaxvalue =  parseInt(jQuery(this).attr('max'));
                    if(  getmaxvalue > 0){
                        jQuery(this).after('<span class="stockavail">Stock:</span><span>'+getmaxvalue+'</span>')
                    }
                });
            });

            // REMOVE OPTION ARROW ICON IF OPT QTY 1 ST
            if(jQuery("#ctm-select-decoration option").length == 1){
                jQuery("#ctm-select-decoration").css("-webkit-appearance", "none");
            }

            if (jQuery("select.ale_location_field option").length == 1) {
                jQuery("select.ale_location_field").css("-webkit-appearance", "none");
            }

            /////////////////// REMOVE OPTION ARROW ICON IF OPT QTY 1 END////////////////////

        </script>

    <?php }
   }

   add_action('wp_head', 'show_hide_color_field');
   
    function show_hide_color_field(){
        echo (get_option("ss_show_hide_color") == "off") ? "<style>#ale_none_field, .select2-container--default, .ale_clr_names {display: none ;} </style>" : "";
    }


    /////////THIS IS THE jQuery append on line 14


// <div id="custom-table-sh" class="ale_aft_Adt_btn">
//     <div class="custom-table-inner-sh">
//         <p>Amount of Colors</p>
//         <span>1</span>
//         <span>2</span>
//         <span>3</span>
//         <span>4</span>
//         <span>5</span>
//         <span>6</span>
//     </div>
//     <div class="custom-table-inner-sh">
//         <p>Minimum Quantity</p>
//         <span>12</span>
//         <span>23</span>
//         <span>48</span>
//         <span>96</span
//         <span>144</span>
//         <span>280</span>
//     </div>
// </div>