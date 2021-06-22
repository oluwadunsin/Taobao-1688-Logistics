<?php require_once('header.php'); ?>

<?php
    // Check if the customer is logged in or not
    if(!isset($_SESSION['customer'])) {
        header('location: '.BASE_URL.'logout.php');
        exit;
    } else {
        // If customer is logged in, but admin make him inactive, then force logout this user.
        $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_id=? AND cust_status=?");
        $statement->execute(array($_SESSION['customer']['cust_id'],0));
        $total = $statement->rowCount();
        if($total) {
            header('location: '.BASE_URL.'logout.php');
            exit;
        }

    }
    
    $checkout_cost=0;
    $statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE customer_email=?");
    $statement->execute(array($_SESSION['customer']['cust_email']));
    $total_checkouts = $statement->rowCount();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $checkout_cost += $row['paid_amount'];
    }
    
?>
<link href="admin/css/AdminLTE.min.css" rel="stylesheet">
<style>
    div.small-box > div.inner{
        height:100px;
    }
    
    @media only screen and (max-width: 400px){
		   .small-box h3 {
		   		font-size: 20px !important;
		   }
           div.small-box > div.inner{
               height:unset;
           }
	 }
</style>
<div class="page">
    <div class="container">
        <div class="row">            
            <div class="col-md-12"> 
                <?php require_once('customer-sidebar.php'); ?>
            </div>
            <div class="col-md-12">
                <div class="user-content">
                    <h3 class="text-center">
                        <?php echo LANG_VALUE_90; ?>
                    </h3>
                </div>                
            </div>
        </div>
        <section class="content">
        	<div class="row">
        
                <div class="col-lg-3 col-xs-6">
                  <!-- small box -->
                  <div class="small-box bg-aqua">
                    <div class="inner">
                      <h4><?php echo (isset($_SESSION['cart_p_id'])) ? count($_SESSION['cart_p_id']) : 0; ?></h4>
                      <p>Cart Items</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-cart-arrow-down"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                      More info <i class="fa fa-arrow-circle-right"></i>
                    </a>
                  </div>
                </div>
        
                <div class="col-lg-3 col-xs-6">
                  <!-- small box -->
                  <div class="small-box bg-green">
                    <div class="inner">
                      <h4><?php echo $site_currency." ".number_format($table_total_price,2); ?></h4>
                      <p>Cart Cost</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-calculator"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                      More info <i class="fa fa-arrow-circle-right"></i>
                    </a>
                  </div>
                </div>
        
                <div class="col-lg-3 col-xs-6">
                  <!-- small box -->
                  <div class="small-box bg-yellow">
                    <div class="inner">
                      <h4><?php echo $total_checkouts; ?></h4>
                      <p>Checkouts</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-cubes"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                      More info <i class="fa fa-arrow-circle-right"></i>
                    </a>
                  </div>
                </div>
        
                <div class="col-lg-3 col-xs-6">
                  <!-- small box -->
                  <div class="small-box bg-red">
                    <div class="inner">
                      <h4><?php echo $site_currency." ".number_format($checkout_cost,2); ?></h4>
                      <p>Checkouts Total</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-truck"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                      More info <i class="fa fa-arrow-circle-right"></i>
                    </a>
                  </div>
                </div>
        
        		<div class="col-md-4 col-sm-6 col-xs-12">
        			<div class="info-box">
        				<span class="info-box-icon bg-purple"><i class="fa fa-calendar"></i></span>
        				<div class="info-box-content">
        					<span class="info-box-text">Registered</span>
        					<span class="info-box-number"><?php echo date('Y-m-d h:i:s',$_SESSION['customer']['cust_timestamp']); ?></span>
        				</div>
        			</div>
        		</div>
        		<div class="col-md-4 col-sm-6 col-xs-12">
        			<div class="info-box">
        				<span class="info-box-icon bg-lime"><i class="fa fa-user"></i></span>
        				<div class="info-box-content">
        					<span class="info-box-text">Name</span>
        					<span class="info-box-number"><?php echo $_SESSION['customer']['cust_name']; ?></span>
        				</div>
        			</div>
        		</div>
        		<div class="col-md-4 col-sm-6 col-xs-12">
        			<div class="info-box">
        				<span class="info-box-icon bg-green"><i class="fa fa-certificate"></i></span>
        				<div class="info-box-content">
        					<span class="info-box-text">Id</span>
        					<span class="info-box-number"><?php echo $_SESSION['customer']['cust_id']; ?></span>
        				</div>
        			</div>
        		</div>
        		<div class="col-md-4 col-sm-6 col-xs-12">
        			<div class="info-box">
        				<span class="info-box-icon bg-red"><i class="fa fa-envelope"></i></span>
        				<div class="info-box-content">
        					<span class="info-box-text">Email</span>
        					<span class="info-box-number"><?php echo $_SESSION['customer']['cust_email']; ?></span>
        				</div>
        			</div>
        		</div>
        		<div class="col-md-4 col-sm-6 col-xs-12">
        			<div class="info-box">
        				<span class="info-box-icon bg-blue"><i class="fa fa-phone"></i></span>
        				<div class="info-box-content">
        					<span class="info-box-text">Phone</span>
        					<span class="info-box-number"><?php echo $_SESSION['customer']['cust_phone']; ?></span>
        				</div>
        			</div>
        		</div>
        		<div class="col-md-4 col-sm-6 col-xs-12">
        			<div class="info-box">
        				<span class="info-box-icon bg-black"><i class="fa fa-user-plus"></i></span>
        				<div class="info-box-content">
        					<span class="info-box-text">Ref Code</span>
        					<span class="info-box-number"></span>
        				</div>
        			</div>
        		</div>
            </div>
     	</section>
</div>

<?php require_once('footer.php'); ?>