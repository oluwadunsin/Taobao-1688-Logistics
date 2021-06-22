<?php

		ob_start();
		session_start();
		include("admin/inc/config.php");
		include("admin/inc/functions.php");
		include("admin/inc/CSRF_Protect.php");
		$csrf = new CSRF_Protect();

		// Getting all language variables into array as global variable
		$i=1;
		$statement = $pdo->prepare("SELECT * FROM tbl_language");
		$statement->execute();
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
		foreach ($result as $row) {
			define('LANG_VALUE_'.$i,$row['lang_value']);
			$i++;
		}
		
    if(isset($_POST['form1'])) {
            
        if(empty($_POST['cust_email']) || empty($_POST['cust_password'])) {
            $_SESSION['error_message'] = LANG_VALUE_132.'<br>';
            header("location: login.php");
        }elseif ($captcha_status == 1 && (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response']))){
                $_SESSION['error_message'] .= 'Recaptcha Not Done<br>';
                header("location: login.php");
        }else {

            if($captcha_status == 1){
                $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$captcha_secret_key.'&response='.$_POST['g-recaptcha-response']);
                $responseData = json_decode($verifyResponse);
                if($responseData->success) $recaptchaStatus = true;
                else{
                   $_SESSION['error_message'] .= 'Recaptcha verification failed, please try again.<br>';
                   header("location: login.php");
                }
            }
            
            $cust_email = strip_tags(trim($_POST['cust_email']));
            $cust_password = strip_tags(trim($_POST['cust_password']));

            $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email=?");
            $statement->execute(array($cust_email));
            $total = $statement->rowCount();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            foreach($result as $row) {
                $cust_status = $row['cust_status'];
                $row_password = $row['cust_password'];
            }

            if($total==0) {
                $_SESSION['error_message'] .= LANG_VALUE_133.'<br>';
                header("location: login.php");
            } else {

                if( $row_password != md5($cust_password) ) {
                    $_SESSION['error_message'] .= LANG_VALUE_139.'<br>';
                    header("location: login.php");
                } else {
                    if($cust_status == 0) {
                        $_SESSION['error_message'] .= LANG_VALUE_148.'<br>';
                    } else  if(($captcha_status == 1 && $recaptchaStatus) || $captcha_status == 0){
                        $_SESSION['customer'] = $row;
                        get_cart($pdo);
                        header("location: index.php");
                    }
                }
                
            }
        }
    }
?>