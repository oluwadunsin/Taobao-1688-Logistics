<?php if(($newsletter_on_off == 1) && (strtolower(parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH)) != '/checkout')): ?>
<section class="home-newsletter">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="single">
					<?php
			if(isset($_POST['form_subscribe']))
			{

				if(empty($_POST['email_subscribe'])) 
			    {
			        $valid = 0;
			        $error_message1 .= LANG_VALUE_131;
			    }
			    else
			    {
			    	if (filter_var($_POST['email_subscribe'], FILTER_VALIDATE_EMAIL) === false)
				    {
				        $valid = 0;
				        $error_message1 .= LANG_VALUE_134;
				    }
				    else
				    {
				    	$statement = $pdo->prepare("SELECT * FROM tbl_subscriber WHERE subs_email=?");
				    	$statement->execute(array($_POST['email_subscribe']));
				    	$total = $statement->rowCount();							
				    	if($total)
				    	{
				    		$valid = 0;
				        	$error_message1 .= LANG_VALUE_147;
				    	}
				    	else
				    	{
				    		// Sending email to the requested subscriber for email confirmation
				    		// Getting activation key to send via email. also it will be saved to database until user click on the activation link.
				    		$key = md5(uniqid(rand(), true));

				    		// Getting current date
				    		$current_date = date('Y-m-d');

				    		// Getting current date and time
				    		$current_date_time = date('Y-m-d H:i:s');

				    		// Inserting data into the database
				    		$statement = $pdo->prepare("INSERT INTO tbl_subscriber (subs_email,subs_date,subs_date_time,subs_hash,subs_active) VALUES (?,?,?,?,?)");
				    		$statement->execute(array($_POST['email_subscribe'],$current_date,$current_date_time,$key,0));

				    		// Sending Confirmation Email
				    		$to = $_POST['email_subscribe'];
							$subject = 'Subscriber Email Confirmation';
							
							// Getting the url of the verification link
							$verification_url = BASE_URL.'verify-subscriber.php?email='.$to.'&key='.$key;

							$message = '
Thanks for your interest to subscribe our newsletter!<br><br>
Please click this link to confirm your subscription:
					'.$verification_url.'<br><br>
This link will be active only for 24 hours.
					';

							
							
							try {
		    
							    $mail->setFrom($contact_email, 'Admin');
							    $mail->addAddress($to);
							    $mail->addReplyTo($contact_email, 'Admin');
							    
							    $mail->isHTML(true);
							    $mail->Subject = $subject;
						
							    $mail->Body = $message;
							    $mail->send();
						
							    $success_message1 = LANG_VALUE_136;   
							} catch (Exception $e) {
							    echo 'Message could not be sent.';
							    echo 'Mailer Error: ' . $mail->ErrorInfo;
							}
							
							

							
				    	}
				    }
			    }
			}
			if($error_message1 != '') {
				echo "<script>alert('".$error_message1."')</script>";
			}
			if($success_message1 != '') {
				echo "<script>alert('".$success_message1."')</script>";
			}
			?>
				<form action="" method="post">
					<?php $csrf->echoInputField(); ?>
					<h2><?php echo LANG_VALUE_93; ?></h2>
					<div class="input-group">
			        	<input type="email" class="form-control" placeholder="<?php echo LANG_VALUE_95; ?>" name="email_subscribe">
			         	<span class="input-group-btn">
			         	<button class="btn btn-theme" type="submit" name="form_subscribe"><?php echo LANG_VALUE_92; ?></button>
			         	</span>
			        </div>
				</div>
				</form>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="footer-main">
	<div class="container">
		<div class="row">
		    <?if(strtolower(parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH)) != '/checkout'){ ?>
        			<div class="col-md-3 col-sm-6 footer-col">
        				<h3><?php echo LANG_VALUE_110; ?></h3>
        				<div class="row">
        					<div class="col-md-12">
        						<p>
        							<?php echo nl2br($footer_about); ?>
        						</p>
        					</div>
        				</div>
        			</div>
        			<div class="col-md-3 col-sm-6 footer-col">
        				<h3><?php echo LANG_VALUE_113; ?></h3>
        				<div class="row">
        					<div class="col-md-12">
        						<ul>
        							<?php
        				            $i = 0;
        				            $statement = $pdo->prepare("SELECT * FROM tbl_post ORDER BY post_id DESC");
        				            $statement->execute();
        				            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        				            foreach ($result as $row) {
        				                $i++;
        				                if($i > $total_recent_post_footer) {
        				                    break;
        				                }
        				                ?>
        				                <li><a href="blog-single.php?slug=<?php echo $row['post_slug']; ?>"><?php echo $row['post_title']; ?></a></li>
        				                <?php
        				            }
                   					?>
        						</ul>
        					</div>
        				</div>
        			</div>
        			<div class="col-md-3 col-sm-6 footer-col">
        				<h3><?php echo LANG_VALUE_112; ?></h3>
        				<div class="row">
        					<div class="col-md-12">
        						<ul>
        							<?php
        				            $i = 0;
        				            $statement = $pdo->prepare("SELECT * FROM tbl_post ORDER BY total_view DESC");
        				            $statement->execute();
        				            $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
        				            foreach ($result as $row) {
        				                $i++;
        				                if($i > $total_popular_post_footer) {
        				                    break;
        				                }
        				                ?>
        				                <li><a href="blog-single.php?slug=<?php echo $row['post_slug']; ?>"><?php echo $row['post_title']; ?></a></li>
        				                <?php
        				            }
        				            ?>
        						</ul>
        					</div>
        				</div>
        			</div>
			<? } ?>
			<div class="col-md-3 col-sm-6 footer-col">
				<h3><?php echo LANG_VALUE_114; ?></h3>
				<div class="contact-item">
					<div class="text"><?php echo nl2br($contact_address); ?></div>
				</div>
				<div class="contact-item">
					<div class="text"><?php echo $contact_phone; ?></div>
				</div>
				<div class="contact-item">
					<div class="text"><?php echo $contact_email; ?></div>
				</div>
			</div>

		</div>
	</div>
</section>


<div class="footer-bottom">
	<div class="container">
		<div class="row">
			<div class="col-md-12 copyright">
				<?php echo $footer_copyright; ?>
			</div>
		</div>
	</div>
</div>


<a href="#" class="scrollup">
	<i class="fa fa-angle-up"></i>
</a>

<script src="assets/js/jquery-2.2.4.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="https://js.stripe.com/v2/"></script>
<script src="assets/js/megamenu.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/owl.animate.js"></script>
<script src="assets/js/jquery.bxslider.min.js"></script>
<script src="assets/js/jquery.magnific-popup.min.js"></script>
<script src="assets/js/rating.js"></script>
<script src="assets/js/jquery.touchSwipe.min.js"></script>
<script src="assets/js/bootstrap-touch-slider.js"></script>
<script src="assets/js/select2.full.min.js"></script>
<script src="assets/js/custom.js"></script>
<script>
	function confirmDelete()
	{
	    return confirm("Do you sure want to delete this data?");
	}
	$(document).ready(function () {
		advFieldsStatus = $('#advFieldsStatus').val();

		$('#paypal_form').hide();
		$('#stripe_form').hide();
		$('#bank_form').hide();
		$('#paystack_form').hide();

        $('#advFieldsStatus').on('change',function() {
            advFieldsStatus = $('#advFieldsStatus').val();
            if ( advFieldsStatus == '' ) {
            	$('#paypal_form').hide();
				$('#stripe_form').hide();
				$('#bank_form').hide();
				$('#paystack_form').hide();
            } else if ( advFieldsStatus == 'PayPal' ) {
               	$('#paypal_form').show();
				$('#stripe_form').hide();
				$('#bank_form').hide();
				$('#paystack_form').hide();
            } else if ( advFieldsStatus == 'Stripe' ) {
               	$('#paypal_form').hide();
				$('#stripe_form').show();
				$('#bank_form').hide();
				$('#paystack_form').hide();
            } else if ( advFieldsStatus == 'Paystack' ) {
               	$('#paypal_form').hide();
				$('#stripe_form').hide();
				$('#bank_form').hide();
				$('#paystack_form').show();
            } else if ( advFieldsStatus == 'Bank Deposit' ) {
            	$('#paypal_form').hide();
				$('#stripe_form').hide();
				$('#bank_form').show();
				$('#paystack_form').hide();
            }
        });
	});


	$(document).on('submit', '#stripe_form', function () {
        // createToken returns immediately - the supplied callback submits the form if there are no errors
        $('#submit-button').prop("disabled", true);
        $("#msg-container").hide();
        Stripe.card.createToken({
            number: $('.card-number').val(),
            cvc: $('.card-cvc').val(),
            exp_month: $('.card-expiry-month').val(),
            exp_year: $('.card-expiry-year').val()
            // name: $('.card-holder-name').val()
        }, stripeResponseHandler);
        return false;
    });
    Stripe.setPublishableKey('<?php echo $stripe_public_key; ?>');
    function stripeResponseHandler(status, response) {
        if (response.error) {
            $('#submit-button').prop("disabled", false);
            $("#msg-container").html('<div style="color: red;border: 1px solid;margin: 10px 0px;padding: 5px;"><strong>Error:</strong> ' + response.error.message + '</div>');
            $("#msg-container").show();
        } else {
            var form$ = $("#stripe_form");
            var token = response['id'];
            form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
            form$.get(0).submit();
        }
    }
	 
	  function payWithPaystack(name, email, amount,shipping,commission){
	    var handler = PaystackPop.setup({
	      key: '<?php echo $paystack_key; ?>',
	      email: email,
	      amount: amount,
	      metadata: {
	         custom_fields: [
	            {
	                display_name: name,
	                shipping: shipping,
	                commission: commission
	            }
	         ]
	      },
	      callback: function(response){
	      	  window.open('<?php echo BASE_URL; ?>payment/paystack/init.php?token='+response.reference+'&email='+email,'_self');
	      },
	      onClose: function(){
	          alert('Transaction Cancelled');
	      }
	    });
	    handler.openIframe();
	  }
</script>
<?php if(isset($_SESSION['customer']))store_cart($pdo); ?>
<?php echo $before_body; ?>
</body>
</html>