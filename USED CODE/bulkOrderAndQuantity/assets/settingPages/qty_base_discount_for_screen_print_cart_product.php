<?php
global $wpdb;
$table_screen_print = $wpdb->prefix . "ss_screen_print_cart_product";
$get_min_col = get_min_max("ss_min_qty_field",$table_screen_print);
$get_max_col = get_min_max("ss_max_qty_field",$table_screen_print);

/////////////////////////// INSERT DATA INOT DATABASE/////////////////////////////
if (isset($_POST['submit']) && !empty($_POST['ss_screenp_qty_min_limit_field']) && !empty($_POST['ss_screenp_qty_max_limit_field'])) {
	if($_POST['ss_screenp_qty_min_limit_field'] < $get_min_col->ss_min && $_POST['ss_screenp_qty_max_limit_field'] < $get_min_col->ss_min || $_POST['ss_screenp_qty_min_limit_field'] > $get_max_col->ss_max && $_POST['ss_screenp_qty_max_limit_field'] > $get_max_col->ss_max ) {
		$inserted_screenp = $wpdb->insert(
			$table_screen_print,
			array(
				'ss_min_qty_field' => $_POST['ss_screenp_qty_min_limit_field'],
				'ss_max_qty_field' => $_POST['ss_screenp_qty_max_limit_field'],
				'ss_discount_in_per' => $_POST['ss_screenp_discount_in_per'],
				'ss_amountof_color' => $_POST['ss_amountof_color'],
				'date' => date("m/d/Y"),
			)
		);
		if ($inserted_screenp) echo "<h2>Data Inserted Successfully</h2>";
	} else { echo "<script> alert('limits are incorect.'); </script>";}
}
/////////////////////////// UPDATE DATA INOT DATABASE/////////////////////////////
if ($_GET['action']) {

    $results_screenp = $wpdb->get_row("SELECT * FROM $table_screen_print WHERE id =" . $_GET['id']);
    $ss_min_qty_field = intval($results_screenp->ss_min_qty_field);
    $ss_max_qty_field = intval($results_screenp->ss_max_qty_field);
    $ss_discount_in_per = $results_screenp->ss_discount_in_per;
    $ss_amountof_color = $results_screenp->ss_amountof_color;
    $sid = intval($results_screenp->id);

    if (isset($_POST['ss_update']) && !empty($_POST['ss_screenp_qty_min_limit_field']) && !empty($_POST['ss_screenp_qty_max_limit_field'])) {
        $update_screenp = $wpdb->update(
            $table_screen_print,
            array(
                'ss_min_qty_field' => $_POST['ss_screenp_qty_min_limit_field'],
                'ss_max_qty_field' => $_POST['ss_screenp_qty_max_limit_field'],
                'ss_amountof_color' => $_POST['ss_amountof_color'],
                'ss_discount_in_per' => $_POST['ss_screenp_discount_in_per'],
                'date' => date("m/d/Y"),
            ),
            array('id' => $sid)
        );
        if ($update_screenp) {
            echo "<h2 class='notis'>Record Updated Successfully</h2>";
            header('Location: ' . admin_url() . 'admin.php?page=discount-for-screen-print-cart-product'); //$_SERVER['HTTP_REFERER']
            exit;
        }
    }
} else {
    $ss_min_qty_field = "";
    $ss_max_qty_field = "";
    $ss_discount_in_per = "";
    $ss_amountof_color = "";
}

/////////////////////////// DELETE DATA INOT DATABASE/////////////////////////////
if ($_GET['action'] == 'delete') {
    $delete_screenp = $wpdb->delete($table_screen_print, array('id' => $_GET['id']));
    if ($delete_screenp) {
        header('Location: ' . admin_url() . 'admin.php?page=discount-for-screen-print-cart-product'); //$_SERVER['HTTP_REFERER']
        exit;
    }
}
?>
<div class="qty-base-price-wrap">
    <div class="globally-discount-rule-left">
        <h1>Quantity based Pricing For Screen Print At Cart </h1>

        <form method="post">
            <table class="form-table ss_qty_base_dis_tab">
                <tr valign="top">
                    <th scope="row">QTY Min Limit</th>
                    <td><input type="number" required name="ss_screenp_qty_min_limit_field" placeholder="Add Qty like: 100" value="<?php echo $ss_min_qty_field; ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">QTY Max Limit</th>
                    <td><input type="number" required name="ss_screenp_qty_max_limit_field" placeholder="Add Qty like: 500" value="<?php echo $ss_max_qty_field; ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Add Price</th>
                    <td><input type="text" required name="ss_screenp_discount_in_per" placeholder="Add Comma Seprated price like: 12,65,200" value="<?php echo $ss_discount_in_per; ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Amount of color</th>
                    <td><input type="number" required name="ss_amountof_color" placeholder="Add amount of color i.e 3" value="<?php echo $ss_amountof_color; ?>" /></td>
                </tr>

            </table>

            <?php
            if ($_GET['action'] == "edit") {
                echo '<p class="submit"><input type="submit" name="ss_update" id="submit" class="button button-primary" value="Save Changes"></p>';
            } else {
                submit_button();
            }
            ?>

        </form>
    </div>


    <table class="fetchin-data">
        <tr>
            <th>Min QTY</th>
            <th>Max QTY</th>
            <th>Prices</th>
            <th>Amount of color  <!-- class="select2-selection--multiple" -->
                <!-- <li class="select2-selection__choice"><span id="select2-ss_product_tages-container-choice-n7bp-29">gildon</span></li>
                <li class="select2-selection__choice"><span id="select2-ss_product_tages-container-choice-n7bp-29">gildon</span></li> -->
            </th>
            <th>Update</th>
        </tr>
        <?php
        global $wpdb;

        // $table_screen_print = $wpdb->prefix."ss_wc_product_discount";
        $result_screen_print = $wpdb->get_results("SELECT * FROM $table_screen_print ORDER BY ABS(ss_min_qty_field)");
        // echo "<pre>";
        // print_r($result_screen_print);

        foreach ($result_screen_print as $key => $records_screen_p) {
        ?>
            <tr>
                <td><?php echo $records_screen_p->ss_min_qty_field; ?></td>
                <td><?php echo $records_screen_p->ss_max_qty_field; ?></td>
                <td><?php foreach (explode(",",$records_screen_p->ss_discount_in_per) as $array_walk_value) { echo "<span class='screen-print-prices'>".$array_walk_value."</span>"; }; ?></td>
                <td><?php echo $records_screen_p->ss_amountof_color; ?></td>
                <td>
                    <a href="<?php echo $_SERVER['REQUEST_URI'] . '&action=edit&id=' . $records_screen_p->id; ?>"><button name="save" class="button-primary" type="submit" value="Save Limit">Edit</button></a>
                    <a href="<?php echo $_SERVER['REQUEST_URI'] . '&action=delete&id=' . $records_screen_p->id; ?>" onclick="return confirm('Are you sure.You want to delete?')"><button name="save" class="button delete-theme" type="submit" value="Save Limit">Delete</button></a>
                </td>
                <!-- <td>Germany</td> -->
            </tr>
        <?php }
        ?>

    </table>
</div>