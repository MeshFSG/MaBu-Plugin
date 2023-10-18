<?php

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////// ????????????????????????????????????????????????? ////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////


// function scratchcode_run_code_one_time() {
//     if ( !get_option('run_only_once_01') ):
//         // Execute your one time code here
//         create_imprint_loc_color_attribute_taxonomies();
//         add_option('run_only_once_01', 1);
//     endif;
// }
// add_action( 'init', 'scratchcode_run_code_one_time' );

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////// CREATES IMPORT LOCATION & COLOR ATTRIBUTE TAXONOMY ////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// add_action("init", "create_imprint_loc_color_attribute_taxonomies");

function create_imprint_loc_color_attribute_taxonomies() {

  $attributes = wc_get_attribute_taxonomies();
  $slugs = wp_list_pluck( $attributes, 'pa_imprint-location' );

  if ( ! in_array( 'pa_imprint-location', $slugs ) ) {
      $args = array(
          'slug'    => 'pa_imprint-location',
          'name'   => __( 'Imprint Location', 'your-textdomain' ),
          'type'    => 'select',
          'orderby' => 'menu_order',
          'has_archives'  => false,
      );
      $result = wc_create_attribute( $args );
  }

  $colors_slugs = wp_list_pluck( $attributes, 'pa_print-colors' );
  if ( ! in_array( 'pa_print-colors', $colors_slugs ) ) {
    $args_1 = array(
        'slug'    => 'pa_print-colors',
        'name'   => __( 'Imprint Colors', 'your-textdomain' ),
        'type'    => 'color',
        'orderby' => 'menu_order',
        'has_archives'  => false,
      );
    $result_1 = wc_create_attribute( $args_1 );
	}


}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////// Adds Decoration Settings Tab into the WC Products Settings ////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function misha_product_settings_tabs( $tabs ){
	$tabs['misha'] = array(
		'label'    => 'Decoration Settings',
		'target'   => 'alie_ss_product_data',
		'class'    => array('show_if_variable'),
		'priority' => 21,
	);
	return $tabs; 
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////// ????????????????????????????????????????????????? ////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function misha_product_panels(){
	echo '<div id="alie_ss_product_data" class="panel woocommerce_options_panel hidden">';

  ////////////////////////////////////// IMPRINT DECORATION SELECTION ///////////////////////////////////////////
  $get_imprint_meta = get_post_meta( get_the_ID(), 'pa_imprint_type',true ); ?>

  <p class="form-field "> <label for="pa_imprint_type">Select Imprint Type:</label>
   <select id="pa_imprint_type" name="pa_imprint_type[]" multiple="multiple" style="width:99%;max-width:25em;">

     <option value="embroidery-2" <?php if (!empty($get_imprint_meta)) {
       echo in_array( 'embroidery-2',  $get_imprint_meta ) ? ' selected="selected"' : '';
     }
     ?>>Embroidery</option>

     <option value="screen-print-3" <?php if (!empty($get_imprint_meta)) {
      echo in_array( 'screen-print-3',  $get_imprint_meta ) ? ' selected="selected"' : ''; 
     }
     ?>>Screen Print</option>

   <select>    
  </p>

  <?php

  ////////////////////////////////////// IMPRINT LOCATION SELECTION ///////////////////////////////////////////
  $pa_imprint_location = get_post_meta( get_the_ID(), 'pa_imprint_location',true );

  if( $get_imprt_loc = get_terms( 'pa_imprint-location', 'hide_empty=0' ) ) {  
    ?>
      <p class="form-field "> <label for="pa_imprint_location">Select Imprint Location:</label>
        <select id="pa_imprint_location" name="pa_imprint_location[]" multiple="multiple" style="width:99%;max-width:25em;">
          <?php
            foreach( $get_imprt_loc as $val ) {
              $selected = ( is_array( $pa_imprint_location ) && in_array( $val->term_id, $pa_imprint_location ) ) ? ' selected="selected"' : '';
              echo '<option value="' . $val->term_id . '"' . $selected . '>' . $val->name . '</option>';
            }
          ?>
        <select>    
      </p>
    <?php
  }

  ////////////////////////////////////// IMPRINT COLOR SELECTION ///////////////////////////////////////////
    // always array because we have added [] to our <select> name attribute
	$imprint_color = get_post_meta( get_the_ID(), 'pa_imprint_color',true );
    
  if( $tags = get_terms( 'pa_print-colors', 'hide_empty=0' ) ) {  
    ?>
	 	  <p class="form-field "> <label for="pa_imprint_color">Select Imprint Color:</label>
        <select id="pa_imprint_color" name="pa_imprint_color[]" multiple="multiple" style="width:99%;max-width:25em;">
          <?php
            foreach( $tags as $tag ) {
                $selected = ( is_array( $imprint_color ) && in_array( $tag->term_id, $imprint_color ) ) ? ' selected="selected"' : '';
                echo '<option value="' . $tag->term_id . '"' . $selected . '>' . $tag->name . '</option>';
            }
          ?>
        <select>    
      </p>
    <?php
	}

	echo '</div>';
}
 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////// ??????? SAVE I THINK IT WAS ???????? ////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function woocommerce_product_custom_fields_save($post_id){
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
      update_post_meta( $post_id, 'pa_imprint_color', $_POST['pa_imprint_color'] );
      update_post_meta( $post_id, 'pa_imprint_location', $_POST['pa_imprint_location'] );
      update_post_meta( $post_id, 'pa_imprint_type', $_POST['pa_imprint_type'] );
}   


