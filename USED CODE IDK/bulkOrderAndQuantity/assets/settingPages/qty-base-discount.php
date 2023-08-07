<?php
      /////////////////////////ADD MENU PAGE FOR DISCOUNT SETTING ////////////////////////

      add_action("admin_menu", "alie_custom_wc_products_settings");

      function alie_custom_wc_products_settings()
      {
          add_menu_page(
              "Wc Products Settings",
              "Wc Products Settings",
              "manage_options",
              "wc_products_settings",
              "woo_wholesale_page_call"
          );
        add_submenu_page(
            "wc_products_settings",
            "Decoration Settings for colors and locations",
            "Decoration Settings",
            "manage_options",
            "woo-global-decoration-settings-product",
            "woo_global_decoration_settings_"
        );
          add_submenu_page(
              "wc_products_settings",
              "Discount for screen print cart product",
              "Screen Print Discount",
              "manage_options",
              "discount-for-screen-print-cart-product",
              "qty_base_discount_for_screen_print_cart_product"
          );
          add_submenu_page(
              "wc_products_settings",
              "Discount for embroidery cart product",
              "Embroidery Discount",
              "manage_options",
              "discount-for-embroidery-cart-product",
              "qty_base_discount_for_embroidery_cart_product"
          );
          add_submenu_page(
            "wc_products_settings", // parent menu slug
            "Color settings Show/Hide", // page title
            "Color settings", // menu title
            "manage_options",
            "color_setting", //slug
            "color_setting" // call back function
        );
      }

      function qty_base_discount_for_screen_print_cart_product()
      {
          include_once PLUGIN_DIR_PATH(__FILE__) .
              "qty_base_discount_for_screen_print_cart_product.php";
      }
      function woo_global_decoration_settings_()  {
        include_once PLUGIN_DIR_PATH(__FILE__) . "decoration_settings.php";  
      }
      
      function qty_base_discount_for_embroidery_cart_product()
      {
          include_once PLUGIN_DIR_PATH(__FILE__) .
              "qty_base_discount_for_embroidery_cart_product.php";
      }
      function color_setting()
      {
          include_once PLUGIN_DIR_PATH(__FILE__) .
              "color_setting.php";
      }
      function woo_wholesale_page_call()
      {
          global $wpdb;
          $table_name = $wpdb->prefix . "ss_wc_product_discount";

          /////////////////////////// INSERT DATA INOT DATABASE/////////////////////////////
        //   if (isset($_POST["submit"])) {
        //       print_r (update_option("ss_glbl_product_tags", $_POST["ss_product_tages"]));
        //     //   die();
        //   }

          if (isset($_POST["submit"]) ) {

            $post_min_qty = $_POST["ss_minqty_field"];
            $post_max_qty = $_POST["ss_maxqty_field"];
            $post_discount = $_POST["ss_discount_in_per"];
            $post_tags = $_POST["ss_product_tages"];
            $post_date = date("m/d/Y");

            $array_to_strings_tags = implode(',',$post_tags);
            $discount_object = array();
            $inner_array = array();
            $inner_array['minQTY'] =  $post_min_qty;
            $inner_array['maxQTY'] =  $post_max_qty;
            $inner_array['discount'] =  $post_discount;
            array_push($discount_object,$inner_array);

            $checkemp = false;
            foreach ($post_tags as $key => $value) {
                $checkmatch_Result = $wpdb->get_row("SELECT * FROM $table_name WHERE tags_name LIKE '%" . $value ."%'" );
                    //  echo "<pre>";
                    //     print_r( $checkmatch_Result);
                    if($checkmatch_Result   ){
                        $checkemp = true;
                    }
            }

            if(!$checkemp){
            $inserted = $wpdb->insert($table_name, [
                'tags_name' => $array_to_strings_tags,
                "tags_object" => serialize($discount_object),
                "date" => $post_date
            ]);
              if( $inserted ){
               echo "<script>window.alert('Data Inserted Successfully')</script>";
              }else{
                  echo "<script>window.alert('Server Error')</script>";
              }
            }else{
                echo "<script>window.alert('already inserted')</script>";
            }
          
          }

          /////////////////////////// ADD NEW VALUE INTO DATABASE AGAINST TAGS/////////////////////////////
              
              if (isset($_POST["ss_update"])) {
                $get_results_ = $wpdb->get_row(
                    "SELECT * FROM $table_name WHERE id =" . $_GET["id"]
                );

                
                $sid = intval($get_results_->id);
                $min_qty = $_POST["ss_minqty_field"];
                $max_qty = $_POST["ss_maxqty_field"];
                $discount = $_POST["ss_discount_in_per"];
                $get_inserted_obj =  unserialize( $get_results_->tags_object );

                // echo "<pre>";
                // print_r($get_inserted_obj);
                // exit();
                
              if (count($get_inserted_obj) > 0 ) {
                    $getmaxvalue = max( array_column($get_inserted_obj ,'maxQTY') );
                    $getminvalue = min( array_column($get_inserted_obj ,'minQTY') );
                 }else{
                    $getmaxvalue = 0;
                    $getminvalue = 0;
                 }


                if( ( $min_qty < $getminvalue && $max_qty < $getminvalue ) || ( $min_qty > $getmaxvalue && $max_qty > $getmaxvalue  ) ){
                 $create_arr =   array('minQTY'=>$min_qty,'maxQTY'=>$max_qty,'discount'=>$discount);
                array_push($get_inserted_obj,$create_arr);
                $serialisedata = serialize($get_inserted_obj);

                $execut = $wpdb->update(
                    $table_name,
                    [
                        "tags_object" => $serialisedata,
                        "date" => date("m/d/Y"),
                    ],
                    ["id" => $sid]
                );

                if ($execut) {
                    echo "<h2 class='notis'>Record Updated Successfully</h2>";
                    // header(
                    //     "Location: " .
                    //         admin_url() .
                    //         "admin.php?page=wc_products_settings"
                    // ); //$_SERVER['HTTP_REFERER']
                    // exit();
                }
                
                }else{
                    echo "<script>window.alert(' ALREADY EXISTS')</script>";
                }
              }
          /////////////////////////// DELETE DATA INOT DATABASE/////////////////////////////
          //delete products limit & price range.
          if ($_GET["action"] == "delete") {
            $getrowid = $_GET['id'];
            $indexId = $_GET['indexId'];
            if( isset($_GET['del_all']) ){
                $delete_row = $wpdb->delete($table_name, array('id' => $getrowid));
                if ($delete_row) {
                    header(
                        "Location: " .
                            admin_url() .
                            "admin.php?page=wc_products_settings"
                    ); //$_SERVER['HTTP_REFERER']
                    exit();
                }//if code execute

            }else{
                $get_results_ = $wpdb->get_row(
                    "SELECT * FROM $table_name WHERE id =" . $getrowid
                );
    $getrowdata = unserialize( $get_results_->tags_object );
    unset($getrowdata[$indexId]);
    $rerandearray = array_values($getrowdata);
    $is_serialise = serialize( $rerandearray ) ;
    
                $delete_data = $wpdb->update(
                    $table_name,
                    [
                        "tags_object" => $is_serialise,
                        "date" => date("m/d/Y"),
                    ],
                    ["id" => $getrowid]
                );
    
                  if ($delete_data) {
                      header(
                          "Location: " .
                              admin_url() .
                              "admin.php?page=wc_products_settings"
                      ); //$_SERVER['HTTP_REFERER']
                      exit();
                  }//if code execute


            }//delete all else end here
          }// action get delete
          ?>

<div class="qty-base-price-wrap">
    <div class="globally-discount-rule-left">
        <h1>Mention Globally Discount Rules</h1>
        <form method="post">
            <table class="form-table ss_qty_base_dis_tab">
                <?php
                if ($_GET['tags_id']) {
                    echo "<span class='termtag'>".get_term( $_GET['tags_id'] )->name."</span>";
                }
        if( isset($_GET['action']) != 'add' ){ ?>
                <tr valign="top">
                    <th scope="row">Product Tags</th>
                    <td>
                        <?php 
                        $product_tag_terms = get_terms([
                            'taxonomy' => "product_tag",
                            'hide_empty' => false,
                        ]);
                        // $product_tag_terms = get_terms(
                        // array( 'hide_empty' => false, // only if you want to hide false 'taxonomy' =>
                        // 'product_tag', ) ); // echo "<pre>"; // print_r($product_tag_terms);
                        // // $tages_result = get_option("ss_glbl_product_tags"); // print_r ($tages_result) ;
                            // echo "<pre>";
                            // print_r($_POST);
                ?>
                        <select
                            id="ss_product_tages"
                            name="ss_product_tages[]"
                            class="select"
                            required
                            multiple="multiple">
                        <?php 
                        if(!empty($product_tag_terms)){
                            foreach ($product_tag_terms as $key => $ss_product_tag) {
								// if (is_array($tages_result)){
                                //     $selected = in_array($ss_product_tag->term_id, $tages_result) ? ' selected="selected"' : "";
                                // }else{ 
								// 	$selected = "";
								// }
                                echo "<option value='".$ss_product_tag->term_id."'>".$ss_product_tag->name."</option>";
                            }
                        }
                            
                        ?>

                        </select>
                 
                    </td>
                </tr>
                <?php  
                        }// not add button isset
                        ?>
                <tr valign="top">
                    <th scope="row">Min QTY</th>
                    <td><input
                        type="number"
                        name="ss_minqty_field"
                        placeholder="Add Qty like 12"
                        required
                        value=""/></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Max QTY</th>
                    <td><input
                        type="number"
                        name="ss_maxqty_field"
                        placeholder="Add Qty like 24"
                        required
                        value=""/></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Discount in %</th>
                    <td><input
                        type="number"
                        name="ss_discount_in_per"
                        required
                        step="any"
                        value=""/></td>
                </tr>
            </table>
        <?php  
		  if ($_GET["action"] == "add") {
            echo '<div class="dis_update_btn">
            <p class="submit"><input type="submit" name="ss_update" id="submit" class="button button-primary" value="Save Changes"></p>
            <p class="ctm_btn_back"><a href="'.admin_url().'admin.php?page=wc_products_settings" class="button button-primary"> Back</a></p>
            </div>';
                  
              } else {	
                  submit_button();
              } ?>
        </form>
    </div>
    <script>
        jQuery(document)
            .ready(function () {
                jQuery('select#ss_product_tages').select2();

                var get_idd = "#<?php echo $_GET['tags_id']; ?>";
                jQuery(get_idd).css("display", "table-row");

                jQuery('tr[class^="row_tag_"]').click(function () {
                    const classnakme = jQuery(this).attr('class');
                    const converttoarray = classnakme.split('_');
                    const arrylength = converttoarray.length;
                    const index = jQuery.trim(converttoarray[arrylength - 1]);
                    jQuery('tr[class*="row_value_' + index + '"]').toggle('slow')

                });
            });
    </script>
    <table class="fetchin-data-obj fetchin-data">
        <tbody>
            <?php global $wpdb;

            if ($_GET["id"]) {
                $result = array();
                $result_ss = $wpdb->get_row("SELECT * FROM $table_name WHERE id =" . $_GET["id"]);
                array_push($result,$result_ss);
            } else {
                $result = $wpdb->get_results("SELECT * FROM $table_name");
            }

            // $result = $wpdb->get_results("SELECT * FROM $table_name ORDER BY ABS(ss_min_qty_field)");
            // echo "<pre>";
            // print_r($result);

            foreach ($result as $key => $value) { 
                $tags_name = $value->tags_name;
                $tagobject = unserialize( $value->tags_object );

                $action_for_add = admin_url().'admin.php?page=wc_products_settings&action=add&id='.$value->id;
             $action_for_delete = $_SERVER['REQUEST_URI'].'&action=delete&id='.$value->id;

             $str_to_arr = explode( ',', $tags_name );
             $tags_name_get = '';
             foreach($str_to_arr as $tagid){
                $tags_name_get .= '<span class="termtag">'.get_term( $tagid )->name."</span>";
             }
             echo '<tr class="row_tag_'.$key.'"><td>'.$tags_name_get.'<a href="'.$action_for_delete.'&indexId='.$key.'&del_all=true" class="button-primary" style="float: right;">Delete All</a></td></tr>';
             echo "<tr class='row_value_".$key."' id='".$tags_name."'><td><table class='fetchin-data row_content_".$key."'>";
             
             echo "<tr><th>MinQTY</th><th>Max QTY</th> <th>Discountin<bold>%</bold></th><th>Update</th></tr>";


             $minQTY_get = array_column($tagobject, 'minQTY');
             array_multisort($minQTY_get, SORT_ASC, $tagobject);

            //  echo "<pre>";
            //  print_r($tagobject_1);

            foreach($tagobject as $key1 => $value2){ 

                $minqty = $value2['minQTY'];
                $maxqty = $value2['maxQTY'];
                $disc = $value2['discount'];

             echo "<tr>";
             echo
            "<td>".$minqty."</td> <td>".$maxqty."</td> <td>".$disc."</td> <td>
            <a href='".$action_for_delete."&indexId=".$key1."' class='button delete-theme'>Delete</a>
            </td>";
             echo "</tr>";
         } echo"<tr><td><a
            href='".$action_for_add."&tags_id=".$tags_name ."' class='button-primary'>Add</a></td></tr></table></td></tr>";
             } ?>
        </tbody>
    </table>
</div>
<?php
      }

      /////////////////////////// woocommerce before add to cart form ///////////////////////////

      function show_discount_product_base_and_gloablly($arr, int $price, $boolean = true)
      {
        //  echo $price;
          $html = '<div class="main-sh-li">';
          $html .= "<p>Blank Pricing</p>";
          $html .= "<ul>";
          foreach ($arr as $key => $value) {
              if ($boolean) {
                  $qty = $value["minQTY"];
                  $discount = $value["discount"];
              } 
            //   else {
            //       $qty = $value->ss_max_qty_field;
            //       $discount = $value->ss_discount_in_per;
            //   }
            
              $after_discount = $price - ($price * $discount) / 100;

              $html .= "<li data-discount=" . $discount . ">";
              $html .= "<p>" . $qty . "+</p>";
              $html .= '<hr class="hr-sh">';
              $html .= '<p>$' . round($after_discount, 2) . "</p>";
              $html .= "</li>";
          }
          $html .= " </ul>";
          $html .= "</div>";
          echo $html;
      }

       add_action("Show_Discount_On_Product_Page","Show_Discount_On_Product_Page");

      function Show_Discount_On_Product_Page()
      {
          global $wpdb, $product, $post;
           $postId = $post->ID;
           $product_p = $product->get_price();
          $table_name = $wpdb->prefix . "ss_wc_product_discount";

           $product_tags = $product->get_tag_ids();
            foreach ($product_tags as $value) {
                $result =  $wpdb->get_row(  "SELECT * FROM $table_name WHERE tags_name LIKE '%" . $value ."%'" );
                if ($result ) {
                    $unser_tag_obj = unserialize($result->tags_object);
                    $minQTY_get_ = array_column($unser_tag_obj, 'minQTY');
                    array_multisort($minQTY_get_, SORT_ASC, $unser_tag_obj);
                    break;
                }
            }

          if ($unser_tag_obj) {
                show_discount_product_base_and_gloablly(
                    $unser_tag_obj,
                    $product_p,
                );
              }
		  
		  	$table_screen_print_cart_prod = $wpdb->prefix . "ss_screen_print_cart_product";
			$result_screen_print_cart_prod = $wpdb->get_results("SELECT * FROM $table_screen_print_cart_prod ORDER BY ABS(ss_min_qty_field)");
			$ss_min_qty_arr = array_column($result_screen_print_cart_prod, 'ss_min_qty_field');
			$ss_max_qty_arr = array_column($result_screen_print_cart_prod, 'ss_max_qty_field');
			$ss_amountof_color_arr = array_column($result_screen_print_cart_prod, 'ss_amountof_color');

			if (is_array($ss_max_qty_arr) && count($ss_max_qty_arr) > 0) {
				echo " <input type='hidden' id='ss_max_qty_arr' name='ss_hidden_max_qty_arr' value='" . json_encode($ss_max_qty_arr) . "'>";
			} else {echo " <input type='hidden' id='ss_max_qty_arr' name='ss_hidden_max_qty_arr' value='0'>";}
			if (is_array($ss_min_qty_arr) && count($ss_min_qty_arr) > 0) {
				echo " <input type='hidden' id='ss_min_qty_arr' name='ss_hidden_min_qty_arr' value='" . json_encode($ss_min_qty_arr) . "'>";
			} else {echo " <input type='hidden' id='ss_min_qty_arr' name='ss_hidden_min_qty_arr' value='0'>";}
			if (is_array($ss_amountof_color_arr) && count($ss_amountof_color_arr) > 0) {
				echo " <input type='hidden' id='ss_amountof_color_arr' name='ss_hidden_amountof_color_arr' value='" . json_encode($ss_amountof_color_arr) . "'>";
			} else {echo " <input type='hidden' id='ss_amountof_color_arr' name='ss_hidden_amountof_color_arr' value='0'>";}
          }
    //   }

      /////////////////////////// DATABASE TABLE ///////////////////////////

      function ss_create_table_custom()
      {
          global $wpdb;
          $charset_collate = $wpdb->get_charset_collate();
          require_once ABSPATH . "wp-admin/includes/upgrade.php";
          /////////////////Create table (ss_wc_product_discount)/////////////////////////
          $table_deco_setting = $wpdb->prefix . "ss_gloablly_decoration_settings";
          $sql = "CREATE TABLE IF NOT EXISTS $table_deco_setting (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            tags_name varchar(5000) DEFAULT '' NOT NULL,
            tags_object varchar(5000) DEFAULT '' NOT NULL,
            date varchar(55) DEFAULT '' NOT NULL,
            PRIMARY KEY  (id)
          ) $charset_collate;";
          dbDelta($sql);

        /////////////////Create table (ss_wc_product_discount)/////////////////////////
        $table_name = $wpdb->prefix . "ss_wc_product_discount";
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            ss_min_qty_field varchar(55) DEFAULT '' NOT NULL,
            ss_max_qty_field varchar(55) DEFAULT '' NOT NULL,
            ss_discount_in_per varchar(55) DEFAULT '' NOT NULL,
            tags_name varchar(5000) DEFAULT '' NOT NULL,
            tags_object varchar(5000) DEFAULT '' NOT NULL,
            date varchar(55) DEFAULT '' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

          ///////////////////////Create table (ss_screen_print_cart_product)////////////////
          $table_screen_print = $wpdb->prefix . "ss_screen_print_cart_product";
          $sql = "CREATE TABLE IF NOT EXISTS $table_screen_print (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          ss_min_qty_field varchar(55) DEFAULT '' NOT NULL,
          ss_max_qty_field varchar(55) DEFAULT '' NOT NULL,
          ss_discount_in_per varchar(55) DEFAULT '' NOT NULL,
		  ss_amountof_color varchar(55) DEFAULT '' NOT NULL,
          date varchar(55) DEFAULT '' NOT NULL,
          PRIMARY KEY  (id)
        ) $charset_collate;";
          dbDelta($sql);
          /////////////////Create table (ss_embroidery_cart_product)//////////////////////
          $table_embroidery = $wpdb->prefix . "ss_embroidery_cart_product";
          $sql = "CREATE TABLE IF NOT EXISTS $table_embroidery (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          ss_min_qty varchar(55) DEFAULT '' NOT NULL,
          ss_max_qty varchar(55) DEFAULT '' NOT NULL,
          ss_discount_in_per varchar(55) DEFAULT '' NOT NULL,
          date varchar(55) DEFAULT '' NOT NULL,
          PRIMARY KEY  (id)
          ) $charset_collate;";
          dbDelta($sql);
      }

?>