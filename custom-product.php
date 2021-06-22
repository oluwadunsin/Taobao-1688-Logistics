<?php 
    require_once('header.php'); 
?>

<?php if(true){ ?>

        <form action="" method="post" style="display:none;">
            <?php $csrf->echoInputField(); ?> 
            <input type="hidden" id="linkInput" name="link" value="">
            <input type="submit" name="form1" id="linkInputSubmit" style="display:none;">
        </form>

        <script>
            var buyForm = async () => {
                    const { value: linkAddress } = await Swal.fire({
                      title: 'Enter your Item Link',
                      input: 'text',
                      inputPlaceholder: 'Enter your 1688 or Taobao link',
                      showCancelButton: true,
                      inputValidator: (value) => {
                        if (!value) {
                          return 'You need to write something!'
                        }else if((/1688.com|taobao.com|tb.cn/gi).exec(value) == null){
                             return 'Link does not belong to 1688 or Taobao'
                        }
                      }
                    })

                    if (linkAddress) {
                      document.getElementById('linkInput').value = linkAddress;
                      document.getElementById('linkInputSubmit').click();
                    }

            };
        </script>
<?php } ?>

<?php 
    if(!isset($_GET['id'])){
        $gen_id = time();
        $show_alert=true;
        header('location: custom-product.php?id='.$gen_id);
    }
    $theres_error = false;
?>

<?php
    // Check if the customer is logged in or not
    if(!isset($_SESSION['customer'])) {
        header('location: '.BASE_URL.'logout.php');
        exit;
    }

    $show_alert = false;
    if(isset($_POST['form1'])) {
        if(empty($_POST['link'])) {
            $show_alert = true;
            header('location: .');
            exit;
        }else if(preg_match("/1688.com|taobao.com|tb.cn/i", $_POST['link']) < 1){
            echo '<script type="text/javascript">
                Swal.fire({
                  icon: "error",
                  title: "Oops...",
                  text: "Link does not belong to 1688 or Taobao!"
                }).then(function() {
                        buyForm();
                });
            </script>';
        }else if(preg_match("/taobao.com|tb.cn/i", $_POST['link']) > 0){
            $pattern = '/id=|sm=/i';
            preg_match("/(id=|sm=)\w+/i", $_POST['link'],$result);
            if(count($result) > 0){
                 $res = preg_replace($pattern,"",$result);   
                 $product_id = $res[0];
                 $product_type = 'taobao';
            }else{
                echo '<script type="text/javascript">
                    Swal.fire({
                      icon: "error",
                      title: "Oops...",
                      text: "Invalid Taobao Link : has no id attribute!"
                    }).then(function() {
                        buyForm();
                    });
                </script>';
            }
        }else if(preg_match("/1688.com/i", $_POST['link']) > 0){
            if(stripos($_POST['link'],'detail.m.1688.com') !== false){
                preg_match("/(?!offerId=)\w+$/i", $_POST['link'],$prelim);
                $_POST['link'] = 'https://detail.1688.com/offer/'.$prelim[0].'.html';
            }
            preg_match("/\w+(?=\.html)/i", $_POST['link'],$result);
            if(count($result) > 0){
                $product_id = $result[0];
                $product_type = '1688';
            }else{
                echo '<script type="text/javascript">
                    Swal.fire({
                      icon: "error",
                      title: "Oops...",
                      text: "Invalid 1688 Link : has no id attribute!"
                    }).then(function() {
                        buyForm();
                    });
                </script>';
            }
        }
        
        //curl onebound
        if(isset($product_type)){
                $statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
                $statement->execute();
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
                foreach ($result as $row) {
                    $import_api = $row['import_api'];
                    $import_key = $row['import_key'];
                }
                try{
                    $method = "POST" ;  
                    $url = "https://api.onebound.cn/".$product_type."/api_call.php?key=".$import_api."&secret=".$import_key."&api_name=item_get&num_iid=".$product_id."&is_promotion=1&lang=en&result_type=json" ; 
                    
                    //$response = urldecode(html_entity_decode(file_get_contents($url)));
                    $response = file_get_contents($url);
                    $info = json_decode($response,true); 
                    //echo $url;
                    //echo($response);
                    //print_r($info);

                    if($info['error'] != null){
                        // there was an error from the API
                        throw new Exception('API returned error: ' . $info['error']." ".$info['reason']." ".$info['error_code']);
                    }

                    $shipping_fee = 0;

                    $item = $info['item']; 
                    $item_title = $item['title'];
                    $item_link = $item['detail_url'];
                    $item_sdesc = $item['desc_short'];
                    $item_desc = $item['desc'];
                    $item_price = $item['price'] * $ngn_yen;
                    $item_originalprice = $item['orginal_price'] * $ngn_yen;
                    $item_mainpic = $item['pic_url'];
                    $item_imgs = "";
                    $item_imgs2 = "";
                    $item_qty = "999";
                    $c = 1;
                    //echo '<br> TITLE IS : '.$item['title'].'<br>';
                    foreach($item['item_imgs'] as $key=>$value){
                        $item_imgs .= '<a data-slide-index="'.$c.'" href=""><div class="prod-pager-thumb" style="background-image: url('.$value['url'].');"></div></a>';

                        $item_imgs2 .= '<li style="background-image: url('.$value['url'].');"><a data-slide-index="'.$c.'" href=""><div class="prod-pager-thumb" style="background-image: url("'.$value['url'].');"></div>';

                        $c++;
                    }
                    /**$item_options = '<li id="customOption0" value="" class="customOption" data-price="'.$item_price.'" data-orig-price="'.$item_originalprice.'" data-qty="999" data-description="Select an Option or Variety">Select</li>'; **/
                    $item_options = "";
                    $d = 1;
                    if(count($item['skus']['sku']) > 0){
                            foreach($item['skus']['sku'] as $key=>$value){
                                $properties = explode(";",$value['properties']);
                                $properties_name = array();
                                $properties_desc = array();
                                foreach($properties as $props){
                                    $prop_temp = explode(":",$item['props_list'][$props]);
                                    array_push($properties_name, $prop_temp[0]);
                                    array_push($properties_desc, $prop_temp[1]);
                                }
                                $data_desc1 = "";
                                $data_desc2 = "";
                                for($f=0; $f<count($properties_name); $f++){
                                    $data_desc1 .= '<strong style="font-size:15px;">'.$properties_name[$f].':</strong><br><i>'.$properties_desc[$f].'</i><br>';
                                    $data_desc2 .= $properties_name[$f].':<br><i>'.$properties_desc[$f].'</i><br>';
                                }   
                                $item_options .= '<li id="customOption'.$d.'" class="customOption" data-price="'.$value['price'] * $ngn_yen.'" data-orig-price="'.((isset($value['orginal_price'])) ? ($value['orginal_price'] * $ngn_yen) : NULL).'" data-qty="'.$value['quantity'].'"
                                                 data-description="'.$data_desc2.'"><span>'.$d.'</span>
                                                <div class="vCustomP pDiv">'.$data_desc1.'
                                                         <strong style="font-size:15px;">Price:</strong>
                                                         <i style="color:yellow;">'.$value['price'] * $ngn_yen.' '.$site_currency.'</i><br>
                                                         <div class="qtyInDC">
                                                          <div class="value-button decrease" onclick="decreaseValue(\'customQtyOpt'.$d.'\')" value="Decrease Value">-</div>
                                                          <input type="number" id="customQtyOpt'.$d.'"step="1" min="1" max="'.$value['quantity'].'" value="0" /><span class="qtySpan">(Qty Available : '.$value['quantity'].')</span>
                                                          <div class="value-button increase" onclick="increaseValue(\'customQtyOpt'.$d.'\')" value="Increase Value">+</div>
                                                        </div>
                                                </div>
                                                </li>';   
                                $d++;                  
                            }
                    }else{
                                
                                $data_desc1 = '<strong style="font-size:15px;">'.$item_title.'</strong><br>';
                                $data_desc2 = $item_title.'<br>';  
                                $d = 1;
                                $item_options .= '<li id="customOption'.$d.'" class="customOption" data-price="'.$item_price.'" data-orig-price="'.$item_originalprice.'" data-qty="'.$item_qty.'"
                                                 data-description="'.$data_desc2.'"><span>'.$d.'</span>
                                                <div class="vCustomP pDiv">'.$data_desc1.'
                                                         <strong style="font-size:15px;">Price:</strong>
                                                         <i style="color:yellow;">'.$item_price.' '.$site_currency.'</i><br>
                                                         <div class="qtyInDC">
                                                          <div class="value-button decrease" onclick="decreaseValue(\'customQtyOpt'.$d.'\')" value="Decrease Value">-</div>
                                                          <input type="number" id="customQtyOpt'.$d.'"step="1" min="1" max="'.$item_qty.'" value="0" /><span class="qtySpan">(Qty Available : '.$item_qty.')</span>
                                                          <div class="value-button increase" onclick="increaseValue(\'customQtyOpt'.$d.'\')" value="Increase Value">+</div>
                                                        </div>
                                                </div>
                                                </li>'; 
                    }

                } catch (Exception $e) {
                    $error = $e->getMessage();
                    $theres_error = true;
                    echo '<script type="text/javascript">alert("Error: '.$error.'");
                            window.open(window.location.href,"_self");
                        </script>';
                }
        }
    }else $show_alert = true;

?>

<?php if(true){ ?> 

    <script src="//cdnjs.cloudflare.com/ajax/libs/list.js/1.5.0/list.min.js"></script>
    <style>
        .customSelectDiv {
          width: 500px;
          margin: 10px
        }

        .customSelectDiv ul {
          color: #ccc;
          list-style-type: none;
          height: 300px;
          max-height: 400px;
          overflow:auto;
        }

        .customSelectDiv ul li {
          position: relative;
          font: bold italic 45px/1.5 Helvetica, Verdana, sans-serif;
          margin-bottom: 20px;
        }
        .customSelectDiv ul li:hover {
          cursor: pointer;
          background-color: #9c9494;
          padding: 15px;
        }
        .customSelectDiv .active{
          background-color: #666;
          color: white;
          padding: 15px;
        }
        .customSelectDiv .active .pDiv{
          color: white;
        }
        .customSelectDiv .active .qtyInDC{
          color: #000;
        }

        .customSelectDiv li .pDiv {
          font: 12px/1.5 Helvetica, sans-serif;
          padding-left: 60px;
          color: #555;
        }

        .customSelectDiv span {
          position: absolute;
          font-size:40px;
        }
        
        #customSearch {
            margin-bottom: 20px;
            border: 2px solid #<?php echo $theme_color; ?> !important;
            padding: 3px;
            width: 100%;
        }
    </style>
    <style>
        .qtyInDC{
          width: 300px;
          padding-top: 10px;
          display:none;
        }
        
        .qtyInDC .value-button {
          display: inline-block;
          border: 1px solid #ddd;
          margin: 0px;
          width: 40px;
          height: 40px;
          text-align: center;
          vertical-align: middle;
          padding: 11px 0;
          background: #eee;
          -webkit-touch-callout: none;
          -webkit-user-select: none;
          -khtml-user-select: none;
          -moz-user-select: none;
          -ms-user-select: none;
          user-select: none;
          margin-top:-2px;
        }
        
        .qtyInDC .value-button:hover {
          cursor: pointer;
        }
        
        .qtyInDC .decrease {
          margin-right: -4px;
          border-radius: 8px 0 0 8px;
        }
        
        .qtyInDC .increase {
          margin-left: -4px;
          border-radius: 0 8px 8px 0;
        }
        
        .qtyInDC .input-wrap {
          margin: 0px;
          padding: 0px;
        }
        
        .qtyInDC input[type=number] {
          text-align: center;
          border: none;
          border-top: 1px solid #ddd !important;
          border-bottom: 1px solid #ddd !important;
          margin: 0px !important;
          width: 40px !important;
          height: 40px !important;
        }
        
        .qtyInDC input[type=number]::-webkit-inner-spin-button,
        .qtyInDC input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0 !important;
        }
        
        .qtyInDC .qtySpan {
            font-size: 15px !important;
            color: #fff;
            right: 110px;
            margin-top: 9px;
        }
        
        #submitBtn{
           border: 0;
           padding: 10px 14px;
           background: #<?php echo $theme_color; ?> !important;
           color: #fff;
           cursor:pointer;
        }
    </style>

<div class="page-banner" style="background-image: url(assets/uploads/<?php echo $banner_buy; ?>);">
    <div class="inner">
        <h1>Buy For Me</h1>
    </div>
</div>
    <div class="page" style="visibility: <?php echo (($show_alert || $theres_error) ? 'hidden' : 'visible'); ?>">
    	<div class="container">
    		<div class="row">
    			<div class="col-md-12">

    				<div class="product">
    					<div class="row">
    						<div class="col-md-5">
    							<ul class="prod-slider">
                                    
    								<li style="background-image: url(<?php echo $item_mainpic; ?>);">
                                        <a class="popup" href="<?php echo $item_mainpic; ?>"></a>
    								</li>
                                    <?php echo $item_imgs2; ?>
    							</ul>
    							<div id="prod-pager">
    								<a data-slide-index="0" href=""><div class="prod-pager-thumb" style="background-image: url(<?php echo $item_mainpic; ?>)"></div></a>
                                    <?php echo $item_imgs; ?>
    							</div>
    						</div>
    						<div class="col-md-7">
    							<div class="p-title"><h2><?php echo $item_title; ?></h2></div>
    							<div class="p-short-des">
    								<p>
    									<?php echo $item_sdesc; ?>
    								</p>
    							</div>
                                <form action="" method="post" id="customProduct">
                                <div class="p-quantity">
                                    <div class="row">
                                        <div class="col-md-12 mb_20  customSelectDiv">
                                            Options/Variations/Specifications <br>
                                            <div class="customSelectDiv" id="customSelectDiv">
                                                <input class="search" placeholder="Search Item Variations or Specifications" id="customSearch"/>
                                                <ul id="customSelect"  class="form-control list" required>
                                                    <?php echo $item_options; ?>
                                                </ul>
                                            </div>
                                        </div>
                                       <script>
                                           var options = {
                                              valueNames: ['vCustomP']
                                            };
                                            
                                            var userList = new List('customSelectDiv', options);
                                       </script>

                                    </div>
                                    
                                </div>
    							<div class="p-price" style="margin-top: -30px;">
                                    <span style="font-size:14px;"><?php echo LANG_VALUE_54; ?></span><br>
                                    <span>
                                        <?php if($item_originalprice !='' && $item_originalprice != $item_price){ ?>
                                            <del style="display: none;"><span id="p_old_price"><?php echo $site_currency." ".$item_originalprice; ?></span></del>
                                        <?php }else{ ?> 
                                            <del style="display: none;"><span id="p_old_price"><?php echo $site_currency." ".$item_originalprice; ?></span></del>
                                        <?php } ?>
                                            <span id="p_new_price"><?php echo $site_currency." ".$item_price; ?></span>
                                    </span>
                                </div>
                                <input type="hidden" id="p_current_price" name="p_current_price" value="<?php echo $item_price; ?>">
                                <input type="hidden" name="p_name" value="<?php echo $item_title; ?>">
                                <input type="hidden" name="p_link" value="<?php echo $item_link; ?>">
                                <input type="hidden" name="p_featured_photo" value="<?php echo $item_mainpic; ?>">
                                <input type="hidden" name="p_shipping" value="<?php echo $shipping_fee; ?>">
                                <input type="hidden" id="p_select" name="p_select" value="">
    							<!--<div class="p-quantity">
                                    <?php echo LANG_VALUE_55; ?> (<span style="color:blue">Available : <b id="qty-helper" ><?php echo $item_qty; ?></b></span>) <br>
    								<input id="p_qty" type="number" class="input-text qty" step="1" min="1" max="<?php echo $item_qty; ?>" name="p_qty" value="1" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric">
    							</div>-->
                                <div class="form-group" style="margin-bottom:30px;">
                                        <label for="p_comment">Additional Info</label>
                                        <textarea name="p_comment" id="custProdComment" class="form-control" rows="3" cols="10" placeholder="Enter Additional Informations" style="width:50%;"></textarea>
                                </div>
    							<div class="btn-cart btn-cart1">
                                    <input type="button" id="submitBtn" onclick="submitFunction()" value="<?php echo LANG_VALUE_154; ?>" name="form_add_to_cart">
    							</div>
                                </form>
    						</div>
    					</div>

    					<div class="row">
    						<div class="col-md-12">
    							<!-- Nav tabs -->
    							<ul class="nav nav-tabs" role="tablist">
    								<li role="presentation" class="active"><a href="#description" aria-controls="description" role="tab" data-toggle="tab"><?php echo LANG_VALUE_59; ?></a></li>
    							</ul>

    							<!-- Tab panes -->
    							<div class="tab-content">
    								<div role="tabpanel" class="tab-pane active" id="description" style="margin-top: -30px;">
    									<p>
                                            <?php echo $item_desc; ?>
    									</p>
    								</div>
    							</div>
    						</div>
    					</div>

    				</div>

    			</div>
    		</div>
    	</div>
    </div>
    <script>
        window.addEventListener('load',()=>{
            document.addEventListener('click',(event)=>{
                if(event.target.classList.contains("customOption") || event.target.parentElement.classList.contains("customOption") || event.target.parentElement.parentElement.classList.contains("customOption") == 'customOption'){
                    event.stopPropagation();
                    let dId;
                    if(event.target.classList.contains("customOption")) dId = event.target.id;
                    if(event.target.parentElement.classList.contains("customOption")) dId = event.target.parentElement.id;
                    if(event.target.parentElement.parentElement.classList.contains("customOption")) dId = event.target.parentElement.parentElement.id; 
                    
                    let new_price = document.getElementById(dId).getAttribute("data-price");
                    let old_price = document.getElementById(dId).getAttribute("data-orig-price");
                    //let qty = document.getElementById(dId).getAttribute("data-qty");

                    if(old_price !='' && old_price != new_price){
                        document.getElementById('p_old_price').textContent = '<?php echo $site_currency." "; ?>'+old_price;
                        document.getElementById('p_old_price').style.display = 'inline-block';
                    }else{
                        document.getElementById('p_old_price').textContent = '<?php echo $site_currency." "; ?>'+old_price;
                        document.getElementById('p_old_price').style.display = 'none';
                    }
                    document.getElementById('p_new_price').textContent = '<?php echo $site_currency." "; ?>'+new_price;
                    document.getElementById('p_current_price').value = new_price;
                    
                  
                        if (document.getElementById(dId).classList.contains("active")) {
                          document.getElementById(dId).classList.remove("active");
                          document.getElementById(dId).querySelector('.qtyInDC').style.display = "none";
                        }else{
                            // Add the active class to the current/clicked button
                            document.getElementById(dId).classList.add("active");
                            document.getElementById(dId).querySelector('.qtyInDC').style.display = "unset";
                        }
                }
            });
        });
    </script>
    <script>
        function increaseValue(did) {
          var value = parseInt(document.getElementById(did).value, 10);
          value = isNaN(value) ? 0 : value;
          value++;
          document.getElementById(did).value = value;
        }
        
        function decreaseValue(did) {
          var value = parseInt(document.getElementById(did).value, 10);
          value = isNaN(value) ? 0 : value;
          value < 1 ? value = 1 : '';
          value--;
          document.getElementById(did).value = value;
        }
    </script>
    <script>
        function submitFunction(){
            var fields = document.getElementById("customProduct").getElementsByTagName('*');
            for(var i = 0; i < fields.length; i++){
                fields[i].disabled = true;
            }
            document.getElementById('submitBtn').value = "Submitting";
            
            var current = document.getElementsByClassName("customOption active");
            // If there's no active class
                if (current.length > 0) {
                    for(u=0; u<current.length; u++){
                        let cQty =  current[u].querySelector('.qtyInDC input').value;
                        if(Number(cQty) > 0){
                            let cPrice = current[u].getAttribute("data-price");
                            let cName = "<?php echo $item_title; ?>";
                            let cLink = "<?php echo $item_link; ?>";
                            let cPhoto = "<?php echo $item_mainpic; ?>";
                            let cShipping = "<?php echo $shipping_fee; ?>";
                            let cComment =  document.getElementById('custProdComment').value;
                            let cValue =  current[u].getAttribute("data-description");
                            
                            let dataString = 'id=' + "<?php echo $_GET['id']; ?>" + '&form_add_to_cart=true' + '&p_current_price=' + cPrice + '&p_name=' + cName + '&p_link=' + cLink + '&p_featured_photo=' + cPhoto +
                                                '&p_shipping=' + cShipping + '&p_select=' + cValue + '&p_comment=' + cComment + '&p_qty=' + cQty + '&end=' + ((u == current.length-1) ? true : false);
                    
                            // AJAX code to submit form.
                            $.ajax({
                                type: "POST",
                                url: "custom-product-add",
                                data: dataString,
                                cache: false,
                                success: function(data) {
                                    data = JSON.parse(data);
                                    alertify.notify(data.msg, 'success', 2, function(){});
                                    if(data.reload){
                                        setTimeout(function(){ window.open("<?php echo BASE_URL; ?>custom-product.php","_self");}, 2000);
                                    }
                                },
                                error: function (xhr, textStatus, errorThrown) { alert("error"); }
                            });
                            
                            
                        }else{ alert("Minimum Product Quantity for Selected "+ (u+1) +" is 1");}
                    }
                }
        }
    </script>
<?php } ?>

<?php if($show_alert){ ?>
    <script>
            buyForm();
    </script>
<?php } ?>

<?php require_once('footer.php'); ?>
