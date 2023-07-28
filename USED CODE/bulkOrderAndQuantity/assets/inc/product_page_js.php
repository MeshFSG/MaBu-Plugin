<?php
function ss_product_custom_js_()
{
    if (!is_single()) return; ?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery('#ctm-select-decoration option:contains("embroidery 2")').text('Embroidery');
            jQuery('#ctm-select-decoration option:contains("screen print 3")').text('Screen Print');
        });
    </script>
<?php
} // func
add_action('wp_footer', 'ss_product_custom_js_');
