<?php require_once('header.php'); ?>

<?php
if(!isset($_SESSION['cart_p_id'])) {
    header('location: cart.php');
    exit;
}
?>

<div class="page-banner" style="background-image: url(assets/uploads/<?php echo $banner_checkout; ?>)">
    <div class="overlay"></div>
    <div class="page-banner-inner">
        <h1><?php echo LANG_VALUE_22; ?></h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                
                <?php if(!isset($_SESSION['customer'])): ?>
                    <p>
                        <a href="login.php" class="btn btn-md btn-danger"><?php echo LANG_VALUE_160; ?></a>
                    </p>
                <?php else: ?>

                <h3 class="special"><?php echo LANG_VALUE_26; ?></h3>
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
                        foreach($_SESSION['cart_p_qty'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_qty[$i] = $value;
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
                        <?php
                            $product_links_array = array();
                            $total_shipping_cost = 0;
                            $total_pro_status = false;
                            $statement = $pdo->prepare("SELECT * FROM tbl_shipping_cost WHERE country_id=?");
                            $statement->execute(array($_SESSION['customer']['cust_country']));
                            $total = $statement->rowCount();
                            if($total) {
                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($result as $row) {
                                    $shipping_cost = $row['amount'];
                                }
                            } else {
                                $statement = $pdo->prepare("SELECT * FROM tbl_shipping_cost_all WHERE sca_id=1");
                                $statement->execute();
                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($result as $row) {
                                    $shipping_cost = $row['amount'];
                                }
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
                                    if($arr_cart_cust_val[$i]['link'] != "") {
                                        $bout .= $arr_cart_cust_val[$i]['link'].'<br>';
                                        array_push($product_links_array,$arr_cart_cust_val[$i]['link']); //push all links to an array for comparison
                                    }
                                    if($arr_cart_cust_val[$i]['custom'] != "") $bout .= $arr_cart_cust_val[$i]['custom'].'<br>';
                                    if($arr_cart_cust_val[$i]['comment'] != "") $bout .= $arr_cart_cust_val[$i]['comment'].'<br>';
                                ?>
                            <td><?php echo $bout; ?></td>
                            <td><?php echo $arr_cart_p_qty[$i]; ?></td>
                            <td class="text-right">
                                <?php
                                    $row_total_price = $arr_cart_p_current_price[$i]*$arr_cart_p_qty[$i];
                                    $table_total_price = $table_total_price + $row_total_price;
                                    if($arr_cart_p_type[$i] == 0){
                                         $total_pro_status = true;
                                         $total_shipping_cost += $shipping_cost; 
                                    }
                                    if($total_pro_status)$total_shipping_cost += 0;
                                    else $total_shipping_cost += $arr_cart_cust_val[$i]['shipping'];

                                    echo $site_currency." ".number_format($row_total_price,2);
                                ?>
                            </td>
                        </tr>
                        <?php endfor; ?>  
                        <!-- get and calculate shipping fess here -->
                            <?php 
                                
                                
                            
                                    $combined_links = array_unique($product_links_array); 
                                    $product_qtys_array = array();
                                    $mycount = count($combined_links);
                                    for($r=0; $r<count($combined_links); $r++){ 
                                        if(!array_key_exists($r,$combined_links))continue;
                                        $product_qtys_array[$r]=0;
                                        for($t=1; $t<count($arr_cart_cust_val); $t++){
                                            if($arr_cart_cust_val[$t]['link'] == $combined_links[$r]){
                                                $product_qtys_array[$r] += $arr_cart_p_qty[$t];
                                            }
                                        }
                                    }
                                    //use the fee api
                                    for($r=0; $r<count($combined_links); $r++){
                                        if(!array_key_exists($r,$combined_links))continue;
                                        $link = $combined_links[$r];
                                            if(preg_match("/taobao.com|tb.cn/i", $link) > 0){
                                                $pattern = '/id=|sm=/i';
                                                preg_match("/(id=|sm=)\w+/i", $link,$result);
                                                if(count($result) > 0){
                                                     $res = preg_replace($pattern,"",$result);   
                                                     $product_id = $res[0];
                                                     $product_type = 'taobao';
                                                     $area_id = $area_id_taobao;
                                                }
                                            }else if(preg_match("/1688.com/i", $link) > 0){
                                                preg_match("/\w+(?=\.html)/i", $link,$result);
                                                if(count($result) > 0){
                                                    $product_id = $result[0];
                                                    $product_type = '1688';
                                                    $area_id = $area_id_1688;
                                                }
                                            }
                                        try{ 
                                            $url_shipping = "https://api.onebound.cn/".$product_type."/api_call.php?key=".$import_api."&secret=".$import_key."&api_name=item_fee&num_iid=".$product_id."&area_id=".$area_id."&lang=en&result_type=json&num=".$product_qtys_array[$r]."&cache=no";
                                            
                                            $shipping_response = urldecode(html_entity_decode(file_get_contents($url_shipping)));
                                            $shipping_info = json_decode($shipping_response,true); 
                                            //echo $url_shipping.'<br>';
                                            //print_r($shipping_response);
                        
                                            if($shipping_info['error'] != null && $shipping_info['error'] !=  "data error,no cache" && $shipping_info['error_code'] !=  5000){
                                                // there was an error from the API
                                                throw new Exception('Shipping API returned error: ' . $shipping_info['error']." ".$shipping_info['reason']." ".$shipping_info['error_code']);
                                            }
                        
                                            $shipping = $shipping_info['item'];
                                            if($shipping_info['error'] ==  "data error,no cache" || $shipping_info['error_code'] ==  5000)  $shipping_fee = $backup_express_fee * $mycount;
                                            else if ($shipping['express_fee'] == "") $shipping_fee = $backup_express_fee * $mycount;
                                            else if ($shipping['express_fee'] === 0) $shipping_fee = $shipping['express_fee'] * $ngn_yen;
                                            else $shipping_fee = $shipping['express_fee'] * $ngn_yen;
                                            $total_shipping_cost = $shipping_fee;
                                        } catch (Exception $e) {
                                            $error = $e->getMessage();
                                            echo '<script type="text/javascript">alert("Error: '.$error.'");
                                                    window.open(window.location.href,"_self");
                                                </script>';
                                        }
                                    }
                            ?>
                        <tr class="checkoutDesktop">
                            <th colspan="7" class="total-text"><?php echo LANG_VALUE_81; ?></th>
                            <th class="total-amount"><?php echo $site_currency." ".number_format($table_total_price,2); ?></th>
                        </tr>
                        <tr class="checkoutDesktop">
                            <td colspan="7" class="total-text"><?php echo LANG_VALUE_84; ?></td>
                            <td class="total-amount"><?php echo $site_currency." ".number_format($total_shipping_cost,2); ?></td>
                        </tr>
                        <tr class="checkoutDesktop">
                            <td colspan="7" class="total-text">Commission</td>
                            <?php $total_commission = ($commission/100) * ($table_total_price + $total_shipping_cost); ?>
                            <td class="total-amount"><?php echo $site_currency." ".number_format($total_commission,2); ?></td>
                        </tr>
                        <tr class="checkoutDesktop">
                            <th colspan="7" class="total-text"><?php echo LANG_VALUE_82; ?></th>
                            <th class="total-amount">
                                <?php
                                    $final_total = ceil($table_total_price+$total_shipping_cost+$total_commission);
                                    echo $site_currency." ".number_format($final_total,2);
                                ?>
                            </th>
                        </tr>
                        <tr class="checkoutMobile">
                            <th class="total-text"><?php echo LANG_VALUE_81; ?></th>
                            <th colspan="3" class="total-amount"><?php echo $site_currency." ".number_format($table_total_price,2); ?></th>
                        </tr>
                        <tr class="checkoutMobile">
                            <td class="total-text"><?php echo LANG_VALUE_84; ?></td>
                            <td  colspan="3" class="total-amount"><?php echo $site_currency." ".number_format($total_shipping_cost, 2); ?></td>
                        </tr>
                        <tr class="checkoutMobile">
                            <td class="total-text">Commission</td>
                            <?php $total_commission = ($commission/100) * ($table_total_price + $total_shipping_cost); ?>
                            <td  colspan="3"  class="total-amount"><?php echo $site_currency." ".number_format($total_commission,2); ?></td>
                        </tr>
                        <tr class="checkoutMobile">
                            <th class="total-text"><?php echo LANG_VALUE_82; ?></th>
                            <th  colspan="3"  class="total-amount">
                                <?php
                                    $final_total = ceil($table_total_price+$total_shipping_cost+$total_commission);
                                    echo $site_currency." ".number_format($final_total,2);
                                ?>
                            </th>
                        </tr>
                    </table> 
                </div>

                

                <div class="billing-address">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="special"><?php echo LANG_VALUE_161; ?></h3>
                            <table class="table table-responsive table-bordered bill-address">
                                <tr>
                                    <td><?php echo LANG_VALUE_102; ?></td>
                                    <td><?php echo $_SESSION['customer']['cust_b_name']; ?></p></td>
                                </tr>
                                <tr>
                                    <td><?php echo LANG_VALUE_103; ?></td>
                                    <td><?php echo $_SESSION['customer']['cust_b_cname']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo LANG_VALUE_104; ?></td>
                                    <td><?php echo $_SESSION['customer']['cust_b_phone']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo LANG_VALUE_106; ?></td>
                                    <td>
                                        <?php echo $_SESSION['customer']['cust_b_country']; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo LANG_VALUE_105; ?></td>
                                    <td>
                                        <?php echo nl2br($_SESSION['customer']['cust_b_address']); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo LANG_VALUE_107; ?></td>
                                    <td><?php echo $_SESSION['customer']['cust_b_city']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo LANG_VALUE_108; ?></td>
                                    <td><?php echo $_SESSION['customer']['cust_b_state']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo LANG_VALUE_109; ?></td>
                                    <td><?php echo $_SESSION['customer']['cust_b_zip']; ?></td>
                                </tr>                                
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h3 class="special"><?php echo LANG_VALUE_162; ?></h3>
                            <table class="table table-responsive table-bordered bill-address">
                                <tr>
                                    <td><?php echo LANG_VALUE_102; ?></td>
                                    <td><?php echo $_SESSION['customer']['cust_s_name']; ?></p></td>
                                </tr>
                                <tr>
                                    <td><?php echo LANG_VALUE_103; ?></td>
                                    <td><?php echo $_SESSION['customer']['cust_s_cname']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo LANG_VALUE_104; ?></td>
                                    <td><?php echo $_SESSION['customer']['cust_s_phone']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo LANG_VALUE_106; ?></td>
                                    <td>
                                        <?php echo $_SESSION['customer']['cust_s_country']; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo LANG_VALUE_105; ?></td>
                                    <td>
                                        <?php echo nl2br($_SESSION['customer']['cust_s_address']); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo LANG_VALUE_107; ?></td>
                                    <td><?php echo $_SESSION['customer']['cust_s_city']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo LANG_VALUE_108; ?></td>
                                    <td><?php echo $_SESSION['customer']['cust_s_state']; ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo LANG_VALUE_109; ?></td>
                                    <td><?php echo $_SESSION['customer']['cust_s_zip']; ?></td>
                                </tr> 
                            </table>
                        </div>
                    </div>                    
                </div>

                

                <div class="cart-buttons">
                    <ul>
                        <li><a href="cart.php" class="btn btn-primary"><?php echo LANG_VALUE_21; ?></a></li>
                    </ul>
                </div>

				<div class="clear"></div>
                <h3 class="special"><?php echo LANG_VALUE_33; ?></h3>
                <div class="row">
                    
                    	<?php
		                $checkout_access = 1;
		                if(
		                    ($_SESSION['customer']['cust_b_name']=='') ||
		                    ($_SESSION['customer']['cust_b_phone']=='') ||
		                    ($_SESSION['customer']['cust_b_country']=='') ||
		                    ($_SESSION['customer']['cust_b_address']=='') ||
		                    ($_SESSION['customer']['cust_b_city']=='') ||
		                    ($_SESSION['customer']['cust_b_state']=='') ||
		                    ($_SESSION['customer']['cust_b_zip']=='') ||
		                    ($_SESSION['customer']['cust_s_name']=='') ||
		                    ($_SESSION['customer']['cust_s_phone']=='') ||
		                    ($_SESSION['customer']['cust_s_country']=='') ||
		                    ($_SESSION['customer']['cust_s_address']=='') ||
		                    ($_SESSION['customer']['cust_s_city']=='') ||
		                    ($_SESSION['customer']['cust_s_state']=='') ||
		                    ($_SESSION['customer']['cust_s_zip']=='')
		                ) {
		                    $checkout_access = 0;
		                }
		                ?>
		                <?php if($checkout_access == 0): ?>
		                	<div class="col-md-12">
				                <div style="color:red;font-size:22px;margin-bottom:50px;">
			                        You must have to fill up all the billing and shipping information from your dashboard panel in order to checkout the order. Please fill up the information going to <a href="customer-billing-shipping-update.php" style="color:red;text-decoration:underline;">this link</a>.
			                    </div>
	                    	</div>
	                	<?php else: ?>
		                	<div class="col-md-4">
		                		
	                            <div class="row">

	                                <div class="col-md-12 form-group">
	                                    <label for=""><?php echo LANG_VALUE_34; ?> *</label>
	                                    <select name="payment_method" class="form-control select2" id="advFieldsStatus">
	                                        <option value=""><?php echo LANG_VALUE_35; ?></option>
	                                        <?php if($allowed_paypal){ ?><option value="PayPal"><?php echo LANG_VALUE_36; ?></option><?php } ?>
	                                        <?php if($allowed_stripe){ ?><option value="Stripe"><?php echo LANG_VALUE_37; ?></option><?php } ?>
                                            <?php if($allowed_paystack){ ?><option value="Paystack">Paystack</option><?php } ?>
	                                        <?php if($allowed_bank){ ?><option value="Bank Deposit"><?php echo LANG_VALUE_38; ?></option><?php } ?>
	                                    </select>
	                                </div>

                                    <?php if($allowed_paypal){ ?>
                                        <form class="paypal" action="<?php echo BASE_URL; ?>payment/paypal/payment_process" method="post" id="paypal_form" target="_blank">
                                            <input type="hidden" name="cmd" value="_xclick" />
                                            <input type="hidden" name="no_note" value="1" />
                                            <input type="hidden" name="lc" value="PH" />
                                            <input type="hidden" name="currency_code" value="<?php echo $site_currency; ?>" />
                                            <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest" />

                                            <input type="hidden" name="final_total" value="<?php echo $final_total; ?>">
                                            <input type="hidden" name="shipping" value="<?php echo $total_shipping_cost; ?>">
                                            <input type="hidden" name="commission" value="<?php echo $total_commission; ?>">
                                            <div class="col-md-12 form-group">
                                                <input type="submit" class="btn btn-primary" value="<?php echo LANG_VALUE_46; ?>" name="form1">
                                            </div>
                                        </form>
                                    <?php } ?>

                                    <?php if($allowed_stripe){ ?>
                                        <form action="payment/stripe/init" method="post" id="stripe_form">
                                            <input type="hidden" name="payment" value="posted">
                                            <input type="hidden" name="amount" value="<?php echo $final_total; ?>">
                                            <input type="hidden" name="shipping" value="<?php echo $total_shipping_cost; ?>">
                                            <input type="hidden" name="commission" value="<?php echo $total_commission; ?>">
                                            <div class="col-md-12 form-group">
                                                <label for=""><?php echo LANG_VALUE_39; ?> *</label>
                                                <input type="text" name="card_number" class="form-control card-number">
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label for=""><?php echo LANG_VALUE_40; ?> *</label>
                                                <input type="number" name="card_cvv" class="form-control card-cvc">
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label for=""><?php echo LANG_VALUE_41; ?> *</label>
                                                <input type="number" name="card_month" class="form-control card-expiry-month">
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label for=""><?php echo LANG_VALUE_42; ?> *</label>
                                                <input type="number" name="card_year" class="form-control card-expiry-year">
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <input type="submit" class="btn btn-primary" value="<?php echo LANG_VALUE_46; ?>" name="form2" id="submit-button">
                                                <div id="msg-container"></div>
                                            </div>
                                        </form>
                                    <?php } ?>

                                    <?php if($allowed_paystack){ ?>
                                        <form id="paystack_form">
                                          <script src="https://js.paystack.co/v1/inline.js"></script>
                                            <div class="col-md-12 form-group">
                                                <button type="button" class="btn btn-primary" onclick="payWithPaystack('<?php echo $_SESSION['customer']['cust_name']; ?>','<?php echo $_SESSION['customer']['cust_email']; ?>','<?php echo $final_total * 100; ?>','<?php echo $total_shipping_cost; ?>','<?php echo $total_commission; ?>')"> Pay </button> 
                                            </div>
                                        </form>
                                    <?php } ?>

                                    <?php if($allowed_bank){ ?>
                                        <form action="payment/bank/init" method="post" id="bank_form">
                                            <input type="hidden" name="amount" value="<?php echo $final_total; ?>">
                                            <input type="hidden" name="shipping" value="<?php echo $total_shipping_cost; ?>">
                                            <input type="hidden" name="commission" value="<?php echo $total_commission; ?>">
                                            <div class="col-md-12 form-group">
                                                <label for=""><?php echo LANG_VALUE_43; ?></span></label><br>
                                                <?php
                                                    echo nl2br(htmlspecialchars_decode($bank_detail));
                                                ?>
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label for=""><?php echo LANG_VALUE_44; ?> <br><span style="font-size:12px;font-weight:normal;">(<?php echo LANG_VALUE_45; ?>)</span></label>
                                                <textarea name="transaction_info" class="form-control" cols="30" rows="10" required></textarea>
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <input type="submit" class="btn btn-primary" value="<?php echo LANG_VALUE_46; ?>" name="form3">
                                            </div>
                                        </form>
                                    <?php } ?>
	                                
	                            </div>
		                            
		                        
		                    </div>
		                <?php endif; ?>
                        
                </div>
                

                <?php endif; ?>

            </div>
        </div>
    </div>
</div>


<?php 
    require_once('footer.php');
?>