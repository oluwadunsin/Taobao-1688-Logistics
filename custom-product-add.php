<?php
    session_start();
    // Check if the customer is logged in or not
    if(!isset($_SESSION['customer'])) {
        header('location: logout.php');
        exit;
    }
		include("admin/inc/config.php");
		include("admin/inc/functions.php");
		include("admin/inc/CSRF_Protect.php");
?>
<?php
    $error_message1='';
    $success_message1 = '';
    if(isset($_POST['form_add_to_cart'])) {
        
        if(isset($_SESSION['cart_p_id'])){
            $arr_cart_p_id = array();
            $arr_cart_size_id = array();
            $arr_cart_color_id = array();
            $arr_cart_cust_val = array();
            $arr_cart_p_qty = array();
            $arr_cart_p_current_price = array();

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
            foreach($_SESSION['cart_color_id'] as $key => $value) 
            {
                $i++;
                $arr_cart_color_id[$i] = $value;
            }

            $i=0;
            foreach($_SESSION['cart_cust_val'] as $key => $value) 
            {
                $i++;
                $arr_cart_cust_val[$i] = $value;
            }


            $added = 0;
            if(!isset($_POST['size_id'])) {
                $size_id = 0;
            } else {
                $size_id = $_POST['size_id'];
            }
            if(!isset($_POST['color_id'])) {
                $color_id = 0;
            } else {
                $color_id = $_POST['color_id'];
            }
            if(!isset($_POST['comment'])) {
                $comment = "";
            } 

            $custom_mod = array('comment' => $comment, 'link'=> $_POST['p_link'], 'custom' => $_POST['p_select'],'shipping' => $_POST['p_shipping']);

            for($i=1;$i<=count($arr_cart_p_id);$i++) {
                if( ($arr_cart_p_id[$i]==$_REQUEST['id']) && ($arr_cart_size_id[$i]==$size_id) && ($arr_cart_color_id[$i]==$color_id) && ($arr_cart_cust_val[$i]== $custom_mod) ) {
                    $added = 1;
                    break;
                }
            }
            if($added == 1) {
               $error_message1 = 'This product is already added to the shopping cart.';
            } else {

                $i=0;
                foreach($_SESSION['cart_p_id'] as $key => $res) 
                {
                    $i++;
                }
                $new_key = $i+1;

                if(isset($_POST['size_id'])) {

                    $size_id = $_POST['size_id'];

                    $statement = $pdo->prepare("SELECT * FROM tbl_size WHERE size_id=?");
                    $statement->execute(array($size_id));
                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
                    foreach ($result as $row) {
                        $size_name = $row['size_name'];
                    }
                } else {
                    $size_id = 0;
                    $size_name = '';
                }
                
                if(isset($_POST['color_id'])) {
                    $color_id = $_POST['color_id'];
                    $statement = $pdo->prepare("SELECT * FROM tbl_color WHERE color_id=?");
                    $statement->execute(array($color_id));
                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
                    foreach ($result as $row) {
                        $color_name = $row['color_name'];
                    }
                } else {
                    $color_id = 0;
                    $color_name = '';
                }
              

                $_SESSION['cart_p_id'][$new_key] = $_REQUEST['id'];
                $_SESSION['cart_size_id'][$new_key] = $size_id;
                $_SESSION['cart_size_name'][$new_key] = $size_name;
                $_SESSION['cart_color_id'][$new_key] = $color_id;
                $_SESSION['cart_color_name'][$new_key] = $color_name;
                $_SESSION['cart_cust_val'][$new_key] = $custom_mod;
                $_SESSION['cart_p_type'][$new_key] = 1;
                $_SESSION['cart_p_qty'][$new_key] = $_POST['p_qty'];
                $_SESSION['cart_p_current_price'][$new_key] = $_POST['p_current_price'];
                $_SESSION['cart_p_name'][$new_key] = $_POST['p_name'];
                $_SESSION['cart_p_featured_photo'][$new_key] = $_POST['p_featured_photo'];

                $success_message1 = 'Product is added to the cart successfully!';
            }
            
        }
        else{

            if(isset($_POST['size_id'])) {

                $size_id = $_POST['size_id'];

                $statement = $pdo->prepare("SELECT * FROM tbl_size WHERE size_id=?");
                $statement->execute(array($size_id));
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
                foreach ($result as $row) {
                    $size_name = $row['size_name'];
                }
            }else {
                $size_id = 0;
                $size_name = '';
            }
            
            if(isset($_POST['color_id'])) {
                $color_id = $_POST['color_id'];
                $statement = $pdo->prepare("SELECT * FROM tbl_color WHERE color_id=?");
                $statement->execute(array($color_id));
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
                foreach ($result as $row) {
                    $color_name = $row['color_name'];
                }
            } else {
                $color_id = 0;
                $color_name = '';
            }

            if(!isset($_POST['p_comment'])) {
                $comment = "";
            }else   $comment = strip_tags($_POST['p_comment']);
            
            $custom_mod = array('comment' => $comment, 'link'=> $_POST['p_link'], 'custom' => $_POST['p_select'],'shipping' => $_POST['p_shipping']);
            

            $_SESSION['cart_p_id'][1] = $_REQUEST['id'];
            $_SESSION['cart_size_id'][1] = $size_id;
            $_SESSION['cart_size_name'][1] = $size_name;
            $_SESSION['cart_color_id'][1] = $color_id;
            $_SESSION['cart_color_name'][1] = $color_name;
            $_SESSION['cart_cust_val'][1] = $custom_mod;
            $_SESSION['cart_p_type'][1] = 1;
            $_SESSION['cart_p_qty'][1] = $_POST['p_qty'];
            $_SESSION['cart_p_current_price'][1] = $_POST['p_current_price'];
            $_SESSION['cart_p_name'][1] = $_POST['p_name'];
            $_SESSION['cart_p_featured_photo'][1] = $_POST['p_featured_photo'];

            $success_message1 = 'Product is added to the cart successfully!';
        }
    }
?>
<?php if(isset($_SESSION['customer']))store_cart($pdo); ?>
<?php
    if($error_message1 != '') {
        $status = ((isset($_POST['end']) && $_POST['end']) ? true : false);
        $data = array("msg"=>$error_message1,"reload"=>$status);
        echo json_encode($data);
    }
    if($success_message1 != '') {
        $status = ((isset($_POST['end']) && $_POST['end']) ? true : false);
        $data = array("msg"=>$success_message1,"reload"=>$status);
        echo json_encode($data);
    }
?>