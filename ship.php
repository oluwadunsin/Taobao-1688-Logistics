<?php require_once('header.php'); ?>

<div class="page-banner" style="background-image: url(assets/uploads/<?php echo $banner_ship; ?>);">
    <div class="inner">
        <h1>Ship For Me</h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">            
            <div class="col-md-12">
                <h3>Ship for Me Form</h3>
                <div class="row cform">
                    <div class="col-md-8">
                        <div class="well well-sm">
                            
<?php
        // After form submit checking everything for email sending
        if(isset($_POST['form_ship']))
        {
            $error_message = '';
            $success_message = '';
            $valid = 1;

            if(empty($_POST['ship_link']))
            {
                $valid = 0;
                $error_message .= 'Please enter the picture link.\n';
            }

            if(empty($_POST['ship_destination']))
            {
                $valid = 0;
                $error_message .= 'Please enter your shipping destination.\n';
            }


            if(empty($_POST['ship_type']))
            {
                $valid = 0;
                $error_message .= 'Please enter your package type.\n';
            }

            if(empty($_POST['ship_weight']))
            {
                $valid = 0;
                $error_message .= 'Please enter your package weight.\n';
            }

            if($valid == 1)
            {
                
                $ship_link = strip_tags($_POST['ship_link']);
                $ship_destination = strip_tags($_POST['ship_destination']);
                $ship_type = strip_tags($_POST['ship_type']);
                $ship_weight = strip_tags($_POST['ship_weight']);
                $ship_length = strip_tags($_POST['ship_length']);
                $ship_width = strip_tags($_POST['ship_width']);
                $ship_height = strip_tags($_POST['ship_height']);
                $ship_message = strip_tags($_POST['ship_message']);

                // sending email
                $to_admin = $receive_email;
                $subject = 'Shipping Request';
                $message = '
        <html><body>
        <table>
        <tr>
        <td>Picture Link</td>
        <td>'.$ship_link.'</td>
        </tr>
        <tr>
        <td>Destination</td>
        <td>'.$ship_destination.'</td>
        </tr>
        <tr>
        <td>Type</td>
        <td>'.$ship_type.'</td>
        </tr>
        <td>Weight</td>
        <td>'.$ship_weight.'</td>
        </tr>
        <td>Length</td>
        <td>'.$ship_length.'</td>
        </tr>
        <td>Width</td>
        <td>'.$ship_width.'</td>
        </tr>
        <td>Height</td>
        <td>'.$ship_height.'</td>
        </tr>
        <tr>
        <td>Message</td>
        <td>'.nl2br($ship_message).'</td>
        </tr>
        </table>
        </body></html>
        ';
               
                
                try {
        		    
        	    $mail->setFrom($_SESSION['customer']['cust_email'], $_SESSION['customer']['cust_name']);
        	    $mail->addAddress($to_admin);
        	    $mail->addReplyTo($_SESSION['customer']['cust_email'], $_SESSION['customer']['cust_name']);
        	    
        	    $mail->isHTML(true);
        	    $mail->Subject = $subject;

        	    $mail->Body = $message;
        	    $mail->send();
                    
                $mail->setFrom($to_admin, $site_name." Admin");
                $mail->addAddress($_SESSION['customer']['cust_email']);
                $mail->addReplyTo($to_admin, $site_name." Admin");
                
                $mail->isHTML(true);
                $mail->Subject = "Your ".$subject;

                $mail->Body = $message;
                $mail->send();

                $statement = $pdo->prepare("INSERT INTO tbl_user_mail (subject,message,time_sent,sender,receiver,status) VALUES (?,?,?,?,?,?)");
                $statement->execute(array($subject,$message,time(),$_SESSION['customer']['cust_id'],1,0));

                $statement = $pdo->prepare("INSERT INTO tbl_customer_mail (subject,message,time_sent,sender,receiver,status) VALUES (?,?,?,?,?,?)");
                $statement->execute(array($subject,$message,time(),$_SESSION['customer']['cust_id'],1,0));

        	    $success_message = $receive_email_thank_you_message;
        	} catch (Exception $e) {
        	    echo 'Message could not be sent.';
        	    echo 'Mailer Error: ' . $mail->ErrorInfo;
        	}
                
                

            }
        }
?>
                
                <?php
                if($error_message != '') {
                    echo "<script>alert('".$error_message."')</script>";
                }
                if($success_message != '') {
                    echo "<script>alert('".$success_message."')</script>";
                }
                ?>


                            <form action="" method="post">
                            <?php $csrf->echoInputField(); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ship_link">Picture Link</label>
                                        <input type="text" class="form-control" name="ship_link" placeholder="Enter link" required="">
                                    </div>
                                    <div class="form-group">
                                        <label for="ship_type">Package Type</label>
                                        <select class="form-control" name="ship_type" required="">
                                            <option value="Ordianary">Ordinary Goods</option>
                                            <option value="Dangerous">Dangerous Goods</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="ship_weight">Package weight (KG)</label>
                                        <input type="text" class="form-control" name="ship_weight" placeholder="Enter Package weight" required="">
                                    </div>
                                    <div class="form-group">
                                        <label for="ship_length">Package Length</label>
                                        <input type="text" class="form-control" name="ship_length" placeholder="Enter Package length">
                                    </div>
                                    <div class="form-group">
                                        <label for="ship_width">Package Width</label>
                                        <input type="text" class="form-control" name="ship_width" placeholder="Enter Package width">
                                    </div>
                                    <div class="form-group">
                                        <label for="ship_height">Package Height</label>
                                        <input type="text" class="form-control" name="ship_height" placeholder="Enter Package height">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ship_destination">Destination Address</label>
                                        <textarea name="ship_destination" class="form-control" rows="3" cols="25" placeholder="Enter destination address" required=""></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="ship_message">Additional Info</label>
                                        <textarea name="ship_message" class="form-control" rows="9" cols="25" placeholder="Enter message"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <input type="submit" value="Send Request" class="btn btn-primary pull-right" name="form_ship">
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <legend><span class="glyphicon glyphicon-globe"></span> Our office</legend>
                        <address>
                            <?php echo nl2br($contact_address); ?>
                        </address>
                        <address>
                            <strong>Phone:</strong><br>
                            <span><?php echo $contact_phone; ?></span>
                        </address>
                        <address>
                            <strong>Email:</strong><br>
                            <a href="mailto:<?php echo $contact_email; ?>"><span><?php echo $contact_email; ?></span></a>
                        </address>
                    </div>
                </div>

                <h3>Find Us On Map</h3>
                <?php echo $contact_map_iframe; ?>
                
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>