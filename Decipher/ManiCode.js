


// CustomJS.php
    add_action("wp_footer","allie_custom_js_file_", 100);

//        [FUNCTION]
            alie_custom_js_file_()
                if (is_product() )
                    last_li_dis_price = jQuery("#bulkvariationform .main-sh-li ul li:last-child p:last-child").text();
                    //////////////////////////////////////////////////
                    if(last_li_dis_price){
                        jQuery(".price").html('From: <span class="woocommerce-Price-amount amount">'+last_li_dis_price+'</span>');
                    }