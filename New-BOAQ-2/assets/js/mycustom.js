jQuery(function($){
	// simple multiple select
	$('#pa_imprint_color').select2();
	$('#pa_imprint_location').select2();
	$('#pa_imprint_type').select2();
});


jQuery(document).ready(function() {
	var dec_opt = jQuery('#dectoggle');

	dec_opt.click(function() {
		var btn = jQuery(this);
		var obj = jQuery("#dec_opt_div");
		var classed = obj.hasClass("block_display_first");
		obj.toggleClass("block_display_first");

		if (classed) {
			btn.text("Add Your Logo");
			obj.slideUp();
		}

		if (!classed) {
			btn.text("Hide Logo Options");
			obj.slideDown();
		}
	})
})