<?php
if (isset($_POST["submit"])) {
    if (!isset($_POST["show_hide_color"]))
        $_POST["show_hide_color"] = "off";
    update_option("ss_show_hide_color", $_POST["show_hide_color"]);
}
?>
<form method="post" id="wholesale-product-range-Price" action="" enctype="multipart/form-data">
    <h2>Show/Hide color select box on product page</h2>
    <label class="switch">
        <?php $selected = (get_option("ss_show_hide_color") == "on") ? ' checked="checked"' : ""; ?>
        <input name="show_hide_color" <?php echo $selected; ?> type="checkbox">
        <span class="slider round"></span>
		<p class="show_hide">hide/show</p>
    </label>
    <?php submit_button(); ?>
</form>