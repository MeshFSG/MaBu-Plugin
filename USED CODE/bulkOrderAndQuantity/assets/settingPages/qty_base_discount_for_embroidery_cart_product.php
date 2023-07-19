<?php
global $wpdb;
$table_embroidery = $wpdb->prefix . "ss_embroidery_cart_product";
$get_min_col_emb = get_min_max("ss_min_qty",$table_embroidery);
$get_max_col_emb = get_min_max("ss_max_qty",$table_embroidery);

/////////////////////////// INSERT DATA INOT DATABASE/////////////////////////////
// echo "<pre>";
// print_r($_POST);die();
if ( !empty($_POST['submit']) && !empty($_POST['discounted_product_min_limit']) && !empty($_POST['discounted_product_max_limit']) && !empty($_POST['discounted_price'])) {
	if($_POST['discounted_product_min_limit'] < $get_min_col_emb->ss_min && $_POST['discounted_product_max_limit'] < $get_min_col_emb->ss_min || $_POST['discounted_product_min_limit'] > $get_max_col_emb->ss_max && $_POST['discounted_product_max_limit'] > $get_max_col_emb->ss_max ) {
		$inserted_embroidery = $wpdb->insert(
			$table_embroidery,
			array(
				'ss_min_qty' => $_POST['discounted_product_min_limit'],
				'ss_max_qty' => $_POST['discounted_product_max_limit'],
				'ss_discount_in_per' => $_POST['discounted_price'],
				'date' => date("m/d/Y"),
			)
		);

		if ($inserted_embroidery) echo "<h2 class='notis'>Record Inserted Successfully</h2>";
	} else { echo "<script> alert('limits are incorect.'); </script>";}
}
/////////////////////////// UPDATE DATA INOT DATABASE/////////////////////////////
// if (!empty($_POST['hidden_edit_id']) && !empty($_POST['discounted_product_min_limit']) && !empty($_POST['discounted_product_max_limit']) && !empty($_POST['discounted_price'])) {
if ($_GET['action']) {

    $ss_edit_limit = $wpdb->get_row("SELECT * FROM $table_embroidery WHERE id =" . $_GET['ss_range_id']);

    $edit_discounted_min_limit = $ss_edit_limit->ss_min_qty;
    $edit_discounted_max_limit = $ss_edit_limit->ss_max_qty;
    $edit_discounted_price = $ss_edit_limit->ss_discount_in_per;
    $edit_id = $ss_edit_limit->id;

    if (isset($_POST['ss_update']) && !empty($_POST['discounted_product_min_limit']) && !empty($_POST['discounted_product_max_limit'])) {
        $update_responce = $wpdb->update(
            $table_embroidery,
            array(
                'ss_min_qty' => $_POST['discounted_product_min_limit'],
                'ss_max_qty' => $_POST['discounted_product_max_limit'],
                'ss_discount_in_per' => $_POST['discounted_price']
            ),
            array('id' => $edit_id)
        );
        if ($update_responce) {
            echo "<h2 class='notis'>Record Updated Successfully</h2>";
            header('Location: ' . admin_url() . 'admin.php?page=discount-for-embroidery-cart-product'); //$_SERVER['HTTP_REFERER']
            exit;
        }
    }
} else {
    $edit_discounted_min_limit = '';
    $edit_discounted_max_limit = '';
    $edit_discounted_price = '';
    $edit_id = '';
}

/////////////////////////// DELETE DATA INOT DATABASE/////////////////////////////
if ($_GET['action'] == 'delete') {
    $ss_db_responce = $wpdb->delete($table_embroidery, array('id' => $_GET['ss_range_id']));
    if ($ss_db_responce) {
        header('Location: ' . admin_url() . 'admin.php?page=discount-for-embroidery-cart-product');
        exit;
    }
}

?>
<div class="qty-base-price-wrap">
    <h1>Quantity based Pricing For Embroidery At Cart </h1>
    <form method="post" id="wholesale-product-range-Price" action="" enctype="multipart/form-data">
        <table class="form-table ss_qty_base_dis_tab">
            <tr valign="top">
                <th scope="row">Min Qty</th>
                <td> <input required name="discounted_product_min_limit" id="discounted_product_min_limit" type="number" value="<?php echo $edit_discounted_min_limit; ?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Max Qty</th>
                <td> <input required name="discounted_product_max_limit" id="discounted_product_max_limit" type="number" value="<?php echo $edit_discounted_max_limit; ?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Add Price</th>
                <td> <input required name="discounted_price" id="discounted_price" type="number" step="any" value="<?php echo $edit_discounted_price; ?>" placeholder="Add price like: 12"></td>
            </tr>

        </table>
        <?php
        if ($_GET['action'] == "edit") {
            echo '<p class="submit"><input type="submit" name="ss_update" id="submit" class="button button-primary" value="Save Changes"></p>';
        } else {
            submit_button();
        }
        ?>
        <!-- <p><button name="save" class="button-primary" type="submit" value="Save Limit">Save changes</button></p> -->
    </form>
</div>
<br>
<style>
    .fetchin-data {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    .fetchin-data td,
    .fetchin-data th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    .fetchin-data tr:nth-child(even) {
        background-color: #dddddd;
    }
</style>
<?php


?>
<table class="fetchin-data">
    <thead>
        <tr>
            <th>Min Qty</th>
            <th>Max Qty</th>
            <th>Price</th>
            <th>Update</th>
        </tr>
        <?php
        $all_product_limites_result = $wpdb->get_results("SELECT * FROM $table_embroidery ORDER BY ABS(ss_min_qty)");
        if (count($all_product_limites_result) > 0) {
            foreach ($all_product_limites_result as $key => $value) {
        ?>
                <tr>
                    <!-- <td></td> -->
                    <td><?php echo $value->ss_min_qty; ?></td>
                    <td><?php echo $value->ss_max_qty; ?></td>
                    <td><?php echo $value->ss_discount_in_per; ?></td>
                    <td>
                        <a href="<?php echo $_SERVER['REQUEST_URI'] . '&action=edit&ss_range_id=' . $value->id; ?>"><button name="save" class="button-primary" type="submit" value="Save Limit">Edit</button></a>
                        <a href="<?php echo $_SERVER['REQUEST_URI'] . '&action=delete&ss_range_id=' . $value->id; ?>" onclick="return confirm('Are you sure.You want to delete?')"><button name="save" class="button delete-theme" type="submit" value="Save Limit">Delete</button></a>
                    </td>
                </tr>
        <?php }
        } else {
            echo "<tr><td class='woocommerce-table__empty-item' colspan='12'>No data to display</td></tr>";
        } ?>
        <thead>
</table>