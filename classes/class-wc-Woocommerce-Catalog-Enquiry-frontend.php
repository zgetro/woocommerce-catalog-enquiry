<?php
class WC_Woocommerce_Catalog_Enquiry_Frontend {
	
	public $available_for;
	
	public function __construct() {
		global $WC_Woocommerce_Catalog_Enquiry, $post;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;
		$exclusion = $WC_Woocommerce_Catalog_Enquiry->options_exclusion;
		add_action( 'wc_Woocommerce_Catalog_Enquiry_frontend_hook', array($this, 'wc_Woocommerce_Catalog_Enquiry_frontend_function'), 10, 2 );
		//enqueue scripts
		add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
		//enqueue styles
		add_action('wp_enqueue_scripts', array($this, 'frontend_styles'));	
		add_action('template_redirect', array($this, 'redirect_cart_checkout_on_conditions'));
	
		$current_user = wp_get_current_user();		
		$user_id = $current_user->ID;		
		$this->available_for = '';
		
		
		
		
		if(isset( $exclusion['is_exclusion'] ) && $exclusion['is_exclusion'] == 'Enable' ) {
			if( isset( $exclusion['myuser_list'] ) ) {
				if(is_array($exclusion['myuser_list'])) {
					if(in_array($current_user->ID,$exclusion['myuser_list'])) {
						$this->available_for = $current_user->ID;							
					}					
				}				
			}			
		}
				
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
		
		
		
		if(isset($settings['is_enable']) && $settings['is_enable'] == "Enable") {						
			if(isset($settings['is_custom_button']) && $settings['is_custom_button'] == "Enable") {	
				if (isset($settings['button_type']) && ($settings['button_type'] == 2 || $settings['button_type'] == 3)) {
					add_filter('the_permalink', array($this, 'change_permalink'),10);
				}
			}
		}			
	}
	
	public function load_footer_script_at_last() {
		global $WC_Woocommerce_Catalog_Enquiry;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;	
		?>
		 
    <?php if( isset($settings['load_js_lib']) && $settings['load_js_lib'] == "Enable") {?>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <?php }?>
    <script type="text/javascript" src="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/js/prettify.js"></script>
    <script src="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/js/bootstrap.js"></script>
    <script src="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/js/bootstrap-modalmanager.js"></script>
    <script src="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/js/bootstrap-modal.js"></script>
		
		<?php
	}
	
	
	public function redirect_cart_checkout_on_conditions() {
		global $WC_Woocommerce_Catalog_Enquiry, $post;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;
		$exclusion = $WC_Woocommerce_Catalog_Enquiry->options_exclusion;
		$current_user = wp_get_current_user();		
		$user_id = $current_user->ID;
		
		$count1 = 0;
		$count2 = 0;
		$count3 = 0;
		
		if(isset($settings['is_enable']) && $settings['is_enable'] == "Enable") {
			if(isset($settings['is_hide_cart_checkout']) && $settings['is_hide_cart_checkout'] == "Enable") {
				if(isset( $exclusion['is_exclusion'] ) && $exclusion['is_exclusion'] == 'Enable' ) {
					
					if( isset( $exclusion['myuser_list'] ) ) {
						if(is_array($exclusion['myuser_list'])) {
							$count1 = count($exclusion['myuser_list']);
							
						}						
					}
					if( isset( $exclusion['myproduct_list'] ) ) {
						if(is_array($exclusion['myproduct_list'])) {
							$count2 = count($exclusion['myproduct_list']);	
													
						}						
					}	
					if( isset( $exclusion['mycategory_list'] ) ) {
						if(is_array($exclusion['mycategory_list'])) {
							$count3 = count($exclusion['mycategory_list']);								
						}						
					}
				}
				$cart_page_id = woocommerce_get_page_id( 'cart');						
				$checkout_page_id = woocommerce_get_page_id( 'checkout');
				
				if($count2 == 0 && $count3 == 0 && $count1 == 0) {
					
										
					if(is_page( $cart_page_id ) || is_page( $checkout_page_id) ) {						
					  wp_redirect( home_url() ); exit; 						
					}					
				}
				else if ( $count2 == 0 && $count3 == 0 ) {
					if(!in_array($current_user->ID,$exclusion['myuser_list'])){
						if(is_page( (int)$cart_page_id ) || is_page( $checkout_page_id ) ) {
							wp_redirect( home_url() ); exit; 						
						}						
					}
				}				
			}			
			
		}			
	}
		
		
	
	
	
	public function change_permalink() {
		global $product, $WC_Woocommerce_Catalog_Enquiry, $post;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;				
		if(!$product) {
		  return get_permalink($post->ID);
		}
		else {
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
		$exclusion = $WC_Woocommerce_Catalog_Enquiry->options_exclusion;
		
		
		if(isset($settings['is_enable']) && $settings['is_enable'] == "Enable" && ($this->available_for == '' ||  $this->available_for == 0)) {			
			add_action('init',array($this,'remove_add_to_cart_button'));
			if(isset($settings['is_enable_enquiry']) && $settings['is_enable_enquiry'] == "Enable" ) {
				if(isset($settings['is_disable_popup']) && $settings['is_disable_popup'] == "Enable" ) {
					add_action('woocommerce_single_product_summary', array($this,'add_form_for_enquiry_without_popup'),100);
				}
				else {
					add_action('woocommerce_single_product_summary', array($this,'add_form_for_enquiry'),100);	
				}
			}						
			if(isset($settings['is_remove_price']) && $settings['is_remove_price'] == "Enable") {
				add_action('init',array($this,'remove_price_from_product_list_loop'),10);
				add_action('woocommerce_single_product_summary',array($this,'remove_price_from_product_list_single'),5);				
			}
			if(isset($settings['is_custom_button']) && $settings['is_custom_button'] == "Enable") {
				if((isset($settings['button_type'])) && ($settings['button_type'] == 0 || $settings['button_type'] == '' || $settings['button_type'] == 1)) {
					add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );	
					add_filter('woocommerce_loop_add_to_cart_link', array($this,'add_read_more_button'),10);					
				}
				else if(isset($settings['button_type']) && $settings['button_type'] == 2) {
					add_filter('woocommerce_loop_add_to_cart_link', array($this,'add_external_link_button'),10);				
				}
				else if(isset($settings['button_type']) &&  $settings['button_type'] == 3) {
					add_filter('woocommerce_loop_add_to_cart_link', array($this,'add_external_link_button_independent'),10);					
				}
				else if(isset($settings['button_type']) && $settings['button_type'] == 4) {
					add_filter('woocommerce_loop_add_to_cart_link', array($this,'add_custom_button_without_link'),10);					
				}
			}
			add_action('woocommerce_after_shop_loop_item_title' , array ($this, 'price_for_selected_product'),5);
			add_action('woocommerce_after_shop_loop_item' , array ($this, 'add_to_cart_button_for_selected_product'),5);
			add_action('woocommerce_before_shop_loop_item', array ($this, 'change_permalink_url_for_selected_product'),5);
			add_action( 'woocommerce_single_product_summary', array($this, 'catalog_woocommerce_template_single'), 5 );
			
		}		
	}
	public function change_permalink_url_for_selected_product() {
		global $WC_Woocommerce_Catalog_Enquiry, $post;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;
		$exclusion = $WC_Woocommerce_Catalog_Enquiry->options_exclusion;
		$product_for = '';
		
		if(isset( $exclusion['is_exclusion'] ) && $exclusion['is_exclusion'] == 'Enable' ) {
			if( isset( $exclusion['myproduct_list'] ) ) {
				if(is_array($exclusion['myproduct_list']) && isset($post->ID)) {
					if(in_array($post->ID,$exclusion['myproduct_list'])) {
						
						$product_for = $post->ID;							
					}
					else {
						 $product_for = '';
						
					}
				}						
			} 					
		}
		if($product_for == $post->ID) {
			remove_filter('the_permalink', array($this, 'change_permalink'),10);			
		} else {
			add_filter('the_permalink', array($this, 'change_permalink'),10);
			
		}
	}	
	
	
	
	public function catalog_woocommerce_template_single() {	
		global $WC_Woocommerce_Catalog_Enquiry, $post, $product;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;
		$exclusion = $WC_Woocommerce_Catalog_Enquiry->options_exclusion;
		$product_for = '';
		
		if(isset( $exclusion['is_exclusion'] ) && $exclusion['is_exclusion'] == 'Enable' ) {
			if( isset( $exclusion['myproduct_list'] ) ) {
				if(is_array($exclusion['myproduct_list']) && isset($post->ID)) {
					if(in_array($post->ID,$exclusion['myproduct_list'])) {
						
						$product_for = $post->ID;							
					} 
					else { $product_for = ''; }					
				} 
				else { $product_for = ''; }				
			} 
			else { $product_for = ''; }			
		} 
		else { $product_for = ''; }
		
		$category_for = '';
		if(isset( $exclusion['is_exclusion'] ) && $exclusion['is_exclusion'] == 'Enable' ) {
			if( isset( $exclusion['mycategory_list'] ) ) {
				if(is_array($exclusion['mycategory_list'])) {					
					if(isset($product)) {
						$term_list = wp_get_post_terms($post->ID,'product_cat',array('fields'=>'ids'));
						
						if(count(array_intersect($term_list, $exclusion['mycategory_list'])) > 0) {
							$category_for = $post->ID;
						}
						else {
							$category_for = '';
						}
					}
					else {  $category_for = ''; }										
				} 
				else { $category_for = ''; }				
			} 
			else { $category_for = ''; }			
		} 
		else { $category_for = ''; }
				
		
		
	
		if($product_for == $post->ID ||  $category_for == $post->ID) {			
			remove_action('woocommerce_single_product_summary', array($this,'add_form_for_enquiry'),100);
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );			
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			remove_action( 'woocommerce_single_product_summary', array($this,'add_variation_product'),29 );
		}
	}
	
	
	public function add_form_for_enquiry_without_popup() {		
		global $WC_Woocommerce_Catalog_Enquiry, $woocommerce, $post, $product;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;
		$is_page_redirect = '';
		if(isset($settings['is_page_redirect'])) {
			$is_page_redirect = $settings['is_page_redirect'];
			$redirect_page_id = $settings['redirect_page_id'];
		}
		$settings_buttons = $WC_Woocommerce_Catalog_Enquiry->option_button;
		if(isset($settings_buttons)) {
			
			$custom_design_for_button = isset($settings_buttons['is_button']) ?	$settings_buttons['is_button'] : '';
			$background_color = isset($settings_buttons['button_background_color']) ? $settings_buttons['button_background_color'] : '#ccc';
			$button_text = isset($settings_buttons['button_text']) ? $settings_buttons['button_text'] : __('Send an enquiry',$WC_Woocommerce_Catalog_Enquiry->text_domain);
			$button_text_color = isset($settings_buttons['button_text_color']) ? $settings_buttons['button_text_color'] : '#fff';
			$button_text_color_hover = isset($settings_buttons['button_text_color_hover']) ? $settings_buttons['button_text_color_hover'] : '#ccc';
			$button_background_color_hover = isset($settings_buttons['button_background_color_hover']) ? $settings_buttons['button_background_color_hover'] : '#eee';
			$button_width = isset($settings_buttons['button_width']) ? $settings_buttons['button_width'].'px' : '200px';
			$button_height = isset($settings_buttons['button_height']) ? $settings_buttons['button_height'].'px' : '50px';
			$button_padding = isset($settings_buttons['button_padding']) ? $settings_buttons['button_padding'].'px' : '10px';
			$button_border_size = isset($settings_buttons['button_border_size']) ? $settings_buttons['button_border_size'].'px' : '1px';
			$button_fornt_size = isset($settings_buttons['button_fornt_size']) ? $settings_buttons['button_fornt_size'].'px' : '18px';
			$button_border_redius = isset($settings_buttons['button_border_redius']) ? $settings_buttons['button_border_redius'].'px' : '5px';
			$button_border_color = isset($settings_buttons['button_border_color']) ? $settings_buttons['button_border_color'] : '#999';
			$button_margin_top = isset($settings_buttons['button_margin_top']) ? $settings_buttons['button_margin_top'].'px' : '0px';
			$button_margin_bottom = isset($settings_buttons['button_margin_bottom']) ? $settings_buttons['button_margin_bottom'].'px' : '0px';
			if($button_text == '') {
				$button_text = __('Send an enquiry',$WC_Woocommerce_Catalog_Enquiry->text_domain);
			}
			?>
			<style type="text/css" >
			.woo_catalog_enquiry_custom_button_enquiry {
				background: <?php echo $background_color; ?>;
				color: <?php echo $button_text_color; ?>;
				padding: <?php echo $button_padding;  ?>;
				width: <?php echo $button_width; ?>;
				height: <?php echo $button_height; ?>;
				line-height: <?php echo $button_fornt_size; ?>;
				border-radius: <?php echo $button_border_redius; ?>;
				border: <?php echo $button_border_size.' solid '.$button_border_color; ?>;
				font-size: <?php echo $button_fornt_size; ?>;
				margin-top : <?php echo $button_margin_top; ?>;
				margin-bottom : <?php echo $button_margin_bottom; ?>;
			
			}
			.woo_catalog_enquiry_custom_button_enquiry:hover {
				background: <?php echo $button_text_color_hover;   ?>;
				color: <?php echo $button_background_color_hover;   ?>;
			}		
		</style>
		
		<?php }
		
		
		
		
		
		
		$arr_field = array();
		$arr_field[] = "name";
		$arr_field[] = "email";
		if(isset($settings['is_subject']) && $settings['is_subject'] == "Enable") {
		$arr_field[] = "subject";	
		}
		if(isset($settings['is_phone']) && $settings['is_phone'] == "Enable") {
		$arr_field[] = "phone";	
		}
		if(isset($settings['is_address']) && $settings['is_address'] == "Enable") {
		$arr_field[] = "address";	
		}
		if(isset($settings['is_comment']) && $settings['is_comment'] == "Enable") {
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
		<?php if(isset($settings['custom_css_product_page']) && $settings['custom_css_product_page']!="") {?>
			<style type="text/css">
				<?php echo $settings['custom_css_product_page'];?>				
			</style>			
		<?php }?>	
		<?php if(isset($custom_design_for_button) && $custom_design_for_button == "Enable" ) {?>
			<br/>
			<button class="woo_catalog_enquiry_custom_button_enquiry"  onclick="do_toggle();" data-toggle="modal" href="#responsive"><?php echo $button_text;?></button>
			<?php
		}
		else {?>
		<button class="demo btn btn-primary btn-large" style="margin-top:15px;" onclick="do_toggle();" data-toggle="modal" href="#responsive"><?php echo __('Send an enquiry',$WC_Woocommerce_Catalog_Enquiry->text_domain)?></button>
		<?php }?>
    <script type="text/javascript" >
    	function validateEmail($email) {
				var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
				return emailReg.test( $email );
			}
			function submitthis(str) {
				var name = document.getElementById('woo_user_name').value;				
				var email = document.getElementById('woo_user_email').value;
				var enquiry_product_type = document.getElementById('enquiry_product_type').value;
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
				var product_id = document.getElementById('product_id_for_enquiry').value;	
				<?php if( isset($settings['is_captcha']) && $settings['is_captcha']=="Enable") { ?>
				var captcha = document.getElementById('woo_catalog_captcha');	
				<?php }?>
				
				if(name == '' || name == ' ') {
					document.getElementById('msg_for_enquiry_error').innerHTML='<?php echo __('Name is required field',$WC_Woocommerce_Catalog_Enquiry->text_domain); ?>';					
					document.getElementById('woo_user_name').focus();
					return false;					
				}
				
				if(email == '' || email == ' ') {
					document.getElementById('msg_for_enquiry_error').innerHTML='<?php echo __('Email is required field', $WC_Woocommerce_Catalog_Enquiry->text_domain);?>';					
					document.getElementById('woo_user_email').focus();
					return false;					
				}
				if( !validateEmail(email)) {
					document.getElementById('msg_for_enquiry_error').innerHTML='<?php echo __('Please Enter Valid Email Id', $WC_Woocommerce_Catalog_Enquiry->text_domain);?>';
					document.getElementById('woo_user_email').focus();
					return false;
				}
				
				<?php if( isset($settings['is_captcha']) && $settings['is_captcha']=="Enable") { ?>
				
				if(captcha.value == '' || captcha.value == ' ' ) {					
					document.getElementById('msg_for_enquiry_error').innerHTML='<?php echo __('Please enter the security code',$WC_Woocommerce_Catalog_Enquiry->text_domain);?>';					
					document.getElementById('woo_catalog_captcha').focus();
					return false;					
				}
				if(captcha.value != '<?php echo $captcha; ?>' ) {
					document.getElementById('msg_for_enquiry_error').innerHTML='<?php echo __('Please enter the valid seurity code',$WC_Woocommerce_Catalog_Enquiry->text_domain);?>';					
					document.getElementById('woo_catalog_captcha').focus();
					return false;					
				}
				<?php }?>
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
										 'woo_customer_product_url': product_url,
										 'woo_customer_product_id' : product_id,
										 'enquiry_product_type' : enquiry_product_type
										 
				};
				jQuery.post(ajax_url, data, function(response) {					
						
					if(response.trim()==1) {	
						jQuery("#loader_after_sumitting_the_form").hide();					 	 
						jQuery('#msg_for_enquiry_sucesss').html('<?php echo __('Enquiry sent successfully',$WC_Woocommerce_Catalog_Enquiry->text_domain);?>');												
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
						<?php
						if($is_page_redirect != '') {
						?>
						window.location.href='<?php echo get_permalink($redirect_page_id); ?>';
						<?php }?>											 
					}
					else {	
						jQuery("#loader_after_sumitting_the_form").hide();
						jQuery('#msg_for_enquiry_error').html('<?php echo __('Error in system please try later',$WC_Woocommerce_Catalog_Enquiry->text_domain);?>');									 
					}					
				});						
			}
			function do_toggle() {
				jQuery(document).ready(function($){
				  $("#responsive").slideToggle(1000);						
				});				
			}
		</script>		
		<input type="hidden" name="product_name_for_enquiry" id="product_name_for_enquiry" value="<?php echo get_post_field('post_title',$post->ID); ?>" />
		<input type="hidden" name="product_url_for_enquiry" id="product_url_for_enquiry" value="<?php echo get_permalink($post->ID); ?>" />
		<input type="hidden" name="product_id_for_enquiry" id="product_id_for_enquiry" value="<?php echo $post->ID; ?>" />
		<input type="hidden" name="enquiry_product_type" id="enquiry_product_type" value="<?php if( $product->is_type( 'variable' ) ) { echo 'variable'; } ?>" />
		<div id="responsive"  class="catalog_enquiry_form" tabindex="-1" style="width:100%;">
		<div class="modal-header">
			<?php if( isset($settings['is_override_form_heading'])) { ?>
				<?php if( isset($settings['custom_static_heading'])) { ?>
					<h2 style="font-size:20px;"><?php echo  $settings['custom_static_heading']; ?></h2>
				<?php }?>
			<?php } else{?>
			<h2 style="font-size:20px;"><?php echo __('Enquiry about ',$WC_Woocommerce_Catalog_Enquiry->text_domain)?> <?php echo $product_name; ?></h2>
			<?php }?>
		</div>
			<div class="modal-body">
				<div class="row-fluid">
					<div class="span12">
						<p><?php if( isset($settings['top_content_form'])) { echo $settings['top_content_form']; }?></p>
						<p id="msg_for_enquiry_error" style="color:#f00; text-align:center;"></p>
						<p id="msg_for_enquiry_sucesss" style="color:#0f0; text-align:center;"></p>
						<p id="loader_after_sumitting_the_form" style="text-align:center;"><img src="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/images/loader.gif" ></p>
						
						<p><?php if( isset($settings['name_label']) && $settings['name_label'] != '' && $settings['name_label'] !=' ') { echo $settings['name_label']; } else { echo __('Enter your name : ',$WC_Woocommerce_Catalog_Enquiry->text_domain); } ?></p>	
						<p><input name="woo_user_name" id="woo_user_name"  type="text" value="<?php echo $current_user->display_name; ?>" class="span12" /></p>
						
						<p><?php if( isset($settings['email_label']) && $settings['email_label'] != '' && $settings['email_label'] !=' ') { echo $settings['email_label']; } else { echo __('Enter your Email Id : ',$WC_Woocommerce_Catalog_Enquiry->text_domain); }?></p>	
						<p><input name="woo_user_email" id="woo_user_email"  type="email" value="<?php echo $current_user->user_email; ?>" class="span12" /></p>
						
						<?php if( isset($settings['is_subject']) && $settings['is_subject']=="Enable") { ?>
						<p><?php if( isset($settings['subject_label']) && $settings['subject_label'] != '' && $settings['subject_label'] !=' ') { echo $settings['subject_label']; } else { echo __('Enter enquiry subject : ',$WC_Woocommerce_Catalog_Enquiry->text_domain); } ?></p>	
						<p><input name="woo_user_subject" id="woo_user_subject"  type="text" value="<?php echo __('Enquiry about',$WC_Woocommerce_Catalog_Enquiry->text_domain); ?> <?php echo $product_name; ?>" class="span12" /></p>
						<?php } ?>
						<?php if( isset($settings['is_phone']) && $settings['is_phone']=="Enable") { ?>
						<p><?php if( isset($settings['phone_label']) && $settings['phone_label'] != '' && $settings['phone_label'] !=' ') { echo $settings['phone_label']; } else { echo __('Enter your phone no : ',$WC_Woocommerce_Catalog_Enquiry->text_domain); } ?></p>	
						<p><input name="woo_user_phone" id="woo_user_phone"  type="text" value="" class="span12" /></p>
						<?php } ?>
						<?php if( isset($settings['is_address']) && $settings['is_address']=="Enable") { ?>
						<p><?php if( isset($settings['address_label']) && $settings['address_label'] != '' && $settings['address_label'] !=' ') { echo $settings['address_label']; } else { echo __('Enter your address : ',$WC_Woocommerce_Catalog_Enquiry->text_domain); } ?></p>	
						<p><input name="woo_user_address" id="woo_user_address"  type="text" value="" class="span12" /></p>
						<?php } ?>
						<?php if( isset($settings['is_comment']) && $settings['is_comment']=="Enable") { ?>
						<p><?php if( isset($settings['comment_label']) && $settings['comment_label'] != '' && $settings['comment_label'] !=' ') { echo $settings['comment_label']; } else { echo __('Enter your Message : ',$WC_Woocommerce_Catalog_Enquiry->text_domain); } ?></p>	
						<p><textarea name="woo_user_comment" id="woo_user_comment"  rows="5" class="span12"></textarea></p>
						<?php } ?>
						
						<?php do_action( 'woocommerce_catalog_enquiry_form_product_page' ); ?> 
						<?php if( isset($settings['is_captcha']) && $settings['is_captcha']=="Enable") { ?>
						<p><?php if( isset($settings['captcha_label']) && $settings['captcha_label'] != '' && $settings['captcha_label'] !=' ') { echo $settings['captcha_label']; } else { echo  __('Security Code',$WC_Woocommerce_Catalog_Enquiry->text_domain); } ?> <span class="noselect" style="background:#000; color:#fff; border:1px solid #333; padding:5px; letter-spacing: 5px; font-size:18px;" ><i><?php echo $_SESSION['mycaptcha'];	?></i></span></p>
						<p><?php if( isset($settings['captcha_input_label'])&& $settings['captcha_input_label'] != '' && $settings['captcha_input_label'] !=' ' ) { echo $settings['captcha_input_label']; } else { echo __('Enter the security code shown above',$WC_Woocommerce_Catalog_Enquiry->text_domain); } ?> </p>
						<p><input type="text" id="woo_catalog_captcha" name="woo_captcha" class="span12" /></p>
						<?php }?>
						
						<p><?php if(isset($settings['bottom_content_form'])) { echo $settings['bottom_content_form']; } ?></p>
				</div>
			
		</div>
		</div>
		<div class="modal-footer">		
			<button onclick="submitthis('frm_woo_catalog');" type="button" id="woo_submit_enquiry" class="btn btn-primary"><?php echo __('Send', $WC_Woocommerce_Catalog_Enquiry->text_domain);?></button>
		</div>
	</div>		
		
		
				
		</div>
		<style type="text/css">
		.catalog_enquiry_form{display:none;}
		</style>		
		<?php		
		
	}
	
	
	public function add_form_for_enquiry() {		
		global $WC_Woocommerce_Catalog_Enquiry, $woocommerce, $post, $product;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;
		$is_page_redirect = '';
		if(isset($settings['is_page_redirect'])) {
			$is_page_redirect = $settings['is_page_redirect'];
			$redirect_page_id = $settings['redirect_page_id'];
		}
		$settings_buttons = $WC_Woocommerce_Catalog_Enquiry->option_button;
		if(isset($settings_buttons)) {
			
			$custom_design_for_button = isset($settings_buttons['is_button']) ?	$settings_buttons['is_button'] : '';
			$background_color = isset($settings_buttons['button_background_color']) ? $settings_buttons['button_background_color'] : '#ccc';
			$button_text = isset($settings_buttons['button_text']) ? $settings_buttons['button_text'] : __('Send an enquiry',$WC_Woocommerce_Catalog_Enquiry->text_domain);
			$button_text_color = isset($settings_buttons['button_text_color']) ? $settings_buttons['button_text_color'] : '#fff';
			$button_text_color_hover = isset($settings_buttons['button_text_color_hover']) ? $settings_buttons['button_text_color_hover'] : '#ccc';
			$button_background_color_hover = isset($settings_buttons['button_background_color_hover']) ? $settings_buttons['button_background_color_hover'] : '#eee';
			$button_width = isset($settings_buttons['button_width']) ? $settings_buttons['button_width'].'px' : '200px';
			$button_height = isset($settings_buttons['button_height']) ? $settings_buttons['button_height'].'px' : '50px';
			$button_padding = isset($settings_buttons['button_padding']) ? $settings_buttons['button_padding'].'px' : '10px';
			$button_border_size = isset($settings_buttons['button_border_size']) ? $settings_buttons['button_border_size'].'px' : '1px';
			$button_fornt_size = isset($settings_buttons['button_fornt_size']) ? $settings_buttons['button_fornt_size'].'px' : '18px';
			$button_border_redius = isset($settings_buttons['button_border_redius']) ? $settings_buttons['button_border_redius'].'px' : '5px';
			$button_border_color = isset($settings_buttons['button_border_color']) ? $settings_buttons['button_border_color'] : '#999';
			$button_margin_top = isset($settings_buttons['button_margin_top']) ? $settings_buttons['button_margin_top'].'px' : '0px';
			$button_margin_bottom = isset($settings_buttons['button_margin_bottom']) ? $settings_buttons['button_margin_bottom'].'px' : '0px';
			if($button_text == '') {
				$button_text = __('Send an enquiry',$WC_Woocommerce_Catalog_Enquiry->text_domain);
			}
			?>
			<style type="text/css" >
			.woo_catalog_enquiry_custom_button_enquiry {
				background: <?php echo $background_color; ?>;
				color: <?php echo $button_text_color; ?>;
				padding: <?php echo $button_padding;  ?>;
				width: <?php echo $button_width; ?>;
				height: <?php echo $button_height; ?>;
				line-height: <?php echo $button_fornt_size; ?>;
				border-radius: <?php echo $button_border_redius; ?>;
				border: <?php echo $button_border_size.' solid '.$button_border_color; ?>;
				font-size: <?php echo $button_fornt_size; ?>;
				margin-top : <?php echo $button_margin_top; ?>;
				margin-bottom : <?php echo $button_margin_bottom; ?>;
			
			}
			.woo_catalog_enquiry_custom_button_enquiry:hover {
				background: <?php echo $button_text_color_hover;   ?>;
				color: <?php echo $button_background_color_hover;   ?>;
			}		
		</style>
			
			<?php
		}
		$arr_field = array();
		$arr_field[] = "name";
		$arr_field[] = "email";
		if(isset($settings['is_subject']) && $settings['is_subject'] == "Enable") {
		$arr_field[] = "subject";	
		}
		if(isset($settings['is_phone']) && $settings['is_phone'] == "Enable") {
		$arr_field[] = "phone";	
		}
		if(isset($settings['is_address']) && $settings['is_address'] == "Enable") {
		$arr_field[] = "address";	
		}
		if(isset($settings['is_comment']) && $settings['is_comment'] == "Enable") {
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
		
		<?php if(isset($settings['custom_css_product_page']) && $settings['custom_css_product_page']!="") {?>
			<style type="text/css">
				<?php echo $settings['custom_css_product_page'];?>
				
			</style>
			
		<?php }?>
		<?php if(isset($custom_design_for_button) && $custom_design_for_button == "Enable" ) {?>
			<br/>
			<button class="woo_catalog_enquiry_custom_button_enquiry"  onclick="do_toggle();" data-toggle="modal" href="#responsive"><?php echo $button_text;?></button>
			<?php
		}
		else {?>
		<button class="demo btn btn-primary btn-large" style="margin-top:15px;" onclick="do_toggle();" data-toggle="modal" href="#responsive"><?php echo __('Send an enquiry',$WC_Woocommerce_Catalog_Enquiry->text_domain)?></button>
		<?php }?>
		<?php 
		$theme_name1 = 'The7.2';
		$theme_name = get_current_theme();
		if( $theme_name  == $theme_name1 ){
			?>
			<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
			<link href="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/css/bootstrap.css" rel="stylesheet" />
			<link href="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/css/prettify.css" rel="stylesheet" />
			<link href="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/css/bootstrap-responsive.css" rel="stylesheet" />
			<link href="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/css/bootstrap-modal.css" rel="stylesheet" />
			<?php
			add_action('wp_footer', array($this,'load_footer_script_at_last'),500);
			
		}
		else {
		?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		<link href="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/css/bootstrap.css" rel="stylesheet" />
		<link href="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/css/prettify.css" rel="stylesheet" />
		<link href="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/css/bootstrap-responsive.css" rel="stylesheet" />
    <link href="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/css/bootstrap-modal.css" rel="stylesheet" /> 
    <?php if( isset($settings['load_js_lib']) && $settings['load_js_lib'] == "Enable") {?>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <?php }?>
    <script type="text/javascript" src="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/js/prettify.js"></script>
    <script src="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/js/bootstrap.js"></script>
    <script src="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/js/bootstrap-modalmanager.js"></script>
    <script src="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/frontend/js/bootstrap-modal.js"></script>
    <?php }?>
    <script type="text/javascript" >
    	function validateEmail($email) {
				var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
				return emailReg.test( $email );
			}
			function submitthis(str) {
				var name = document.getElementById('woo_user_name').value;				
				var email = document.getElementById('woo_user_email').value;
				var enquiry_product_type = document.getElementById('enquiry_product_type').value;
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
				var product_id = document.getElementById('product_id_for_enquiry').value;	
				<?php if( isset($settings['is_captcha']) && $settings['is_captcha']=="Enable") { ?>
				var captcha = document.getElementById('woo_catalog_captcha');	
				<?php }?>
				
				if(name == '' || name == ' ') {
					document.getElementById('msg_for_enquiry_error').innerHTML='<?php echo __('Name is required field',$WC_Woocommerce_Catalog_Enquiry->text_domain); ?>';					
					document.getElementById('woo_user_name').focus();
					return false;					
				}
				
				if(email == '' || email == ' ') {
					document.getElementById('msg_for_enquiry_error').innerHTML='<?php echo __('Email is required field', $WC_Woocommerce_Catalog_Enquiry->text_domain);?>';					
					document.getElementById('woo_user_email').focus();
					return false;					
				}
				if( !validateEmail(email)) {
					document.getElementById('msg_for_enquiry_error').innerHTML='<?php echo __('Please Enter Valid Email Id', $WC_Woocommerce_Catalog_Enquiry->text_domain);?>';
					document.getElementById('woo_user_email').focus();
					return false;
				}
				
				<?php if( isset($settings['is_captcha']) && $settings['is_captcha']=="Enable") { ?>
				
				if(captcha.value == '' || captcha.value == ' ' ) {					
					document.getElementById('msg_for_enquiry_error').innerHTML='<?php echo __('Please enter the security code',$WC_Woocommerce_Catalog_Enquiry->text_domain);?>';					
					document.getElementById('woo_catalog_captcha').focus();
					return false;					
				}
				if(captcha.value != '<?php echo $captcha; ?>' ) {
					document.getElementById('msg_for_enquiry_error').innerHTML='<?php echo __('Please enter the valid seurity code',$WC_Woocommerce_Catalog_Enquiry->text_domain);?>';					
					document.getElementById('woo_catalog_captcha').focus();
					return false;					
				}
				<?php }?>
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
					'woo_customer_product_url': product_url,
					'woo_customer_product_id' : product_id,
					'enquiry_product_type' : enquiry_product_type										 
				};
				jQuery.post(ajax_url, data, function(response) {						
					if(response.trim()==1) {	
						jQuery("#loader_after_sumitting_the_form").hide();					 	 
						jQuery('#msg_for_enquiry_sucesss').html('<?php echo __('Enquiry sent successfully',$WC_Woocommerce_Catalog_Enquiry->text_domain);?>');
						<?php
						if($is_page_redirect != '') {
						?>
						window.location.href='<?php echo get_permalink($redirect_page_id); ?>';
						<?php }?>
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
						 jQuery('#msg_for_enquiry_error').html('<?php echo __('Error in system please try later',$WC_Woocommerce_Catalog_Enquiry->text_domain);?>');									 
					 }					
				});						
			}
			function do_toggle() {
				jQuery(document).ready(function($){
				  $("#responsive").removeClass('hide');						
				});				
			}
		</script>
		
		<input type="hidden" name="product_name_for_enquiry" id="product_name_for_enquiry" value="<?php echo get_post_field('post_title',$post->ID); ?>" />
		<input type="hidden" name="product_url_for_enquiry" id="product_url_for_enquiry" value="<?php echo get_permalink($post->ID); ?>" />
		<input type="hidden" name="product_id_for_enquiry" id="product_id_for_enquiry" value="<?php echo $post->ID; ?>" />
		<input type="hidden" name="enquiry_product_type" id="enquiry_product_type" value="<?php if( $product->is_type( 'variable' ) ) { echo 'variable'; } ?>" />
		<div id="responsive"  class="modal hide fade" tabindex="-1" data-width="760">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<?php if( isset($settings['is_override_form_heading'])) { ?>
				<?php if( isset($settings['custom_static_heading'])) { ?>
					<h2 style="font-size:20px;"><?php echo  $settings['custom_static_heading']; ?></h2>
				<?php }?>
			<?php } else{?>
			<h2 style="font-size:20px;"><?php echo __('Enquiry about ',$WC_Woocommerce_Catalog_Enquiry->text_domain)?> <?php echo $product_name; ?></h2>
			<?php }?>
		</div>
			<div class="modal-body">
				<div class="row-fluid">
					<div class="span12">
						<p><?php if( isset($settings['top_content_form'])) { echo $settings['top_content_form']; }?></p>
						<p id="msg_for_enquiry_error" style="color:#f00; text-align:center;"></p>
						<p id="msg_for_enquiry_sucesss" style="color:#0f0; text-align:center;"></p>
						<p id="loader_after_sumitting_the_form" style="text-align:center;"><img src="<?php echo $WC_Woocommerce_Catalog_Enquiry->plugin_url;?>assets/images/loader.gif" ></p>
						
						<p><?php if( isset($settings['name_label']) && $settings['name_label'] != '' && $settings['name_label'] !=' ') { echo $settings['name_label']; } else { echo __('Enter your name : ',$WC_Woocommerce_Catalog_Enquiry->text_domain); } ?></p>	
						<p><input name="woo_user_name" id="woo_user_name"  type="text" value="<?php echo $current_user->display_name; ?>" class="span12" /></p>
						
						<p><?php if( isset($settings['email_label']) && $settings['email_label'] != '' && $settings['email_label'] !=' ') { echo $settings['email_label']; } else { echo __('Enter your Email Id : ',$WC_Woocommerce_Catalog_Enquiry->text_domain); }?></p>	
						<p><input name="woo_user_email" id="woo_user_email"  type="email" value="<?php echo $current_user->user_email; ?>" class="span12" /></p>
						
						<?php if( isset($settings['is_subject']) && $settings['is_subject']=="Enable") { ?>
						<p><?php if( isset($settings['subject_label']) && $settings['subject_label'] != '' && $settings['subject_label'] !=' ') { echo $settings['subject_label']; } else { echo __('Enter enquiry subject : ',$WC_Woocommerce_Catalog_Enquiry->text_domain); } ?></p>	
						<p><input name="woo_user_subject" id="woo_user_subject"  type="text" value="<?php echo __('Enquiry about',$WC_Woocommerce_Catalog_Enquiry->text_domain); ?> <?php echo $product_name; ?>" class="span12" /></p>
						<?php } ?>
						<?php if( isset($settings['is_phone']) && $settings['is_phone']=="Enable") { ?>
						<p><?php if( isset($settings['phone_label']) && $settings['phone_label'] != '' && $settings['phone_label'] !=' ') { echo $settings['phone_label']; } else { echo __('Enter your phone no : ',$WC_Woocommerce_Catalog_Enquiry->text_domain); } ?></p>	
						<p><input name="woo_user_phone" id="woo_user_phone"  type="text" value="" class="span12" /></p>
						<?php } ?>
						<?php if( isset($settings['is_address']) && $settings['is_address']=="Enable") { ?>
						<p><?php if( isset($settings['address_label']) && $settings['address_label'] != '' && $settings['address_label'] !=' ') { echo $settings['address_label']; } else { echo __('Enter your address : ',$WC_Woocommerce_Catalog_Enquiry->text_domain); } ?></p>	
						<p><input name="woo_user_address" id="woo_user_address"  type="text" value="" class="span12" /></p>
						<?php } ?>
						<?php if( isset($settings['is_comment']) && $settings['is_comment']=="Enable") { ?>
						<p><?php if( isset($settings['comment_label']) && $settings['comment_label'] != '' && $settings['comment_label'] !=' ') { echo $settings['comment_label']; } else { echo __('Enter your Message : ',$WC_Woocommerce_Catalog_Enquiry->text_domain); } ?></p>	
						<p><textarea name="woo_user_comment" id="woo_user_comment"  rows="5" class="span12"></textarea></p>
						<?php } ?>
						
						<?php do_action( 'woocommerce_catalog_enquiry_form_product_page' ); ?> 
						<?php if( isset($settings['is_captcha']) && $settings['is_captcha']=="Enable") { ?>
						<p><?php if( isset($settings['captcha_label']) && $settings['captcha_label'] != '' && $settings['captcha_label'] !=' ') { echo $settings['captcha_label']; } else { echo  __('Security Code',$WC_Woocommerce_Catalog_Enquiry->text_domain); } ?> <span class="noselect" style="background:#000; color:#fff; border:1px solid #333; padding:5px; letter-spacing: 5px; font-size:18px;" ><i><?php echo $_SESSION['mycaptcha'];	?></i></span></p>
						<p><?php if( isset($settings['captcha_input_label'])&& $settings['captcha_input_label'] != '' && $settings['captcha_input_label'] !=' ' ) { echo $settings['captcha_input_label']; } else { echo __('Enter the security code shown above',$WC_Woocommerce_Catalog_Enquiry->text_domain); } ?> </p>
						<p><input type="text" id="woo_catalog_captcha" name="woo_captcha" class="span12" /></p>
						<?php }?>
						
						<p><?php if(isset($settings['bottom_content_form'])) { echo $settings['bottom_content_form']; } ?></p>
				</div>
			
		</div>
		</div>
		<div class="modal-footer">
			
			<button type="button" data-dismiss="modal" class="btn"><?php echo __('Close',$WC_Woocommerce_Catalog_Enquiry->text_domain);?></button>
			<button onclick="submitthis('frm_woo_catalog');" type="button" id="woo_submit_enquiry" class="btn btn-primary"><?php echo __('Send', $WC_Woocommerce_Catalog_Enquiry->text_domain);?></button>
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
			line-height: <?php if($settings['button_fornt_size']!=""){ echo $settings['button_fornt_size']."px";} else { echo "14px"; } ?>;
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
	
	
	
	public function price_for_selected_product() {
		global $WC_Woocommerce_Catalog_Enquiry, $post, $product;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;
		$exclusion = $WC_Woocommerce_Catalog_Enquiry->options_exclusion;
		$product_for = '';
		
		if(isset( $exclusion['is_exclusion'] ) && $exclusion['is_exclusion'] == 'Enable' ) {
			if( isset( $exclusion['myproduct_list'] ) ) {
				if(is_array($exclusion['myproduct_list']) && isset($post->ID)) {
					if(in_array($post->ID,$exclusion['myproduct_list'])) {
						
						$product_for = $post->ID;							
					}
					else {
						 $product_for = '';
						
					}
				}						
			} 					
		
		
			$category_for = '';
			if(isset( $exclusion['is_exclusion'] ) && $exclusion['is_exclusion'] == 'Enable' ) {
				if( isset( $exclusion['mycategory_list'] ) ) {
					if(is_array($exclusion['mycategory_list'])) {					
						if(isset($product)) {
							$term_list = wp_get_post_terms($post->ID,'product_cat',array('fields'=>'ids'));
							
							if(count(array_intersect($term_list, $exclusion['mycategory_list'])) > 0) {
								$category_for = $post->ID;
								
							}
							else {
								$category_for = '';
							}
						}
						else {  $category_for = ''; }										
					} 
					else { $category_for = ''; }				
				} 
				else { $category_for = ''; }			
			} 
			else { $category_for = ''; }
		
		
		
			if($product_for == $post->ID || $category_for == $post->ID) {
				add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );			
			} else {
				if(isset($settings['is_remove_price']) && $settings['is_remove_price'] == "Enable") {				
				  remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
				}
				
			}
		}
	}
	
	
	public function add_to_cart_button_for_selected_product() {
		global $WC_Woocommerce_Catalog_Enquiry, $post, $product;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;
		$exclusion = $WC_Woocommerce_Catalog_Enquiry->options_exclusion;
		$product_for = '';
		
		if(isset( $exclusion['is_exclusion'] ) && $exclusion['is_exclusion'] == 'Enable' ) {
			if( isset( $exclusion['myproduct_list'] ) ) {
				if(is_array($exclusion['myproduct_list']) && isset($post->ID)) {
					if(in_array($post->ID,$exclusion['myproduct_list'])) {
						
						$product_for = $post->ID;							
					}
					else {
						 $product_for = '';
						
					}
				}						
			} 					
		}
		
		$category_for = '';
		if(isset( $exclusion['is_exclusion'] ) && $exclusion['is_exclusion'] == 'Enable' ) {
			if( isset( $exclusion['mycategory_list'] ) ) {
				if(is_array($exclusion['mycategory_list'])) {					
					if(isset($product)) {
						$term_list = wp_get_post_terms($post->ID,'product_cat',array('fields'=>'ids'));
						
						if(count(array_intersect($term_list, $exclusion['mycategory_list'])) > 0) {
							$category_for = $post->ID;
						}
						else {
							$category_for = '';
						}
					}
					else {  $category_for = ''; }										
				} 
				else { $category_for = ''; }				
			} 
			else { $category_for = ''; }			
		} 
		else { $category_for = ''; }		
		
		if($product_for == $post->ID || $category_for == $post->ID) {
			
			if(isset($settings['is_custom_button']) && $settings['is_custom_button'] == "Enable") {
				if($settings['button_type'] == 0 || $settings['button_type'] == '' || $settings['button_type'] == 1) {
					add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );	
					remove_filter('woocommerce_loop_add_to_cart_link', array($this,'add_read_more_button'),10);					
				}
				else if($settings['button_type'] == 2) {
					remove_filter('woocommerce_loop_add_to_cart_link', array($this,'add_external_link_button'),10);					
				}
				else if($settings['button_type'] == 3) {
					remove_filter('woocommerce_loop_add_to_cart_link', array($this,'add_external_link_button_independent'),10);					
				}
				else if($settings['button_type'] == 4) {
					remove_filter('woocommerce_loop_add_to_cart_link', array($this,'add_custom_button_without_link'),10);					
				}				
			}
			else {
				add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );				
			}
		}else {
			
			if(isset($settings['is_custom_button']) && $settings['is_custom_button'] == "Enable") {
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
			else {
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
				
			}
		}
	}
	
	
	public function add_read_more_button() {
		global $WC_Woocommerce_Catalog_Enquiry, $post;
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
		global $WC_Woocommerce_Catalog_Enquiry, $post;
		$settings = $WC_Woocommerce_Catalog_Enquiry->options;		
		if(isset($settings['is_custom_button']) && $settings['is_custom_button']=="Enable") {
			
		}
		else {
			
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );	
		}		
			
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		add_action( 'woocommerce_single_product_summary', array($this,'add_variation_product'),29);
		
	}


	public function add_variation_product() {
		
		global $WC_Woocommerce_Catalog_Enquiry, $post, $product;
		// Enqueue variation scripts
        	wp_enqueue_script( 'wc-add-to-cart-variation' );
		if( $product->is_type( 'variable' ) ){
			$variable_product = new WC_Product_Variable($product);
			$available_variations = $variable_product->get_available_variations();		
			//attributes
			include_once ($WC_Woocommerce_Catalog_Enquiry->plugin_path.'templates/variable.php');
			
			/**
			 * Not Needed due to variation scripts is already avail for woocommerce need to just append
			 * here 
			 * please see line #1196
			 * /
			//add_action('wp_footer', array($this, 'add_variation_js'),900);		
			
		}	
	}
	
	public function add_variation_js() {?>
		
		<script type="text/javascript">
		jQuery(document).ready(function($){
			$(".variations select").change(function(e){
			var variation_name = $(this).parent().parent().find(".label label").html();
			var select_value = $(this).val();
			var product_id = $(this).attr("data-product-id");
			var variation_real_name = $(this).attr("name");			
			var ajax_url = '<?php echo admin_url( 'admin-ajax.php' ) ?>';
			var variation_array = $('form.variations_form').attr('data-product_variations');
			
				var data = {
										 'action': 'add_variation_for_enquiry_mail',
										 'variation_name': variation_name,
										 'variation_real_name': variation_real_name,
										 'variation_value': select_value,
										 'product_id': product_id,
										 'variation_array': variation_array
				};
				jQuery.post(ajax_url, data, function(response) { 
					console.log(response);
																			
				});							
			});	
		});
		
		
		</script>	
	<?php	
		
	}
	
	public function remove_price_from_product_list_loop(){				
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );		
	}
	
	public function remove_price_from_product_list_single() {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );	
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
