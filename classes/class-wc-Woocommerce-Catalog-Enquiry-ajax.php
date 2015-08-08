<?php
class WC_Woocommerce_Catalog_Enquiry_Ajax {

	public function __construct() {
		
		add_action('wp_ajax_send_enquiry_mail', array(&$this, 'send_product_enqury_mail') );
		add_action( 'wp_ajax_nopriv_send_enquiry_mail', array( &$this, 'send_product_enqury_mail' ) );
		add_action( 'wp_ajax_add_variation_for_enquiry_mail', array( $this, 'add_variation_for_enquiry_mail'));
		add_action( 'wp_ajax_nopriv_add_variation_for_enquiry_mail', array( $this, 'add_variation_for_enquiry_mail'));
	}
	
	public function add_variation_for_enquiry_mail() {
		global $WC_Woocommerce_Catalog_Enquiry, $woocommerce;
		$variation_name = $_POST['variation_name'];
		$variation_value = $_POST['variation_value'];
		$product_id = $_POST['product_id'];
		$f1 = 0;
		$i = 0;
		if(isset($_SESSION['variation_list']) && $_SESSION['variation_list'] !='') {
			$variation_list = $_SESSION['variation_list'];		
		}
		else {
			$variation_list = '';
		}
		if($variation_list != '') {
			foreach($variation_list as $variation ) {
				if($variation['variation_name'] == $variation_name && $variation['product_id'] == $product_id) {
					if($variation_value == '') {
						unset($variation_list[$i]);
						$variation_list = array_values(array_filter($variation_list));
					}
					else {
						$variation_list[$i]['variation_value'] = $variation_value;
					}
					$f1 = 1;					
				}
				$i++;				
			}			
		}
		$arr = array('variation_name' => $variation_name, 'variation_value' => $variation_value, 'product_id' => $product_id);
		if($f1 == 0) {
			$variation_list[] = $arr;		
		}
		$_SESSION['variation_list'] = $variation_list;
		//print_r($_SESSION['variation_list']);		
		die;
	}
	
	

	public function send_product_enqury_mail() {
		global $WC_Woocommerce_Catalog_Enquiry, $woocommerce, $product;
		if(!isset($_SESSION)) {
			session_start();
		}
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;		
		$name = $_POST['woo_customer_name'];
		$email = $_POST['woo_customer_email'];
		$product_id = $_POST['woo_customer_product_id'];
		$subject = $_POST['woo_customer_subject'];
		$phone = $_POST['woo_customer_phone'];
		$comment = $_POST['woo_customer_comment'];
		$address = $_POST['woo_customer_address'];
		$product_name = $_POST['woo_customer_product_name'];
		$product_url = $_POST['woo_customer_product_url'];
		$enquiry_product_type = $_POST['enquiry_product_type'];		
		$email_admin = get_option( 'admin_email' );
		$other_info_product = "";
		$other_info = "";
		if(isset($settings['other_emails'])) {
			$email_admin .= ','.$settings['other_emails'];				
		}
		$product = new wc_product($product_id);
		
		if( $enquiry_product_type == 'variable' ){			
			$f2 = 0;
			if(isset($_SESSION['variation_list'])) {
				$variation_list = $_SESSION['variation_list'];
				foreach($variation_list as $variation) {
					if($variation['product_id'] == $product_id) {
						$other_info_product .= "<b>".$variation['variation_name']." : </b> ".$variation['variation_value']."<br/>";						
					}					
				}				
			}
			if($other_info_product != '') {
				$other_info = "<h3>".$other_info_product."</h3>";				
			}
		}
		
		$subject_mail = __('Product Enquiry',$WC_Woocommerce_Catalog_Enquiry->text_domain);
		$email_heading = __('Product Enquiry for ',$WC_Woocommerce_Catalog_Enquiry->text_domain).$product_name;
		$email_footer = __("Woocommerce product enquiry",$WC_Woocommerce_Catalog_Enquiry->text_domain);
		$email_content = '';
		$headers = array('Content-Type: text/html; charset=UTF-8');
		ob_start();
		?>
		<style type="text/css">		
		.container {
			display: block!important;
			max-width: 600px!important;
			margin: 0 auto!important; 
			clear: both!important;
		}		
		.body-wrap .container {
			padding: 20px;
		}		
		.content {
			max-width: 600px;
			margin: 0 auto;
			display: block;
		}		
		.content table {
			width: 100%;
		}		
		</style>


<body bgcolor="#f6f6f6">
<!-- body -->
<table class="body-wrap" bgcolor="#f6f6f6">
	<tr>
		<td></td>
		<td class="container" bgcolor="#FFFFFF">
			<!-- content -->
			<div class="content">
			<table>
				<tr>
					<td>
						<p><?php echo __('Dear Admin',$WC_Woocommerce_Catalog_Enquiry->text_domain);?>,</p>
						<p><?php echo __('Please find the product enquiry, details are given below',$WC_Woocommerce_Catalog_Enquiry->text_domain);?>.</p>
						<h1><?php echo __('Product Name :',$WC_Woocommerce_Catalog_Enquiry->text_domain);?> <?php 	echo $product_name; ?></h1>
						<?php if($other_info != '') { echo $other_info; }?>
						<p><a href="<?php echo $product_url; ?>"> <?php echo __('Find the product Link',$WC_Woocommerce_Catalog_Enquiry->text_domain);?></a></p>
						<h2><?php echo __('User Name : ',$WC_Woocommerce_Catalog_Enquiry->text_domain);?><?php echo $name; ?></h2>
						<p><?php echo __('User Email : ',$WC_Woocommerce_Catalog_Enquiry->text_domain);?><?php echo $email; ?></p>
						<table>						
							<tr>
								<td class="padding">
									<p><?php 
									if($subject!= ''){	echo "User Subject : ".$subject."<br/>";} 
									if($phone != '' ) {
										echo __("User Phone : ",$WC_Woocommerce_Catalog_Enquiry->text_domain).$phone."<br/>";
									}
									if($address != '') {
										echo __("User Address : ",$WC_Woocommerce_Catalog_Enquiry->text_domain).$address."<br/>";
									}
									if($comment != '') {
										echo __("User Comments : ",$WC_Woocommerce_Catalog_Enquiry->text_domain).$comment."<br/>";
									}								
									?></p>
								</td>
							</tr>
						</table>
						<p><?php echo __("Product Name : ",$WC_Woocommerce_Catalog_Enquiry->text_domain).$product_name; ?></p>
						<p><?php echo __("Product Url : ",$WC_Woocommerce_Catalog_Enquiry->text_domain).$product_url;	 ?></p>						
					</td>
				</tr>
			</table>
			</div>
			<!-- /content -->		
		</td>
		<td></td>
	</tr>
</table>
<!-- /body -->
<!-- footer -->
<table class="footer-wrap">
	<tr>
		<td></td>
		<td class="container">			
			<!-- content -->
			<div class="content">
				<table>
					<tr>
						<td align="center">
							<p><?php echo __('Thanks for using this plugin.',$WC_Woocommerce_Catalog_Enquiry->text_domain);?></a>.
							</p>
						</td>
					</tr>
				</table>
			</div>
			<!-- /content -->
			
		</td>
		<td></td>
	</tr>
</table>
<!-- /footer -->

</body>	
		
<?php		
		$email_content = ob_get_clean();
		if(wp_mail( $email_admin, $subject_mail, $email_content, $headers )) {
			echo 1;
			unset($_SESSION['variation_list']);			
		}
		else {
			echo 0;
		}	
		die;	  
	}

}
