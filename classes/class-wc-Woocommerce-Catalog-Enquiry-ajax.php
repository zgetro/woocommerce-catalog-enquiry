<?php
class WC_Woocommerce_Catalog_Enquiry_Ajax {

	public function __construct() {
		
		add_action('wp_ajax_send_enquiry_mail', array(&$this, 'send_product_enqury_mail') );
		add_action( 'wp_ajax_nopriv_send_enquiry_mail', array( &$this, 'send_product_enqury_mail' ) );
	}

	public function send_product_enqury_mail() {
		global $WC_Woocommerce_Catalog_Enquiry, $woocommerce;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;		
		$name = $_POST['woo_customer_name'];
		$email = $_POST['woo_customer_email'];
		
		$subject = $_POST['woo_customer_subject'];
		$phone = $_POST['woo_customer_phone'];
		$comment = $_POST['woo_customer_comment'];
		$address = $_POST['woo_customer_address'];
		$product_name = $_POST['woo_customer_product_name'];
		$product_url = $_POST['woo_customer_product_url'];
		
		$email_admin = get_option( 'admin_email' );
		if(isset($settings['other_emails'])) {
			$email_admin .= ','.$settings['other_emails'];				
		}
		$subject_mail = 'Product Enquiry';
		$email_heading = 'Product Enquiry for '.$product_name;
		$email_footer = "Woocommerce product enquiry";
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
			
		}
		else {
			echo 0;
		}	
		die;	  
	}

}
