<?php
    global $wpdb;
    $table_name = $wpdb->prefix . "ss_gloablly_decoration_settings";

    if (isset($_POST["submit"]) ) {
        $post_tags = (array) $_POST["ss_product_tages"];
        $array_to_strings_tags = implode(",", (array) $post_tags);

        $objeArr = array();
        $createArr["pa_imprint_type"] = $_POST["pa_imprint_type"];
        $createArr["pa_imprint_location"] = $_POST["pa_imprint_location"];
        $createArr["pa_imprint_color"] = $_POST["pa_imprint_color"];
        array_push($objeArr,$createArr);


        $checktag_Exist = $wpdb->get_row(
            "SELECT * FROM $table_name WHERE tags_name LIKE '%" . $array_to_strings_tags ."%'"
      );

      if (empty($checktag_Exist)) {
        $inserted = $wpdb->insert($table_name, [
              'tags_name' => $array_to_strings_tags,
              "tags_object" => serialize($objeArr),
              "date" => date("m/d/Y")
          ]);
          if ($inserted) {
            echo "<script>window.alert('Data Inserted Successfully')</script>";
          } else {
            echo "<script>window.alert('Data Not Inserted.')</script>";
          }
      } 
      else {
        echo "<script>window.alert('ALREADY EXISTS')</script>";
      }
        
}
/////////////////////////////// UPDATE CODE GOES HERE//////////////////////////////////////////

    if (isset($_POST['ss_update_btn'])) {
        // $post_tags = (array) $_POST["ss_product_tages"];
        // $array_to_strings_tags = implode(",", (array) $post_tags);
        $objeArr = array();
        $createArr["pa_imprint_type"] = $_POST["pa_imprint_type"];
        $createArr["pa_imprint_location"] = $_POST["pa_imprint_location"];
        $createArr["pa_imprint_color"] = $_POST["pa_imprint_color"];
        array_push($objeArr,$createArr);

        $update_tags = $wpdb->get_row(
            "SELECT `tags_name` FROM $table_name WHERE id =" . $_GET['id']
        );

        $update_data = $wpdb->update(
            $table_name,
            [
                'tags_name' => $update_tags->tags_name,
                "tags_object" => serialize($objeArr),
                "date" => date("m/d/Y")
            ],
            ["id" => $_GET["id"]]
        );

        if ($update_data) {
            echo "<h2 class='notis'>Record Updated Successfully</h2>";
            // header(
            //     "Location: " .
            //         admin_url() .
            //         "admin.php?page=woo-global-decoration-settings-product"
            // ); //$_SERVER['HTTP_REFERER']
            // exit();
        }//if code execute
}

/////////////////////////////// DELETE CODE GOES HERE//////////////////////////////////////////
if( $_GET["action"] == "delete") {
    $delete_row = $wpdb->delete($table_name, array('id' => $_GET["id"]));
    if ($delete_row) {
        header(
            "Location: " .
                admin_url() .
                "admin.php?page=woo-global-decoration-settings-product"
        ); //$_SERVER['HTTP_REFERER']
        exit();
    }//if code execute

}
/////////////////////////////// DELETE CODE GOES HERE//////////////////////////////////////////

?>

<div class="ss_global_deco_setting_container">
    <div class="globally__deco_setting_wrap">
        <h1>Decoration Settings</h1>
        <form method="post">
            <table class="form-table ss_global_decoration_tb ss_qty_base_dis_tab">
                    <?php 
                        $product_tag_terms = get_terms([
                            'taxonomy' => "product_tag",
                            'hide_empty' => false,
                        ]);  
                        
                        $getId = $_GET['id'];
                        $get_results_ = $wpdb->get_row(
                            "SELECT * FROM $table_name WHERE id =" . $getId
                        );
                        $get_tags_ = explode(",", $get_results_->tags_name);
                        $get_obj = $get_results_->tags_object;
                        
                        $get_deco_type = unserialize($get_obj)[0]['pa_imprint_type'];
                        $imprint_location = unserialize($get_obj)[0]['pa_imprint_location'];
                        $imprint_color = unserialize($get_obj)[0]['pa_imprint_color'];
                        //   echo "<pre>";
                        //   print_r($get_deco_type);
                        // print_r($pa_imprint_color);
                        if ($_GET['action'] != "update") { ?>
                    <tr valign="top">
                    <th scope="row">Product Tags</th>
                    <td>
                        <select
                            id="ss_product_tages"
                            name="ss_product_tages[]"
                            class="select"
                            multiple="multiple" required>
                        <?php 
                        
                        if(!empty($product_tag_terms)){
                            foreach ($product_tag_terms as $key => $ss_product_tag) {
                                $selected = ( is_array( $get_tags_ ) && in_array( $ss_product_tag->term_id, $get_tags_ ) ) ? ' selected="selected"' : '';
                                echo '<option value="' . $ss_product_tag->term_id . '"' . $selected . '>' . $ss_product_tag->name . '</option>';
                            }
                        }  ?>
                        </select>
                        
                    </td>
                </tr>
                <?php  } ?>
                <tr valign="top">
                    <th scope="row">Select Imprint Type:</th>
                    <td>
                    <select id="pa_imprint_type" name="pa_imprint_type[]" multiple="multiple" required>
                        <option value="embroidery-2" <?php if (!empty($get_deco_type)) { echo in_array( 'embroidery-2',  $get_deco_type ) ? ' selected="selected"' : '';}?> >Embroidery</option>
                        <option value="screen-print-3" <?php if (!empty($get_deco_type)) { echo in_array( 'screen-print-3',  $get_deco_type ) ? ' selected="selected"' : '';  }?> >Screen Print</option>
                    <select> 
                    
                    </td>
                </tr>
<?php

//////////////////////////////////////IMPRINT LOCATION SELECTION ///////////////////////////////////////////


if( $get_imprt_loc = get_terms( 'pa_imprint-location', 'hide_empty=0' ) ) { ?>

    <tr valign="top">
        <th scope="row">Select Imprint Location:</th>
        <td>
            <select id="pa_imprint_location" name="pa_imprint_location[]" multiple="multiple" required>
            <?php
            foreach( $get_imprt_loc as $val ) {
                $selected = ( is_array( $imprint_location ) && in_array( $val->term_id, $imprint_location ) ) ? ' selected="selected"' : '';
                echo '<option value="' . $val->term_id . '" '.$selected.'>' . $val->name . '</option>';
            } ?>
         <select> 
        </td>
    </tr>

   <?php
}
//////////////////////////////////////IMPRINT COLOR SELECTION ///////////////////////////////////////////

     if( $tags = get_terms( 'pa_print-colors', 'hide_empty=0' ) ) {  ?>
     
     <tr valign="top">
        <th scope="row">Select Imprint Color:</th>
        <td>
        <select id="pa_imprint_color" name="pa_imprint_color[]" multiple="multiple" required>
          <?php
            foreach( $tags as $tag ) {
                 $selected = ( is_array( $imprint_color ) && in_array( $tag->term_id, $imprint_color ) ) ? ' selected="selected"' : '';
                echo '<option value="' . $tag->term_id . '" '.$selected .'>' . $tag->name . '</option>';
            } ?>
	 	<select> 
        </td>
     </tr>
        <?php } ?>
             
            </table>
        <?php  
		  if ($_GET["action"] == "update") {
                //   echo '<p class="submit"><input type="submit" name="ss_update_btn" id="submit" class="button button-primary" value="Save Changes"></p>';

                  echo '<div class="dis_update_btn">
                  <p class="submit"><input type="submit" name="ss_update_btn" id="submit" class="button button-primary" value="Update">
                  <p class="ctm_btn_back"><a href="'.admin_url().'admin.php?page=woo-global-decoration-settings-product" class="button button-primary"> Back</a></p>
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

                var get_idd = "<?php echo $_GET['id']; ?>";
                console.log(".get_row_value_"+get_idd);
                jQuery(".get_row_value_"+get_idd).css("display", "table-row");

                jQuery('tr[class^="get_row_tag_"]').click(function(){
                    getcls = jQuery(this).attr('class');
                    converttoArr = getcls.split("_");
                    getLen = converttoArr.length;
                    const lastIndex = converttoArr[converttoArr.length - 1];
                    jQuery('tr[class*="get_row_value_' + lastIndex + '"]').toggle('slow')
                });
            });
    </script>
    <table class="fetchin-data-obj fetchin-data">
        <tbody>
        <?php 

            if ($_GET["id"]) {
                $result = array();
                $result_by_id = $wpdb->get_row( "SELECT * FROM $table_name WHERE id =" . $_GET['id'] );
                array_push($result,$result_by_id);
            } else {
                $result = $wpdb->get_results("SELECT * FROM $table_name ");
            }


                foreach ($result as $key => $value) {
                    $tags_name = $value->tags_name;
                    $tagobject = unserialize( $value->tags_object );
                    $action_for_delete = admin_url().'admin.php?page=woo-global-decoration-settings-product&action=delete&id='.$value->id; ?>
            <tr class="get_row_tag_<?php echo $value->id ?>"><td>
                <?php 
                foreach(explode(",",$tags_name) as $tagid){
                    echo '<span class="termtag">'.get_term( $tagid )->name."</span>";
                 }
                ?>
                <a href="<?php echo $action_for_delete; ?>" class="button-primary" style="float: right;">Delete All</a>
            </td>
        </tr>
        <tr class="get_row_value_<?php echo $value->id ?>">
            <td>
            <table class="fetchin-data row_content_<?php echo $value->id ?>">
                <tbody>
                    <tr><th>Imprint Type</th> <th>Imprint Location</th> <th>Imprint Color</th> 
                    <?php if (!$_GET['id']) {
                        echo "<th style='float: right;padding-right: 20px;'>Action</th> ";
                    } ?> 
                </tr>
                    <?php 
                    foreach ($tagobject as $key1 => $value1) { ?>
                    <tr>
                    <td class="ctm_imprt_type"><?php 
                    // print_r($value1['pa_imprint_type']); 
                    // print_r(explode('-', $value1['pa_imprint_type']) );
                    foreach ($value1['pa_imprint_type'] as $key => $typeName) {
                        
                        echo '<span class="termtagName">'.substr($typeName, 0, -2) ."</span>";
                    }
                    ?></td> 
                    <td class="imprint_loc">
                        <?php
                        foreach ($value1['pa_imprint_location'] as $key => $term_id) {
                            $term_name_by_id = get_term(
                                intval($term_id),
                                "pa_imprint-location"
                            )->name;
                            echo '<span class="termtagName">'.$term_name_by_id."</span>";
                        } ?>
                    </td> 
                    <td class="imprint_clr_"><?php 
                        foreach ($value1['pa_imprint_color'] as $key => $term_id1) {
                            $term_name_by_id_dd = get_term(
                                intval($term_id1),
                                "pa_print-colors"
                            )->name;
                             echo '<span class="termtagName">'.$term_name_by_id_dd."</span>";
                        }
                    ?></td>  
                    <?php if (!$_GET['id']) { ?>
                        <td class="ctm_update_td">
                            <a href='<?php echo admin_url().'admin.php?page=woo-global-decoration-settings-product&action=update&id='.$value->id ?>' class='button ctm_update_btn'>Update</a>
                        </td>
                    <?php } ?>
                    </tr>
                    <?php } ?>
            <tr>
            <!-- <td> <a href="" class="button-primary">Update</a> </td> -->
        </tr>
    </tbody>
    </table>
    </td>
    </tr>
    <?php } ?>
    
    </tbody>
        </table>