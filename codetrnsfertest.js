/*********============  DECORATION VISIBLE TOGGLE ============*****************/
jQuery(document).ready(function() {
	var dec_opt = jQuery('#dectoggle');

	dec_opt.click(function() {
		var btn = jQuery(this);
		var obj = jQuery("#dec_opt_div");
		var classed = obj.hasClass("block_display_first");
		obj.toggleClass("block_display_first");
		
			if (classed) {
// 				btn.text("Include Decoration");
 				btn.text("Add Your Logo");
				obj.slideUp();
			}
	
			if (!classed) {
				btn.text("Hide Logo Options");
				obj.slideDown();
			}
	})
})

////////////////////////////////////////////////////////////////////////////////////////////////


jQuery(document).ready(function() {
	
// 		var hiddenbtn = jQuery("#hiddentrigger");
		var btnclass = hiddenbtn.hasClass("hiddentrigger");
		var obj = jQuery("#dec_opt_div");
	if (btnclass) {
			obj.slideDown();
	}
})










///////////////////////////////////////////////////////////////////////////////////////////////////
/*_____________________________________________________________________________*/
/************************** CHECK IF PRODUCT BELONGS TO TAG *******************/
/*___________________________________________________________________________*/

add_action( 'woocommerce_before_single_product', 'bbloomer_print_banner_if_product_belongs_to_tag_red' );
 
function bbloomer_print_banner_if_product_belongs_to_tag_red() {
   if ( has_term( 'SDNS', 'product_tag' ) ) {
      echo 'yooo belongs';
   }
}

///////////////////////////////////////////////////////////////////////////////////////////////////

add_action( 'showdec', 'disallow_blank_if_product_brand_requires' );
function disallow_blank_if_product_brand_requires() {
   if ( has_term( 'SDNS', 'product_tag' ) ) {
      echo '<button id="hiddentrigger" class="hiddentrigger"/>' ;
   } else {
	echo '
		<div class="decshowwrapper">
			<div>
				<button 
					type="button"
					id="dectoggle"
					class="deconoff"
				> 
					Add Your Logo
				</button>
			</div>
		</div>
	';
   }
}


////////////////////////////////////////////////////////////////////////////////////////////////




























