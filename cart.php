<?php require_once('header.php'); ?>
<?php
    // Check if the customer is logged in or not
    if(!isset($_SESSION['customer'])) {
        header('location: '.BASE_URL.'logout.php');
        exit;
    }
?>

<?php
    $error_message = '';
    if(isset($_POST['form1'])) {

                $i = 0;
                $statement = $pdo->prepare("SELECT * FROM tbl_product");
                $statement->execute();
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                foreach ($result as $row) {
                    $i++;
                    $table_product_id[$i] = $row['p_id'];
                    $table_quantity[$i] = $row['p_qty'];
                }

                $i=0;
                foreach($_POST['product_id'] as $val) {
                    $i++;
                    $arr1[$i] = $val;
                }

                $i=0;
                foreach($_POST['product_name'] as $val) {
                    $i++;
                    $arr3[$i] = $val;
                }

                $i=0;
                foreach($_POST['quantity'] as $val) {
                    $i++;
                    $arr2[$i] = $val;
                }
                
                $i=0;
                foreach($_POST['product_type'] as $val) {
                    $i++;
                    $arr4[$i] = $val;
                }
        
                $allow_update = 1;
                for($i=1;$i<=count($arr1);$i++) {
                    if($arr4[$i] != 1){
                        for($j=1;$j<=count($table_product_id);$j++) {
                            if($arr1[$i] == $table_product_id[$j]) {
                                $temp_index = $j;
                                break;
                            }
                        }

                        if($table_quantity[$temp_index] < $arr2[$i]) {
                        	$allow_update = 0;
                            $error_message .= '"'.$arr2[$i].'" items are not available for "'.$arr3[$i].'"\n';
                        } else {
                            $_SESSION['cart_p_qty'][$i] = $arr2[$i];
                        }
                    }else $_SESSION['cart_p_qty'][$i] = $arr2[$i];
                }

        $error_message .= '\nOther items quantity are updated successfully!';
?>
        
        <?php if($allow_update == 0): ?>
        	<script>alert('<?php echo $error_message; ?>');</script>
    	<?php else: ?>
    		<script>alert('All Items Quantity Update is Successful!');</script>
    	<?php endif; ?>
        <?php } ?>

<div class="page-banner" style="background-image: url(assets/uploads/<?php echo $banner_cart; ?>)">
    <div class="overlay"></div>
    <div class="page-banner-inner">
        <h1><?php echo LANG_VALUE_18; ?></h1>
    </div>
</div>

<div class="page">
	<div class="container">
		<div class="row">
			<div class="col-md-12">

                <?php if(!isset($_SESSION['cart_p_id'])): ?>
                    <?php echo 'Cart is empty'; ?>
                <?php else: ?>
                <form action="" method="post">
                    <?php $csrf->echoInputField(); ?>
				<div class="cart">
                    <table class="table table-responsive">
                        <tr>
                            <th><?php echo LANG_VALUE_7; ?></th>
                            <th><?php echo LANG_VALUE_8; ?></th>
                            <th><?php echo LANG_VALUE_47; ?></th>
                            <th><?php echo LANG_VALUE_157; ?></th>
                            <th><?php echo LANG_VALUE_158; ?></th>
                            <th><?php echo LANG_VALUE_159; ?></th>
                            <th>Custom</th>
                            <th><?php echo LANG_VALUE_55; ?></th>
                            <th class="text-right"><?php echo LANG_VALUE_82; ?></th>
                            <th class="text-center" style="width: 100px;"><?php echo LANG_VALUE_83; ?></th>
                        </tr>
                        <?php
                        $table_total_price = 0;

                        $i=0;
                        foreach($_SESSION['cart_p_id'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_id[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_size_id'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_size_id[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_size_name'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_size_name[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_color_id'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_color_id[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_color_name'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_color_name[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_qty'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_qty[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_cust_val'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_cust_val[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_type'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_type[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_current_price'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_current_price[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_name'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_name[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_featured_photo'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_featured_photo[$i] = $value;
                        }
                        ?>
                        <?php for($i=1;$i<=count($arr_cart_p_id);$i++): ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td>
                                <?php if($arr_cart_p_type[$i] == 0) { ?>
                                    <img src="assets/uploads/<?php echo $arr_cart_p_featured_photo[$i]; ?>" alt="">
                                <?php } ?>
                                <?php if($arr_cart_p_type[$i] == 1) { ?>
                                    <img src="<?php echo $arr_cart_p_featured_photo[$i]; ?>" alt="" height="100px" width="100px">
                                <?php } ?>
                            </td>
                            <td><?php echo $arr_cart_p_name[$i]; ?></td>
                            <td><?php echo $arr_cart_size_name[$i]; ?></td>
                            <td><?php echo $arr_cart_color_name[$i]; ?></td>
                            <td><?php echo $site_currency." ".$arr_cart_p_current_price[$i]; ?></td>
                                <?php 
                                    $bout = "" ;
                                    if($arr_cart_cust_val[$i]['link'] != "") $bout .= $arr_cart_cust_val[$i]['link'].'<br>';
                                    if($arr_cart_cust_val[$i]['custom'] != "") $bout .= $arr_cart_cust_val[$i]['custom'].'<br>';
                                    if($arr_cart_cust_val[$i]['comment'] != "") $bout .= $arr_cart_cust_val[$i]['comment'].'<br>';
                                ?>
                            <td><?php echo $bout; ?></td>
                            <td>
                                <input type="hidden" name="product_id[]" value="<?php echo $arr_cart_p_id[$i]; ?>">
                                <input type="hidden" name="product_name[]" value="<?php echo $arr_cart_p_name[$i]; ?>">
                                <input type="hidden" name="product_type[]" value="<?php echo $arr_cart_p_type[$i]; ?>">
                                <input type="number" class="input-text qty text" step="1" min="1" max="" name="quantity[]" value="<?php echo $arr_cart_p_qty[$i]; ?>" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric">
                            </td>
                            <td class="text-right">
                                <?php
                                    $row_total_price = $arr_cart_p_current_price[$i]*$arr_cart_p_qty[$i];
                                    $table_total_price = $table_total_price + $row_total_price;
                                ?>
                                <?php echo $site_currency." ".$row_total_price; ?>
                            </td>
                            <td class="text-center">
                                <a onclick="return confirmDelete();" href="cart-item-delete.php?id=<?php echo $arr_cart_p_id[$i]; ?>&size=<?php echo $arr_cart_size_id[$i]; ?>&color=<?php echo $arr_cart_color_id[$i]; ?>" class="trash"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endfor; ?>
                        <tr>
                            <th colspan="7" class="total-text">Sub-Total</th>
                            <th class="total-amount"><?php echo $site_currency." ".$table_total_price; ?></th>
                            <th></th>
                        </tr>
                    </table> 
                </div>

                <div class="cart-buttons">
                    <ul>
                        <li><input type="submit" value="<?php echo LANG_VALUE_20; ?>" class="btn btn-primary" name="form1"></li>
                        <li><a href="index.php" class="btn btn-primary"><?php echo LANG_VALUE_85; ?></a></li>
                        <li><a href="checkout.php" class="btn btn-primary"><?php echo LANG_VALUE_23; ?></a></li>
                    </ul>
                </div>
                </form>
                <?php endif; ?>

                

			</div>
		</div>
	</div>
</div>


<?php 
    require_once('footer.php');
?>