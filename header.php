<?php
		ob_start();
		session_start();
		include("admin/inc/config.php");
		include("admin/inc/functions.php");
		include("admin/inc/CSRF_Protect.php");
		$csrf = new CSRF_Protect();

		require 'assets/mail/PHPMailer.php';
		require 'assets/mail/Exception.php';
		$mail = new PHPMailer\PHPMailer\PHPMailer();

		$error_message = '';
		$success_message = '';
		$error_message1 = '';
		$success_message1 = '';

		// Getting all language variables into array as global variable
		$i=1;
		$statement = $pdo->prepare("SELECT * FROM tbl_language");
		$statement->execute();
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
		foreach ($result as $row) {
			define('LANG_VALUE_'.$i,$row['lang_value']);
			$i++;
		}

		$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
		$statement->execute();
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result as $row){
			$logo = $row['logo'];
			$favicon = $row['favicon'];
			$contact_email = $row['contact_email'];
			$contact_phone = $row['contact_phone'];
			$meta_title_home = $row['meta_title_home'];
		    $meta_keyword_home = $row['meta_keyword_home'];
		    $meta_description_home = $row['meta_description_home'];
		    $before_head = $row['before_head'];
		    $after_body = $row['after_body'];
		    $theme_color = $row['color'];
		    $site_name = $row['site_name'];
		    $site_notif = $row['site_notification'];
		    $site_currency = $row['site_currency'];
            $site_address = $row['contact_address'];
            $site_phone = $row['contact_phone'];
            $site_email = $row['contact_email'];
		    $ngn_yen = $row['ngn'];
		    $usd_yen = $row['usd'];
		    $commission = $row['commission'];
            $banner_cart = $row['banner_cart'];
            $banner_checkout = $row['banner_checkout'];
            $allowed_paypal =  $row['paypal_status'];   
            $allowed_stripe =  $row['stripe_status'];  
            $allowed_paystack =  $row['paystack_status']; 
            $allowed_bank =  $row['bank_status'];
            $commission = $row['commission'];
            $import_api = $row['import_api'];
            $import_key = $row['import_key'];
            $area_id_1688 = $row['area_id_1688'];
            $area_id_taobao = $row['area_id_taobao'];
            $backup_express_fee = $row['express_fee'];
            $contact_map_iframe = $row['contact_map_iframe'];
            $contact_email = $row['contact_email'];
            $contact_phone = $row['contact_phone'];
            $contact_address = $row['contact_address'];
            $banner_buy = $row['banner_buy'];
            $custom_express_fee = $row['express_fee'];
        	$footer_about = $row['footer_about'];
        	$footer_copyright = $row['footer_copyright'];
        	$total_recent_post_footer = $row['total_recent_post_footer'];
            $total_popular_post_footer = $row['total_popular_post_footer'];
            $newsletter_on_off = $row['newsletter_on_off'];
            $before_body = $row['before_body'];
            $paystack_key = $row['paystack_public_key'];
            $stripe_public_key = $row['stripe_public_key'];
            $stripe_secret_key = $row['stripe_secret_key'];
            $banner_forget_password = $row['banner_forget_password'];
            $captcha_site_key = $row['captcha_site_key'];
            $captcha_secret_key = $row['captcha_secret_key'];
            $captcha_status = $row['captcha_status'];
            $forget_password_message = $row['forget_password_message'];
            $banner_login = $row['banner_login'];
            $admin_email = $row['receive_email'];
            $receive_email = $row['receive_email'];
            $receive_email_thank_you_message = $row['receive_email_thank_you_message'];
            $banner_registration = $row['banner_registration'];
            $banner_forget_password = $row['banner_forget_password'];
            $banner_reset_password = $row['banner_reset_password'];
            $banner_search = $row['banner_search'];
            $banner_ship = $row['banner_ship'];
            $ads_category_sidebar_on_off = $row['ads_category_sidebar_on_off'];
            $total_recent_post_sidebar = $row['total_recent_post_sidebar'];
            $total_popular_post_sidebar = $row['total_popular_post_sidebar'];
            $bank_detail = $row['bank_detail'];
		}

		$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE id=1");
		$statement->execute();
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
		foreach ($result as $row) {
			$about_meta_title = $row['about_meta_title'];
			$about_meta_keyword = $row['about_meta_keyword'];
			$about_meta_description = $row['about_meta_description'];
			$faq_meta_title = $row['faq_meta_title'];
			$faq_meta_keyword = $row['faq_meta_keyword'];
			$faq_meta_description = $row['faq_meta_description'];
			$blog_meta_title = $row['blog_meta_title'];
			$blog_meta_keyword = $row['blog_meta_keyword'];
			$blog_meta_description = $row['blog_meta_description'];
			$contact_meta_title = $row['contact_meta_title'];
			$contact_meta_keyword = $row['contact_meta_keyword'];
			$contact_meta_description = $row['contact_meta_description'];
			$pgallery_meta_title = $row['pgallery_meta_title'];
			$pgallery_meta_keyword = $row['pgallery_meta_keyword'];
			$pgallery_meta_description = $row['pgallery_meta_description'];
			$vgallery_meta_title = $row['vgallery_meta_title'];
			$vgallery_meta_keyword = $row['vgallery_meta_keyword'];
			$vgallery_meta_description = $row['vgallery_meta_description'];
		}

        $statement = $pdo->prepare("SELECT * FROM tbl_shipping_cost t1 JOIN tbl_country t2 ON t1.country_id = t2.country_id ORDER BY t2.country_name ASC");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);	
        $ordinary_goods = '<optgroup label="Ordinary Goods">';
        $dangerous_goods = '<optgroup label="Dangerous Goods">';						
        foreach ($result as $row) {
        	if($row['amount2'] != ""){
        		$ordinary_goods .= '<option value="'.$row['amount2'].'">'.$row['country_name'].'</option>';
        	}
        	if($row['amount3'] != ""){
        		$dangerous_goods .= '<option value="'.$row['amount3'].'">'.$row['country_name'].'</option>';
        	}
        }

        $statement = $pdo->prepare("SELECT * FROM tbl_shipping_cost_all WHERE sca_id=1");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);							
        foreach ($result as $row) {
        	if($row['amount2'] != ""){
        		$ordinary_goods .= '<option value="'.$row['amount2'].'">Others</option>';
        	}
        	if($row['amount3'] != ""){
        		$dangerous_goods .= '<option value="'.$row['amount3'].'">Others</option>';
        	}
        }

        $ordinary_goods .= '</optgroup>';
        $dangerous_goods .= '</optgroup>';
?>
<?php
    unset($_SESSION['cart_p_id']);
    unset($_SESSION['cart_size_id']);
    unset($_SESSION['cart_size_name']);
    unset($_SESSION['cart_color_id']);
    unset($_SESSION['cart_color_name']);
    unset($_SESSION['cart_cust_val']);
    unset($_SESSION['cart_p_type']);
    unset($_SESSION['cart_p_qty']);
    unset($_SESSION['cart_p_current_price']);
    unset($_SESSION['cart_p_name']);
    unset($_SESSION['cart_p_featured_photo']);
?>
<?php if(isset($_SESSION['customer']))get_cart($pdo); ?>

<!DOCTYPE html>
<html lang="en">
<head>

	<!-- Meta Tags -->
	<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

	<!-- Favicon -->
	<link rel="icon" type="image/png" href="assets/uploads/<?php echo $favicon; ?>">

	<!-- Stylesheets -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/css/owl.carousel.min.css">
	<link rel="stylesheet" href="assets/css/owl.theme.default.min.css">
	<link rel="stylesheet" href="assets/css/jquery.bxslider.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/rating.css">
	<link rel="stylesheet" href="assets/css/spacing.css">
	<link rel="stylesheet" href="assets/css/bootstrap-touch-slider.css">
	<link rel="stylesheet" href="assets/css/animate.min.css">
	<link rel="stylesheet" href="assets/css/tree-menu.css">
	<link rel="stylesheet" href="assets/css/select2.min.css">
	<link rel="stylesheet" href="assets/css/main.css">
	<link rel="stylesheet" href="assets/css/responsive.css">

	<?php

			$cur_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
			
			if($cur_page == 'index.php' || $cur_page == 'login.php' || $cur_page == 'registration.php' || $cur_page == 'cart.php' || $cur_page == 'checkout.php' || $cur_page == 'forget-password.php' || $cur_page == 'reset-password.php' || $cur_page == 'product-category.php' || $cur_page == 'product.php' || $cur_page == 'custom-product.php') {
				?>
				<title><?php echo $meta_title_home; ?></title>
				<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
				<meta name="description" content="<?php echo $meta_description_home; ?>">
				<?php
			}

			if($cur_page == 'about.php') {
				?>
				<title><?php echo $about_meta_title; ?></title>
				<meta name="keywords" content="<?php echo $about_meta_keyword; ?>">
				<meta name="description" content="<?php echo $about_meta_description; ?>">
				<?php
			}
			if($cur_page == 'faq.php') {
				?>
				<title><?php echo $faq_meta_title; ?></title>
				<meta name="keywords" content="<?php echo $faq_meta_keyword; ?>">
				<meta name="description" content="<?php echo $faq_meta_description; ?>">
				<?php
			}
			if($cur_page == 'blog.php') {
				?>
				<title><?php echo $blog_meta_title; ?></title>
				<meta name="keywords" content="<?php echo $blog_meta_keyword; ?>">
				<meta name="description" content="<?php echo $blog_meta_description; ?>">
				<?php
			}
			if($cur_page == 'contact.php') {
				?>
				<title><?php echo $contact_meta_title; ?></title>
				<meta name="keywords" content="<?php echo $contact_meta_keyword; ?>">
				<meta name="description" content="<?php echo $contact_meta_description; ?>">
				<?php
			}
			if($cur_page == 'photo-gallery.php') {
				?>
				<title><?php echo $pgallery_meta_title; ?></title>
				<meta name="keywords" content="<?php echo $pgallery_meta_keyword; ?>">
				<meta name="description" content="<?php echo $pgallery_meta_description; ?>">
				<?php
			}
			if($cur_page == 'video-gallery.php') {
				?>
				<title><?php echo $vgallery_meta_title; ?></title>
				<meta name="keywords" content="<?php echo $vgallery_meta_keyword; ?>">
				<meta name="description" content="<?php echo $vgallery_meta_description; ?>">
				<?php
			}

			if($cur_page == 'blog-single.php')
			{
				$statement = $pdo->prepare("SELECT * FROM tbl_post WHERE post_slug=?");
				$statement->execute(array($_REQUEST['slug']));
				$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
				foreach ($result as $row) 
				{
				    $og_photo = $row['photo'];
				    $og_title = $row['post_title'];
				    $og_slug = $row['post_slug'];
					$og_description = substr(strip_tags($row['post_content']),0,200).'...';
					echo '<meta name="description" content="'.$row['meta_description'].'">';
					echo '<meta name="keywords" content="'.$row['meta_keyword'].'">';
					echo '<title>'.$row['meta_title'].'</title>';
				}
			}

			if($cur_page == 'product.php')
			{
				$statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
				$statement->execute(array($_REQUEST['id']));
				$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
				foreach ($result as $row) 
				{
				    $og_photo = $row['p_featured_photo'];
				    $og_title = $row['p_name'];
				    $og_slug = 'product.php?id='.$_REQUEST['id'];
					$og_description = substr(strip_tags($row['p_description']),0,200).'...';
				}
			}

			if($cur_page == 'dashboard.php') {
				?>
				<title>Dashboard - <?php echo $meta_title_home; ?></title>
				<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
				<meta name="description" content="<?php echo $meta_description_home; ?>">
				<?php
			}
			if($cur_page == 'customer-profile-update.php') {
				?>
				<title>Update Profile - <?php echo $meta_title_home; ?></title>
				<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
				<meta name="description" content="<?php echo $meta_description_home; ?>">
				<?php
			}
			if($cur_page == 'customer-billing-shipping-update.php') {
				?>
				<title>Update Billing and Shipping Info - <?php echo $meta_title_home; ?></title>
				<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
				<meta name="description" content="<?php echo $meta_description_home; ?>">
				<?php
			}
			if($cur_page == 'customer-password-update.php') {
				?>
				<title>Update Password - <?php echo $meta_title_home; ?></title>
				<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
				<meta name="description" content="<?php echo $meta_description_home; ?>">
				<?php
			}
			if($cur_page == 'customer-order.php') {
				?>
				<title>Orders - <?php echo $meta_title_home; ?></title>
				<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
				<meta name="description" content="<?php echo $meta_description_home; ?>">
				<?php
			}
	?>
	
	<?php if($cur_page == 'blog-single.php'): ?>
		<meta property="og:title" content="<?php echo $og_title; ?>">
		<meta property="og:type" content="website">
		<meta property="og:url" content="<?php echo BASE_URL.$og_slug; ?>">
		<meta property="og:description" content="<?php echo $og_description; ?>">
		<meta property="og:image" content="assets/uploads/<?php echo $og_photo; ?>">
	<?php endif; ?>

	<?php if($cur_page == 'product.php'): ?>
		<meta property="og:title" content="<?php echo $og_title; ?>">
		<meta property="og:type" content="website">
		<meta property="og:url" content="<?php echo BASE_URL.$og_slug; ?>">
		<meta property="og:description" content="<?php echo $og_description; ?>">
		<meta property="og:image" content="assets/uploads/<?php echo $og_photo; ?>">
	<?php endif; ?>

	<!--google captcha-->
	<script src='https://www.google.com/recaptcha/api.js' async defer></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>

	<script type="text/javascript" src="//platform-api.sharethis.com/js/sharethis.js#property=5993ef01e2587a001253a261&product=inline-share-buttons"></script>

	<script>
		window.addEventListener( 'load',function(){
			// When the user scrolls the page, execute myFunction
			//window.onscroll = function() {stickyFunction()};

			// Get the header
			//var header = document.getElementById("myStickyHeader");

			// Get the offset position of the navbar
			//var sticky = header.offsetTop;

			// Add the sticky class to the header when you reach its scroll position. Remove "sticky" when you leave the scroll position
			function stickyFunction() {
			  if (window.pageYOffset > sticky) {
			    header.classList.add("stickyMain");
			  } else {
			    header.classList.remove("stickyMain");
			  }
			}
	    });
			var shipCalc = async () => {

					const { value: formValues } =  await Swal.fire({
					  title: 'Shipping Cost Calculator',
					  html:
					    '<input id="swal-input1"  type="Number" step="0.0001" placeholder="Item weight in KG" class="swal2-input">' +
					    '<select id="swal-input2"><option value="">Select Your Country</option><?php echo $ordinary_goods.$dangerous_goods; ?></select>',
					  focusConfirm: false,
					  showCancelButton: true,
					  preConfirm: () => {
					    return [
					      document.getElementById('swal-input1').value,
					      document.getElementById('swal-input2').value
					    ]
					  }
					});

					if (formValues) {
					  //let value = JSON.parse(formValues);
					  if(formValues[0] == ""){  
					    Swal.fire("Item weight must be specified");
					  }else if(formValues[1] == ""){  
					    Swal.fire("Country Must be Selected");
					  }else{ 
					  	let cost = "<?php echo $site_currency; ?>  "+(formValues[1] * formValues[0]);
					  	Swal.fire(cost);
					  }
					}

			};
	</script>
	
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.0.0/animate.min.css">
	
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    
	<!-- JavaScript -->
	<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

	<!-- CSS -->
	<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
	<!-- Default theme -->
	<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>
	
	

<?php if($site_notif != "none"){ ?>
	<script>
		let notifMessage = '<?php echo htmlspecialchars_decode($site_notif); ?>';
		function notify(){
			Swal.fire({
				  icon: 'info',
				  html: notifMessage,
				  showClass: {
				    popup: 'animate__animated animate__fadeInDown'
				  },
				  hideClass: {
				    popup: 'animate__animated animate__fadeOutUp'
				  }
			});
	    }
	</script>
<?php } else{ ?>
    <script>
        let notifMessage = "none";
    </script>
<?php } ?>

<style>

        div.swal2-header{
		 	margin: 20px auto;
		}
		#swal-input2{
		  width:100%;
		  padding: 10px;
		}
		#swal-input2 option{
		  font-size: 15px;
		}
		#swal-input1{
		  border: 1px solid;
		  max-width: 100% !important;
		}
		button.swal2-cancel.swal2-styled{
			margin-top: 0px;
		}
		
		.top .right ul li a:hover,
        .nav,
        .menu-container,
        .slide-text > a.btn-primary,
        .welcome p.button a,
        .product .owl-controls .owl-prev:hover, 
        .product .owl-controls .owl-next:hover,
        .product .text p a,
        .home-blog .text p.button a,
        .home-newsletter,
        .footer-main h3:after,
        .scrollup i,
        .cform input[type="submit"],
        .blog p.button a,
        div.pagination a,
        #left ul.nav>li.cat-level-1.parent>a,
        .product .btn-cart1 input[type="submit"],
        .review-form .btn-default {
			background: #<?php echo $theme_color; ?>!important;
		}
        
        #left ul.na > li.cat-level-1.parent > a > .sign, 
        #left ul.nav > i.cat-level-1 li.parent >a >.sign {
            background-color: #<?php echo $theme_color; ?>!important;
        }
        
        .top .left ul li,
        .top .left ul li i,
        .top .right ul li a,
        .header .right ul li,
        .header .right ul li a,
        .welcome p.button a:hover,
        .product .text h4,
        .cform address span,
        .blog h3 a:hover,
        .blog .text ul.status li a,
        .blog .text ul.status li,
        .widget ul li a:hover,
        .breadcrumb ul li,
        .breadcrumb ul li a,
        .product .p-title h2 {
			color: #<?php echo $theme_color; ?>!important;
		}
        
        .scrollup i,
        div.pagination a,
        #left ul.nav>li.cat-level-1.parent>a {
            border-color: #<?php echo $theme_color; ?>!important;
        }
        
        .widget h4 {
            border-bottom-color: #<?php echo $theme_color; ?>!important;
        }
        
        
        .top .right ul li a:hover,
        #left ul.nav>li.cat-level-1 .lbl1 {
            color: #fff!important;
        }
        .welcome p.button a:hover {
            background: #fff!important;
        }
        .slide-text > a:hover, .slide-text > a:active {
            background-color: #333333!important;
        }
        .product .text p a:hover,
        .home-blog .text p.button a:hover,
        .blog p.button a:hover {
            background: #333!important;
        }
        
        div.pagination span.current {
            border-color: #777!important;
            background: #777!important;
        }
        
        div.pagination a:hover, 
        div.pagination a:active {
            border-color: #777!important;
            background: #777!important;
        }
        
        .product .nav {
            background: transparent!important;
        }
        
        .cart-buttons li input[type="submit"], .cart-buttons li a{
            background: #<?php echo $theme_color; ?>!important;
        }
        .page h3.special:after{
            background: #<?php echo $theme_color; ?>!important;
        }

        @media only screen and (max-width: 991px){
			.header .logo {
		   		margin: 10px auto;
		   }
	    }
</style> 

<?php echo $before_head; ?>

</head>
<body>

<?php echo $after_body; ?>

<div id="preloader">
	<div id="status"></div>
</div>


<div class="top">
	<div class="container">
		<div class="row">
			<div class="col-md-4 col-sm-4 col-xs-12">
				<div class="left">
					<ul>
						<!--<li><i class="fa fa-phone"></i> <?php //echo $contact_phone; ?></li>
						<li><i class="fa fa-envelope-o"></i> <?php //echo $contact_email; ?></li>-->
					</ul>
				</div>
			</div>
			<div class="col-md-4 col-sm-5 col-xs-12">
				<div class="center" style="background-color: #<?php echo $theme_color; ?>!important;">
					<ul>
						<li><span style="margin:auto;line-height:40px;color:#fff;padding-left:15px;font-size:14px;"><?php echo $site_currency; ?> - RMB : <span style="color: gold;"><?php echo $ngn_yen; ?></span></span></li>
						<li><span style="margin:auto;line-height:40px;color:#fff;padding-left:15px;font-size:14px;">|</span></li>
						<li><span style="margin:auto;line-height:40px;color:#fff;padding-left:15px;font-size:14px;">USD - RMB : <span style="color: gold;"><?php echo $usd_yen; ?></span></span></li>
					</ul>
				</div>
			</div>
			<div class="col-md-4 col-sm-3 col-xs-12">
				<div class="right">
					<ul>
						<?php
						$statement = $pdo->prepare("SELECT * FROM tbl_social");
						$statement->execute();
						$result = $statement->fetchAll(PDO::FETCH_ASSOC);
						foreach ($result as $row) {
							?>
							<?php if($row['social_url'] != ''): ?>
							<li><a href="<?php echo $row['social_url']; ?>" target="_blank"><i class="<?php echo $row['social_icon']; ?>"></i></a></li>
							<?php endif; ?>
							<?php
						}
						?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="header">
	<div class="container">
		<div class="row inner">
			<div class="col-md-4 logo">
				<a href="index.php"><img src="assets/uploads/<?php echo $logo; ?>" alt="logo image"></a>
			</div>
			
			<div class="col-md-5 right">
				<ul>
					
					<?php
					if(isset($_SESSION['customer'])) {
						?>
						<li><i class="fa fa-user"></i> <?php echo LANG_VALUE_13; ?> <?php echo $_SESSION['customer']['cust_name']; ?></li>
						<li><a href="dashboard.php"><i class="fa fa-home"></i> <?php echo LANG_VALUE_89; ?></a></li>
						<?php
					} else {
						?>
						<li><a href="login.php"><i class="fa fa-sign-in"></i> <?php echo LANG_VALUE_9; ?></a></li>
						<li><a href="registration.php"><i class="fa fa-user-plus"></i> <?php echo LANG_VALUE_15; ?></a></li>
						<?php	
					}
					?>

					<li><a href="cart.php"><i class="fa fa-shopping-cart"></i> <?php echo LANG_VALUE_19; ?> (<?php echo  $site_currency." "; ?><?php
					if(isset($_SESSION['cart_p_id'])) {
						$table_total_price = 0;
						$i=0;
	                    foreach($_SESSION['cart_p_qty'] as $key => $value) 
	                    {
	                        $i++;
	                        $arr_cart_p_qty[$i] = $value;
	                    }                    $i=0;
	                    foreach($_SESSION['cart_p_current_price'] as $key => $value) 
	                    {
	                        $i++;
	                        $arr_cart_p_current_price[$i] = $value;
	                    }
	                    for($i=1;$i<=count($arr_cart_p_qty);$i++) {
	                    	$row_total_price = $arr_cart_p_current_price[$i]*$arr_cart_p_qty[$i];
	                        $table_total_price = $table_total_price + $row_total_price;
	                    }
						echo number_format($table_total_price,2);
					} else {
						echo '0.00';
					}
					?>)</a></li>
					<?php if(isset($_SESSION['customer'])) { ?>
						<li><i class="fa fa-sign-out"></i><a href="logout.php"><?php echo LANG_VALUE_14; ?></a></li>
					<?php } ?>
				</ul>
			</div>
			<div class="col-md-3 search-area">
				<form class="navbar-form navbar-left" role="search" action="search-result.php" method="get">
					<?php $csrf->echoInputField(); ?>
					<div class="form-group">
						<input type="text" class="form-control search-top" placeholder="<?php echo LANG_VALUE_2; ?>" name="search_text">
					</div>
					<button type="submit" class="btn btn-default"><?php echo LANG_VALUE_3; ?></button>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="nav">
	<div class="container">
		<div class="row">
			<div class="col-md-12 pl_0 pr_0 mainMenu">
				<div class="menu-container">
					<div class="menu">
						<ul>
							<li><a href="index.php">Home</a></li>
							
							<?php
							$statement = $pdo->prepare("SELECT * FROM tbl_top_category WHERE show_on_menu=1");
							$statement->execute();
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);
							foreach ($result as $row) {
								?>
								<li><a href="product-category.php?id=<?php echo $row['tcat_id']; ?>&type=top-category"><?php echo $row['tcat_name']; ?></a>
									<ul>
										<?php
										$statement1 = $pdo->prepare("SELECT * FROM tbl_mid_category WHERE tcat_id=?");
										$statement1->execute(array($row['tcat_id']));
										$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
										foreach ($result1 as $row1) {
											?>
											<li><a href="product-category.php?id=<?php echo $row1['mcat_id']; ?>&type=mid-category"><?php echo $row1['mcat_name']; ?></a>
												<ul>
													<?php
													$statement2 = $pdo->prepare("SELECT * FROM tbl_end_category WHERE mcat_id=?");
													$statement2->execute(array($row1['mcat_id']));
													$result2 = $statement2->fetchAll(PDO::FETCH_ASSOC);
													foreach ($result2 as $row2) {
														?>
														<li><a href="product-category.php?id=<?php echo $row2['ecat_id']; ?>&type=end-category"><?php echo $row2['ecat_name']; ?></a></li>
														<?php
													}
													?>
												</ul>
											</li>
											<?php
										}
										?>
									</ul>
								</li>
								<?php
							}
							?>

							<?php
							$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE id=1");
							$statement->execute();
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);		
							foreach ($result as $row) {
								$about_title = $row['about_title'];
								$faq_title = $row['faq_title'];
								$blog_title = $row['blog_title'];
								$contact_title = $row['contact_title'];
								$pgallery_title = $row['pgallery_title'];
								$vgallery_title = $row['vgallery_title'];
							}
							?>
							<li><a href="#">Gallery</a>
								<ul>
									<li><a href="photo-gallery.php"><?php echo $pgallery_title; ?></a></li>
									<li><a href="video-gallery.php"><?php echo $vgallery_title; ?></a></li>
								</ul>
							</li>
							<li><a href="about.php"><?php echo $about_title; ?></a></li>
							<li><a href="faq.php"><?php echo $faq_title; ?></a></li>
							<li><a href="blog.php"><?php echo $blog_title; ?></a></li>
							<li><a href="contact.php"><?php echo $contact_title; ?></a></li>
							<li class="rgtMenu"><a href="tracking.php">Track</a></li>
							<li class="rgtMenu"><a href="custom-product.php">Buy for Me</a></li>
							<li class="rgtMenu"><a href="ship.php">Ship for Me</a></li>
							<li class="rgtMenu" onclick="shipCalc()"><a href="#">Cost Calculator</a></li>
						</ul>
					</div>
				</div>
				<!--<div class="stickyHeader" id="myStickyHeader">
					<form class="sticky-form-wrapper" method="post" action="custom-product.php?id=<?php echo time(); ?>">
						<?php $csrf->echoInputField(); ?>
    					<input type="text" name="link" id="search" placeholder="Please enter the LINK of your Item..." required>
    					<input type="submit" value="go" id="submit" name="form1">
					</form>
				</div>-->
			</div>
		</div>
	</div>
</div>