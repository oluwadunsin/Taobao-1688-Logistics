<?php
    ob_start();
    session_start();
    include("admin/inc/config.php");
    include("admin/inc/functions.php");
    include("admin/inc/CSRF_Protect.php");
    $csrf = new CSRF_Protect();

  // Check if the user is logged in or not
  if(!isset($_SESSION['customer'])) {
    header('location: login.php');
    exit;
  }

		// Getting all language variables into array as global variable
		$i=1;
		$statement = $pdo->prepare("SELECT * FROM tbl_language");
		$statement->execute();
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
		foreach ($result as $row) {
			define('LANG_VALUE_'.$i,$row['lang_value']);
			$i++;
		}
?>

<?php
    if(!isset($_REQUEST['id'])) {
      header('location: logout.php');
      exit;
    } else {
        // Check the id is valid or not
        $statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_id=?");
        $statement->execute(array($_REQUEST['id']));
        $total = $statement->rowCount();
        if( $total == 0 ) {
          header('location: logout.php');
          exit;
        } else {
          $id = $_REQUEST['id'];
          $result = $statement->fetchAll(PDO::FETCH_ASSOC);             
          foreach ($result as $row) {
            $cust_id = $row['customer_id'];
            $cust_name = $row['customer_name'];
            $cust_email = $row['customer_email'];
            $cust_date1 = date('d/m/Y', strtotime($row['payment_date']));
            $cust_date2 = date('d/m/Y h:i:s A', strtotime($row['payment_date']));
            $cust_amount = $row['paid_amount'];
            $cust_shipping = $row['paid_shipping'];
            $cust_commission = $row['paid_commission'];
            $cust_method = ucfirst($row['payment_method']);
            $cust_payment_id = $row['payment_id'];
            $cust_txn_id = $row['txnid'];
          }

          $statement = $pdo->prepare("SELECT * FROM tbl_order WHERE payment_id=?");
          $statement->execute(array($cust_payment_id));
          $result= $statement->fetchAll(PDO::FETCH_ASSOC);   
          $order_price_total = 0;   
          $tableData = "";                     
          foreach ($result as $row) {
              $order_name = $row['product_name'];
              $order_link = $row['product_link'];
              $order_quantity = $row['quantity'];
              $order_price = $row['unit_price']*$order_quantity;
              $order_price_total += (int)$order_price;
              $tableData .= '
                  <tr>
                    <td>'.$order_quantity.'</td>
                    <td>'.$order_name.'</td>
                    <td>'.$order_link.'</td>
                    <td>'.$order_price.'</td>
                  </tr>';
          }
          
          $statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
          $statement->execute();
          $result = $statement->fetchAll(PDO::FETCH_ASSOC);                           
          foreach ($result as $row) {
			  $logo = $row['logo'];
              $site_name = $row['site_name'];
              $site_address = $row['contact_address'];
              $site_phone = $row['contact_phone'];
              $site_email = $row['contact_email'];
              $site_currency = $row['site_currency'];
              $ngn_yen = $row['ngn'];
              $usd_yen = $row['usd'];
              $commission = $row['commission'];
          }

        }
    }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $site_name ?> | Invoice</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="admin/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="admin/css/AdminLTE.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="wrapper">
     <!-- Main content -->
        <section class="invoice">
          <!-- title row -->
          <div class="row">
            <div class="col-xs-12">
              <h2 class="page-header">
                <img src="assets/uploads/<?php echo $logo; ?>" alt="logo image" height="50px" width="50px"> <?php echo $site_name; ?>.
                <small class="pull-right">Date: <?php echo $cust_date1; ?></small>
              </h2>
            </div><!-- /.col -->
          </div>
          <!-- info row -->
          <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
              From
              <address>
                <strong><?php echo $site_name; ?>.</strong><br>
                <?php echo $site_address; ?><br>
                Phone: <?php echo $site_phone; ?><br>
                Email: <?php echo $site_email; ?>
              </address>
            </div><!-- /.col -->
            <div class="col-sm-4 invoice-col">
              To
              <address>
                <strong><?php echo $cust_name; ?></strong><br>
                <?php echo $_SESSION['customer']['cust_address']; ?><br>
                Phone: <?php echo $_SESSION['customer']['cust_phone']; ?><br>
                Email: <?php echo $cust_email; ?>
              </address>
            </div><!-- /.col -->
            <div class="col-sm-4 invoice-col">
              <b>Invoice #<?php echo $cust_payment_id ?></b><br>
              <br>
              <b>Txn ID:</b> <?php echo $cust_txn_id ?><br>
              <b>Payment Due:</b> <?php echo $cust_date2; ?><br>
              <b>Account:</b> <?php echo $cust_id; ?>
            </div><!-- /.col -->
          </div><!-- /.row -->

          <!-- Table row -->
          <div class="row">
            <div class="col-xs-12 table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Qty</th>
                    <th>Product</th>
                    <th>Description</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <?php echo $tableData; ?>
                </tbody>
              </table>
            </div><!-- /.col -->
          </div><!-- /.row -->

          <div class="row">
            <!-- accepted payments column -->
            <div class="col-xs-6">
              <p class="lead">Payment Methods:</p>
              <img src="assets/img/visa.png" alt="Visa">
              <img src="assets/img/mastercard.png" alt="Mastercard">
              <img src="assets/img/american-express.png" alt="American Express">
              <img src="assets/img/paypal.png" alt="Paypal">
              <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                PAID WITH : <?php echo $cust_method; ?>
              </p>
            </div><!-- /.col -->
            <div class="col-xs-6">
              <p class="lead">Amount PAID <?php echo date('d/m/Y h:i:s A',time()); ?></p>
              <div class="table-responsive">
                <table class="table">
                  <tr>
                    <th style="width:50%">Subtotal:</th>
                    <td><?php echo $site_currency.' '.number_format($order_price_total,2); ?></td>
                  </tr>
                  <tr>
                    <th>Tax (0%)</th>
                    <td><?php echo $site_currency.' 0.00'; ?></td>
                  </tr>
                  <tr>
                    <th><?php echo LANG_VALUE_84; ?>:</th>
                    <td><?php echo $site_currency.' '.number_format($cust_shipping,2); ?></td>
                  </tr>
                  <tr>
                    <th>Commission:</th>
                    <td><?php echo $site_currency.' '.number_format($cust_commission,2); ?></td>
                  </tr>
                  <tr>
                    <th>Total:</th>
                    <td><?php echo $site_currency.' '.number_format($cust_amount,2); ?></td>
                  </tr>
                </table>
              </div>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->

    </div><!-- ./wrapper -->
  </body>
</html>
