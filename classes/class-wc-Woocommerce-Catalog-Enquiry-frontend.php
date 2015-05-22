<?php
class WC_Woocommerce_Catalog_Enquiry_Frontend {

	public function __construct() {
		global $WC_Woocommerce_Catalog_Enquiry;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;
		add_action( 'wc_Woocommerce_Catalog_Enquiry_frontend_hook', array(&$this, 'wc_Woocommerce_Catalog_Enquiry_frontend_function'), 10, 2 );
		//enqueue scripts
		add_action('wp_enqueue_scripts', array(&$this, 'frontend_scripts'));
		//enqueue styles
		add_action('wp_enqueue_scripts', array(&$this, 'frontend_styles'));	
		if($settings['is_enable'] == "Enable") {
			if($settings['is_custom_button'] == "Enable") {	
				if ($settings['button_type'] == 2 || $settings['button_type'] == 3) {
					add_filter('the_permalink', array($this, 'change_permalink'));
				}
			}
		}
	
		$current_user = wp_get_current_user();		
		$for_user_type = $settings['for_user_type'];
		if($for_user_type == 0 || $for_user_type == 3 || $for_user_type == '' ) {
			$this->init_catalog();	
			
		}
		else if($for_user_type == 1) {
			if($current_user->ID == 0) {
				$this->init_catalog();	
				
			}
		}
		else if($for_user_type == 2) {
			if($current_user->ID != 0) {
				$this->init_catalog();	
				
			}			
		}		
	}
	
	
	public function change_permalink() {
		global $product, $WC_Woocommerce_Catalog_Enquiry;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;				
		if(!$product) {
		  return get_permalink($post->ID);
		}else {
			if ($settings['button_type'] == 2) {
				$link = $settings['button_link'];
				return $link;
			}
			else if($settings['button_type'] == 3 ) {
				$link = get_post_field("woo_catalog_enquiry_product_link",$post->ID);
				return $link;				
			}
			else {
				return get_permalink($post->ID);				
			}
		}
		
	}
	
	public function init_catalog() {
		global $WC_Woocommerce_Catalog_Enquiry;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;	
		
		if($settings['is_enable'] == "Enable") {			
			add_action('init',array($this,'remove_add_to_cart_button'));
			if($settings['is_enable_enquiry'] == "Enable" ) {
					add_action('woocommerce_single_product_summary', array($this,'add_form_for_enquiry'),100);				
			}						
			if($settings['is_remove_price'] == "Enable") {
				add_action('init',array($this,'remove_price_from_product_list'));				
			}
			if($settings['is_custom_button'] == "Enable") {
				if($settings['button_type'] == 0 || $settings['button_type'] == '' || $settings['button_type'] == 1) {
					add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );	
					add_filter('woocommerce_loop_add_to_cart_link', array($this,'add_read_more_button'),10);					
				}
				else if($settings['button_type'] == 2) {
					add_filter('woocommerce_loop_add_to_cart_link', array($this,'add_external_link_button'),10);				
				}
				else if($settings['button_type'] == 3) {
					add_filter('woocommerce_loop_add_to_cart_link', array($this,'add_external_link_button_independent'),10);					
				}
				else if($settings['button_type'] == 4) {
					add_filter('woocommerce_loop_add_to_cart_link', array($this,'add_custom_button_without_link'),10);					
				}
			}
		}		
	}
	
	public function add_form_for_enquiry() {		
		global $WC_Woocommerce_Catalog_Enquiry, $woocommerce;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;
		$arr_field = array();
		$arr_field[] = "name";
		$arr_field[] = "email";
		if($settings['is_subject'] == "Enable") {
		$arr_field[] = "subject";	
		}
		if($settings['is_phone'] == "Enable") {
		$arr_field[] = "phone";	
		}
		if($settings['is_address'] == "Enable") {
		$arr_field[] = "address";	
		}
		if($settings['is_comment'] == "Enable") {
		$arr_field[] = "comment";	
		}
		
		
		
		$productid = $post->ID;
		$current_user = wp_get_current_user();
		$product_name = get_post_field('post_title',$productid);
		$product_url = get_permalink($productid);
		
		
		
		$arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0','1','2','3','4','5','6','7','8','9');
		$i = 0;
		$captcha = '';
		while($i < 8) {
			$v1 = rand(0,35);
			$captcha .= $arr[$v1];
			$i++;
		}
		$_SESSION['mycaptcha'] = $captcha;
		?>
		
    
		<div id="woo_catalog" name="woo_catalog" >
		
		<?php if($settings['custom_css_product_page']!="") {?>
			<style type="text/css">
				<?php echo $settings['custom_css_product_page'];?>
				
			</style>
			
		<?php }?>
		<style type="text/css">
		.noselect {
					-webkit-touch-callout: none;
					-webkit-user-select: none;
					-khtml-user-select: none;
					-moz-user-select: none;
					-ms-user-select: none;
					user-select: none;
				}
		#loader_after_sumitting_the_form {
			display:none;
		}
		</style>
		
		<button class="demo btn btn-primary btn-large" style="margin-top:15px;" data-toggle="modal" href="#responsive">Send an enquiry</button>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		<link href="http://getbootstrap.com/2.3.2/assets/css/bootstrap.css" rel="stylesheet" />
		<link href="http://getbootstrap.com/2.3.2/assets/js/google-code-prettify/prettify.css" rel="stylesheet" />
		<link href="http://getbootstrap.com/2.3.2/assets/css/bootstrap-responsive.css" rel="stylesheet" />
    <link href="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/css/bootstrap-modal.css" rel="stylesheet" /> 
    <?php if($settings['load_js_lib'] == "Enable") {?>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <?php }?>
    <script type="text/javascript" src="http://getbootstrap.com/2.3.2/assets/js/google-code-prettify/prettify.js"></script>
    <script src="http://getbootstrap.com/2.3.2/assets/js/bootstrap.js"></script>
    <script src="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/js/bootstrap-modalmanager.js"></script>
    <script src="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/js/bootstrap-modal.js"></script>
    <script type="text/javascript" >
			function submitthis(str) {
				var name = document.getElementById('woo_user_name').value;				
				var email = document.getElementById('woo_user_email').value;
				var subject = '';
				var phone = '';
				var address = '';
				var comment = '';
				var json_arr = <?php echo json_encode( $arr_field ) ?>;					
				if(json_arr.indexOf("subject") != -1) {
					subject = document.getElementById('woo_user_subject').value;					
				}				
				if(json_arr.indexOf("phone") != -1) {
					phone = document.getElementById('woo_user_phone').value;					
				}				
				if(json_arr.indexOf("address") != -1) {
					address = document.getElementById('woo_user_address').value;					
				}				
				if(json_arr.indexOf("comment") != -1) {
					comment = document.getElementById('woo_user_comment').value;					
				}									
				var product_name = document.getElementById('product_name_for_enquiry').value;				
				var product_url = document.getElementById('product_url_for_enquiry').value;				
				var captcha = document.getElementById('woo_catalog_captcha');				
				
				if(name == '' || name == ' ') {
					document.getElementById('msg_for_enquiry_error').innerHTML='Name is required field';					
					document.getElementById('woo_user_name').focus();
					return false;					
				}
				
				if(email == '' || email == ' ') {
					document.getElementById('msg_for_enquiry_error').innerHTML='Email is required field';					
					document.getElementById('woo_user_email').focus();
					return false;					
				}
				
				if(captcha.value == '' || captcha.value == ' ' ) {					
					document.getElementById('msg_for_enquiry_error').innerHTML='Please enter the security code';					
					document.getElementById('woo_catalog_captcha').focus();
					return false;					
				}
				if(captcha.value != '<?php echo $captcha; ?>' ) {
					document.getElementById('msg_for_enquiry_error').innerHTML='Please enter the valid seurity code';					
					document.getElementById('woo_catalog_captcha').focus();
					return false;					
				}
				jQuery("#loader_after_sumitting_the_form").show();
				document.getElementById('msg_for_enquiry_error').innerHTML='';
			
				var ajax_url = '<?php echo admin_url( 'admin-ajax.php' ) ?>';
				
				var data = {
										 'action': 'send_enquiry_mail',
										 'woo_customer_name': name,
										 'woo_customer_email': email,
										 'woo_customer_subject': subject,
										 'woo_customer_phone': phone,
										 'woo_customer_address': address,
										 'woo_customer_comment': comment,
										 'woo_customer_product_name': product_name,
										 'woo_customer_product_url': product_url										 
				};
				jQuery.post(ajax_url, data, function(response) {  
						
						//alert(response);
					 if(response.trim()==1) {	
					 	 jQuery("#loader_after_sumitting_the_form").hide();					 	 
						 jQuery('#msg_for_enquiry_sucesss').html('Enquiry sent successfully');
						 setTimeout(function(){
																			jQuery('#responsive').modal('hide');
																			jQuery('#msg_for_enquiry_sucesss').html('');
																			jQuery('#woo_user_name').val('');
																			jQuery('#woo_user_email').val('');
																			jQuery('#woo_catalog_captcha').val('');
																			if(json_arr.indexOf("subject") != -1) {
																				jQuery('#woo_user_subject').val('');																									
																			}				
																			if(json_arr.indexOf("phone") != -1) {
																				jQuery('#woo_user_phone').val('');																									
																			}				
																			if(json_arr.indexOf("address") != -1) {
																				jQuery('#woo_user_address').val('');
																									
																			}				
																			if(json_arr.indexOf("comment") != -1) {
																				jQuery('#woo_user_comment').val('');																									
																			}
																	}, 3000);
						 
						 
					 }
					 else {	
					 	 jQuery("#loader_after_sumitting_the_form").hide();
						 jQuery('#msg_for_enquiry_error').html('error in system please try later');									 
					 }					
				});						
			}		
		</script>
		
		<input type="hidden" name="product_name_for_enquiry" id="product_name_for_enquiry" value="<?php echo get_post_field('post_title',$post->ID); ?>" />
		<input type="hidden" name="product_url_for_enquiry" id="product_url_for_enquiry" value="<?php echo get_permalink($post->ID); ?>" />
		<div id="responsive"  class="modal hide fade" tabindex="-1" data-width="760">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>Enquiry about <?php echo $product_name; ?></h3>
		</div>
			<div class="modal-body">
				<div class="row-fluid">
					<div class="span12">
						<h5><?php echo $settings['top_content_form']; ?></h5>
						<p id="msg_for_enquiry_error" style="color:#f00; text-align:center;"></p>
						<p id="msg_for_enquiry_sucesss" style="color:#0f0; text-align:center;"></p>
						<p id="loader_after_sumitting_the_form" style="text-align:center;"><img src="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/images/loader.gif" ></p>
						
						<p><?php echo __('Enter your name : ',$WC_Woocommerce_Catalog_Enquiry->text_domain); ?></p>	
						<p><input name="woo_user_name" id="woo_user_name"  type="text" value="<?php echo $current_user->display_name; ?>" class="span12" /></p>
						
						<p><?php echo __('Enter your Email Id : ',$WC_Woocommerce_Catalog_Enquiry->text_domain); ?></p>	
						<p><input name="woo_user_email" id="woo_user_email"  type="email" value="<?php echo $current_user->user_email; ?>" class="span12" /></p>
						
						<?php if($settings['is_subject']=="Enable") { ?>
						<p><?php echo __('Enter enquiry subject : ',$WC_Woocommerce_Catalog_Enquiry->text_domain); ?></p>	
						<p><input name="woo_user_subject" id="woo_user_subject"  type="text" value="Product enquiry" class="span12" /></p>
						<?php } ?>
						<?php if($settings['is_phone']=="Enable") { ?>
						<p><?php echo __('Enter your phone no : ',$WC_Woocommerce_Catalog_Enquiry->text_domain); ?></p>	
						<p><input name="woo_user_phone" id="woo_user_phone"  type="text" value="" class="span12" /></p>
						<?php } ?>
						<?php if($settings['is_address']=="Enable") { ?>
						<p><?php echo __('Enter your address : ',$WC_Woocommerce_Catalog_Enquiry->text_domain); ?></p>	
						<p><input name="woo_user_address" id="woo_user_address"  type="text" value="" class="span12" /></p>
						<?php } ?>
						<?php if($settings['is_comment']=="Enable") { ?>
						<p><?php echo __('Enter your Message : ',$WC_Woocommerce_Catalog_Enquiry->text_domain); ?></p>	
						<p><textarea name="woo_user_comment" id="woo_user_comment"  rows="5" class="span12"></textarea></p>
						<?php } ?>
						
						<?php do_action( 'woocommerce_catalog_enquiry_form_product_page' ); ?> 
						
						<p><?php echo  __('Security Code',$WC_Woocommerce_Catalog_Enquiry->text_domain); ?> <span class="noselect" style="background:#000; color:#fff; border:1px solid #333; padding:5px; letter-spacing: 5px; font-size:18px;" ><i><?php echo $_SESSION['mycaptcha'];	?></i></span></p>
						<p><?php echo __('Enter the security code shown above',$WC_Woocommerce_Catalog_Enquiry->text_domain); ?> </p>
						<p><input type="text" id="woo_catalog_captcha" name="woo_captcha" class="span12" /></p>
						
						<h5><?php echo $settings['bottom_content_form']; ?></h5>
				</div>
			
		</div>
		</div>
		<div class="modal-footer">
			
			<button type="button" data-dismiss="modal" class="btn">Close</button>
			<button onclick="submitthis('frm_woo_catalog');" type="button" id="woo_submit_enquiry" class="btn btn-primary">Send</button>
		</div>
	</div>		
		
		
				
		</div>		
		<?php		
		
	}
	
	public function custom_button_style() {
			global $WC_Woocommerce_Catalog_Enquiry;
			$settings = $WC_Woocommerce_Catalog_Enquiry->options;
			
		?>
		<style type="text/css" >
		#woo_catalog_enquiry_custom_button {
			background: <?php if($settings['button_background_color']!=''){ echo $settings['button_background_color'];} else { echo '#013ADF'; }   ?>;
			color: <?php if($settings['button_text_color']!=""){ echo $settings['button_text_color'];} else { echo "#FFF"; } ?>;
			padding: <?php if($settings['button_padding']!=""){ echo $settings['button_padding']."px";} else { echo "5px"; } ?>;
			width: <?php if($settings['button_width']!=""){ echo $settings['button_width']."px";} else { echo "80px"; } ?>;
			height: <?php if($settings['button_height']!=""){ echo $settings['button_height']."px";} else { echo "20px"; } ?>;
			line-height: <?php if($settings['button_height']!=""){ echo $settings['button_height']."px";} else { echo "20px"; } ?>;
			border-radius: <?php if($settings['button_border_redius']!=""){ echo $settings['button_border_redius']."px";} else { echo "5px"; } ?>;
			border: <?php if($settings['button_border_size']!=""){ echo $settings['button_border_size']."px";} else { echo "1px"; } ?> solid  <?php if($settings['button_border_color']!=""){ echo $settings['button_border_color'];} else { echo "#333"; } ?>;
			font-size: <?php if($settings['button_fornt_size']!=""){ echo $settings['button_fornt_size']."px";} else { echo "12px"; } ?>;
			margin-top : <?php if($settings['button_margin_top']!=""){ echo $settings['button_margin_top']."px";} else { echo "5px"; } ?>;
			margin-bottom : <?php if($settings['button_margin_bottom']!=""){ echo $settings['button_margin_bottom']."px";} else { echo "5px"; } ?>;
			
		}
		#woo_catalog_enquiry_custom_button:hover {
				background: <?php if($settings['button_background_color_hover']!=""){ echo $settings['button_background_color_hover'];} else { echo "#0431B4"; }   ?>;
				color: <?php if($settings['button_text_color_hover']!=""){ echo $settings['button_text_color_hover'];} else { echo "#CECEF6"; }   ?>;
		}		
		</style>		
		<?php		
	}
	
	
	
	public function add_read_more_button() {
		global $WC_Woocommerce_Catalog_Enquiry;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;
		$this->custom_button_style();
		$button_text = "Read More";
		if($settings['button_text']!="" || $settings['button_text']!=" ") {
			$button_text = $settings['button_text'];
		}
		$link = get_permalink($post->ID);
		echo ' <center><a  id="woo_catalog_enquiry_custom_button" href="' . $link  . '" class="single_add_to_cart_button button">'.$button_text.'</a></center>';
		
	}
	
	public function add_external_link_button() {
		global $WC_Woocommerce_Catalog_Enquiry;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;
		$this->custom_button_style();
		$button_text = "Read More";
		if($settings['button_text']!="" || $settings['button_text']!=" ") {
			$button_text = $settings['button_text'];
		}
		$link = $settings['button_link'];
		echo ' <center><a  id="woo_catalog_enquiry_custom_button" href="' . $link  . '" class="single_add_to_cart_button button">'.$button_text.'</a></center>';
		
	}
	
	public function add_external_link_button_independent() {
		global $WC_Woocommerce_Catalog_Enquiry;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;
		$this->custom_button_style();
		$button_text = "Read More";
		if($settings['button_text']!="" || $settings['button_text']!=" ") {
			$button_text = $settings['button_text'];
		}
		$link = get_post_field("woo_catalog_enquiry_product_link",$post->ID);
		echo ' <center><a id="woo_catalog_enquiry_custom_button" href="' . $link  . '" class="single_add_to_cart_button button">'.$button_text.'</a></center>';
		
	}
	
	public function add_custom_button_without_link() {
		global $WC_Woocommerce_Catalog_Enquiry;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;
		$this->custom_button_style();
		$button_text = "Read More";
		if($settings['button_text']!="" || $settings['button_text']!=" ") {
			$button_text = $settings['button_text'];
		}
		$link = "#";
		echo ' <center><a id="woo_catalog_enquiry_custom_button" href="' . $link  . '" class="single_add_to_cart_button button">'.$button_text.'</a></center>';
		
	}
	
	
	
	
	
	public function remove_add_to_cart_button(){
		global $WC_Woocommerce_Catalog_Enquiry;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;
		if($settings['is_custom_button']!="Enable") {
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );	
		}		
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	}	
	
	public function remove_price_from_product_list(){		
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );		
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );		
	}
	
	

	function frontend_scripts() {
		global $WC_Woocommerce_Catalog_Enquiry;
		$frontend_script_path = $WC_Woocommerce_Catalog_Enquiry->plugin_url . 'assets/frontend/js/';
		$frontend_script_path = str_replace( array( 'http:', 'https:' ), '', $frontend_script_path );
		$pluginURL = str_replace( array( 'http:', 'https:' ), '', $WC_Woocommerce_Catalog_Enquiry->plugin_url );
		$suffix 				= defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		
		// Enqueue your frontend javascript from here
	}

	function frontend_styles() {
		global $WC_Woocommerce_Catalog_Enquiry;
		$frontend_style_path = $WC_Woocommerce_Catalog_Enquiry->plugin_url . 'assets/frontend/css/';
		$frontend_style_path = str_replace( array( 'http:', 'https:' ), '', $frontend_style_path );
		$suffix 				= defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Enqueue your frontend stylesheet from here
	}
	
	function dc_wc_Woocommerce_Catalog_Enquiry_frontend_function() {
	  // Do your frontend work here
	  
	}

}
