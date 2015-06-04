<?php
class WC_Woocommerce_Catalog_Enquiry_Settings_Gneral {
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;
  
  private $tab;
  
  public $all_users;

  /**
   * Start up
   */
  public function __construct($tab) {
    $this->tab = $tab;
    $users = get_users();
    $this->all_users = array();
    foreach($users as $user) {					
			$this->all_users[$user->data->ID] = $user->data->display_name;	 			
		}
    $this->options = get_option( "dc_{$this->tab}_settings_name" );
    $this->settings_page_init();
    
    
		
	
		
  }
  
  /**
   * Register and add settings
   */
  public function settings_page_init() {
    global $WC_Woocommerce_Catalog_Enquiry;
     
    $settings_tab_options = array("tab" => "{$this->tab}",
                                  "ref" => &$this,
                                  "sections" => array(
                                                      "default_settings_section" => array("title" =>  __('Woocommerce Catalog Enquiry Settings', $WC_Woocommerce_Catalog_Enquiry->text_domain), // Section one
                                                                                         "fields" => array("is_enable" => array('title' => __('Catalog Enable?', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'checkbox', 'id' => 'is_enable', 'label_for' => 'is_enable', 'name' => 'is_enable', 'desc' => __('Just Checked this checkbox for woocommerce catalog mode on', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'hints' => __('Check this for active the catalog functionality.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'value' => 'Enable'), // is catalog enable
                                                                                         	 								 "load_js_lib" => array('title' => __('Load Plugin JS Library(1.8.2)?', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'checkbox', 'id' => 'load_js_lib', 'label_for' => 'load_js_lib', 'name' => 'load_js_lib', 'desc' => __('Just Checked this checkbox for load Plugin Js Lib 1.8.2 if your theme have own Js lib then ingnore it', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'hints' => __('Check this for load plugin js lib.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'value' => 'Enable'), // is js lib enable	
                                                                                                           "for_user_type" => array('title' => __('Applicable For', $WC_Woocommerce_Catalog_Enquiry->text_domain) , 'type' => 'select', 'id' => 'for_user_type', 'label_for' => 'for_user_type', 'name' => 'for_user_type', 'options' => array('0' =>'Please Select', '1' => 'Only for logout user', '2' => 'Only for logged in user', '3' => 'Either logged in or logged out'), 'hints' => __('Method applicable for only secleted user group default all.', $WC_Woocommerce_Catalog_Enquiry->text_domain),   'desc' => __('Select the user type where this catalog is applicable.', $WC_Woocommerce_Catalog_Enquiry->text_domain)), // user_type
                                                                                                           "top_content_form" => array('title' => __('Enquiry Top content', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'wpeditor', 'id' => 'top_content_form', 'label_for' => 'top_content_form', 'desc' => __('Put your content if you want to top of enquiry form', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'name' => 'top_content_form'), //Top Content
                                                                                                           "bottom_content_form" => array('title' => __('Enquiry Bottom content', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'wpeditor', 'id' => 'bottom_content_form', 'label_for' => 'bottom_content_form', 'desc' => __('Put your content if you want to bottom of enquiry form', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'name' => 'bottom_content_form'), //Bottom Content
                                                                                                           "is_enable_enquiry" => array('title' => __('Product Enquiry Enable?', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'checkbox', 'id' => 'is_enable_enquiry', 'label_for' => 'is_enable_enquiry', 'name' => 'is_enable_enquiry', 'desc' => __('Just Checked this checkbox for product page enquiry form enable', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'hints' => __('Check this for active the form functionality.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'value' => 'Enable'), // is catalog enable
                                                                                                           "is_remove_price" => array('title' => __('Remove Price?', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'checkbox', 'id' => 'is_remove_price', 'label_for' => 'is_remove_price', 'name' => 'is_remove_price', 'desc' => __('Just Checked this checkbox for remove the price from catalog', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'hints' => __('Check this for remove the price from product list.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'value' => 'Enable'), // is catalog enable
                                                                                                           "custom_css_product_page" => array('title' => __('Custom CSS', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'textarea', 'label_for' => 'custom_css_product_page', 'name' => 'custom_css_product_page', 'desc' => __('Put your custom css in this box for product page there is no need to put the style Tag', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'rows' => 10, 'cols' => 120),
                                                                                                           "is_custom_button" => array('title' => __('Want a custom Button?', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'checkbox', 'id' => 'is_custom_button', 'label_for' => 'is_custom_button', 'name' => 'is_custom_button', 'desc' => __('Do you want a custom Button at the Place of add to cart button then checked here', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'hints' => __('Check this if you want custom button at the place of add to cart.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'value' => 'Enable'), // is button enable
                                                                                                           "is_hide_cart_checkout" => array('title' => __('Hide Cart Chackout Page?', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'checkbox', 'id' => 'is_hide_cart_checkout', 'label_for' => 'is_hide_cart_checkout', 'name' => 'is_hide_cart_checkout', 'desc' => __('Do you want to redirect to home if any one click on the cart or checkout page link', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'hints' => __('Do you want to redirect to home page if any one click on the cart or checkout page link.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'value' => 'Enable'), // is button enable
                                                                                                                                                                                                                      
                                                                                                           "button_type" => array('title' => __('Choose your button type', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'select', 'id' => 'button_type', 'label_for' => 'button_type', 'name' => 'button_type',  'options' => array('0' => 'Please Select', '1' => 'Read More', '2' => 'Custom Link For All Products', '3' => 'Individual link in all products', '4' => 'No Link Just #'), 'hints' => __('Choose your preferred button type.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'desc' => __('By default Read More Button.', $WC_Woocommerce_Catalog_Enquiry->text_domain)), // Button Type
                                                                                                           "button_link" => array('title' => __('Button Link', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'text', 'id' => 'button_link', 'name' => 'button_link', 'desc' => __('Applicable only when you choose custom link for all products in button type', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'hints' => __('Button link applicable only if you choose button type Custom Link For All Products ', $WC_Woocommerce_Catalog_Enquiry->text_domain))
                                                                                                           
                                                                                                           
                                                                                                          
                                                                                                           )
                                                                                         ), 
                                                      "custom_settings_section" => array("title" => "Custom Button Layout Settings", // Another section
                                                                                         "fields" => array("button_text" => array('title' => __('Custom button label', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'text', 'id' => 'button_text', 'name' => 'button_text', 'hints' => __('Give your custom button Text', $WC_Woocommerce_Catalog_Enquiry->text_domain)),
																																												 "button_text_color" => array('title' => __('Choose Button Text Color', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'colorpicker', 'id' => 'button_text_color', 'label_for' => 'button_text_color', 'name' => 'button_text_color', 'default' => '#000000', 'hints' => __('Choose your button text color here.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'desc' => __('This is button text color will be appear in the custom button .', $WC_Woocommerce_Catalog_Enquiry->text_domain)),
																																												 "button_background_color" => array('title' => __('Choose Button Background Color', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'colorpicker', 'id' => 'button_background_color', 'label_for' => 'button_background_color', 'name' => 'button_background_color', 'default' => '#999999', 'hints' => __('Choose your button background color here.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'desc' => __('This is button background color will be appear in the custom button .', $WC_Woocommerce_Catalog_Enquiry->text_domain)),
																																												 "button_text_color_hover" => array('title' => __('Choose Button Text Color Hover', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'colorpicker', 'id' => 'button_text_color_hover', 'label_for' => 'button_text_color_hover', 'name' => 'button_text_color_hover', 'default' => '#ffffff', 'hints' => __('Choose your button text color on hover here.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'desc' => __('This is button text color on hover will be appear in the custom button .', $WC_Woocommerce_Catalog_Enquiry->text_domain)),
																																												 "button_background_color_hover" => array('title' => __('Choose Button background Color Hover', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'colorpicker', 'id' => 'button_background_color_hover', 'label_for' => 'button_background_color_hover', 'name' => 'button_background_color_hover', 'default' => '#000000', 'hints' => __('Choose your button background color on hover here.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'desc' => __('This is button background color on hover will be appear in the custom button .', $WC_Woocommerce_Catalog_Enquiry->text_domain)),
																																												 "button_width" => array('title' => __('Custom button Width', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'text', 'id' => 'button_width', 'name' => 'button_width', 'hints' => __('Give your custom button Width in px', $WC_Woocommerce_Catalog_Enquiry->text_domain)),
																																												 "button_height" => array('title' => __('Custom button Height', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'text', 'id' => 'button_height', 'name' => 'button_height', 'hints' => __('Give your custom button Height', $WC_Woocommerce_Catalog_Enquiry->text_domain)),
																																												 "button_padding" => array('title' => __('Custom button Padding', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'text', 'id' => 'button_padding', 'name' => 'button_padding', 'hints' => __('Give your custom button Padding', $WC_Woocommerce_Catalog_Enquiry->text_domain)),
																																												 "button_border_size" => array('title' => __('Custom button Border', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'text', 'id' => 'button_border_size', 'name' => 'button_border_size', 'hints' => __('Give your custom button border size', $WC_Woocommerce_Catalog_Enquiry->text_domain)),
																																												 "button_fornt_size" => array('title' => __('Custom button Font size', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'text', 'id' => 'button_fornt_size', 'name' => 'button_fornt_size', 'hints' => __('Give your custom button Font Size', $WC_Woocommerce_Catalog_Enquiry->text_domain)),
																																												 "button_border_redius" => array('title' => __('Custom button border redius', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'text', 'id' => 'button_border_redius', 'name' => 'button_border_redius', 'hints' => __('Give your custom button border redius', $WC_Woocommerce_Catalog_Enquiry->text_domain)),
																																												 "button_border_color" => array('title' => __('Choose Button Border Color', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'colorpicker', 'id' => 'button_border_color', 'label_for' => 'button_border_color', 'name' => 'button_border_color', 'default' => '#333333', 'hints' => __('Choose your button border color.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'desc' => __('This is button border color which will be appear in the custom button .', $WC_Woocommerce_Catalog_Enquiry->text_domain)),
																																												 "button_margin_top" => array('title' => __('Custom button margin top', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'text', 'id' => 'button_margin_top', 'name' => 'button_margin_top', 'hints' => __('Give your custom button top margin', $WC_Woocommerce_Catalog_Enquiry->text_domain)),
																																												 "button_margin_bottom" => array('title' => __('Custom button margin bottom', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'text', 'id' => 'button_margin_bottom', 'name' => 'button_margin_bottom', 'hints' => __('Give your custom button bottom margin', $WC_Woocommerce_Catalog_Enquiry->text_domain))
                                                                                                          )
                                                                                         ),
                                                      "enquiry_settings_section_form" => array("title" => "Enquiry Form Settings", // Another section
                                                      																					"fields" => array("is_name" => array('title' => __('Name Field Enable ?', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'checkbox', 'checked'=>'checked',  'id' => 'is_name', 'label_for' => 'is_name', 'name' => 'is_name', 'desc' => __('Name must be enable in the form.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'hints' => __('Name field must be enabled in the enquiry form.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'value' => 'Enable'), // is button enable
                                                      																														"is_email" => array('title' => __('Email Field Enable ?', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'checkbox', 'checked'=>'checked', 'id' => 'is_email', 'label_for' => 'is_email', 'name' => 'is_email', 'desc' => __('Email must be enable in the form.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'hints' => __('Email field must be enabled in the enquiry form.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'value' => 'Enable'),
                                                      																														"is_subject" => array('title' => __('Subject Field Enable ?', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'checkbox', 'id' => 'is_subject', 'label_for' => 'is_subject', 'name' => 'is_subject', 'desc' => __('Do you want Subject field in for enquiry form.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'hints' => __('Check this if you want subject field in the enquiry form.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'value' => 'Enable'),
                                                      																														"is_phone" => array('title' => __('Phone Field Enable ?', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'checkbox', 'id' => 'is_phone', 'label_for' => 'is_phone', 'name' => 'is_phone', 'desc' => __('Do you want Phone field in for enquiry form.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'hints' => __('Check this if you want to Phone field in the enquiry form.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'value' => 'Enable'),
                                                      																														"is_address" => array('title' => __('Address Field Enable ?', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'checkbox', 'id' => 'is_address', 'label_for' => 'is_address', 'name' => 'is_address', 'desc' => __('Do you want Address field in for enquiry form.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'hints' => __('Check this if you want address field in the enquiry form.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'value' => 'Enable'),
                                                      																														"is_comment" => array('title' => __('Comment Field Enable ?', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'type' => 'checkbox', 'id' => 'is_comment', 'label_for' => 'is_comment', 'name' => 'is_comment', 'desc' => __('Do you want Comment field in for enquiry form.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'hints' => __('Check this if you want comment field in the enquiry form.', $WC_Woocommerce_Catalog_Enquiry->text_domain), 'value' => 'Enable')
                                                      																														
                                                      																						
                                                      																					))
                                                      )
                                  );
    
    $WC_Woocommerce_Catalog_Enquiry->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function dc_wc_Woocommerce_Catalog_Enquiry_general_settings_sanitize( $input ) {
    global $WC_Woocommerce_Catalog_Enquiry;
    $new_input = array();
   
    
    $hasError = false;
    
    if( isset( $input['is_enable'] ) )
      $new_input['is_enable'] = sanitize_text_field( $input['is_enable'] );
    
    if( isset( $input['load_js_lib'] ) )
      $new_input['load_js_lib'] = sanitize_text_field( $input['load_js_lib'] );
    
    if( isset( $input['for_user_type'] ) ) {
      $new_input['for_user_type'] = sanitize_text_field( $input['for_user_type'] );
    } 
    
    if( isset( $input['top_content_form'] ) )
      $new_input['top_content_form'] = sanitize_text_field( $input['top_content_form'] );

    if( isset( $input['bottom_content_form'] ) )
      $new_input['bottom_content_form'] = sanitize_text_field( $input['bottom_content_form'] );
    
    if( isset( $input['is_enable_enquiry'] ) )
      $new_input['is_enable_enquiry'] = sanitize_text_field( $input['is_enable_enquiry'] );
    
    if( isset( $input['is_remove_price'] ) )
      $new_input['is_remove_price'] = sanitize_text_field( $input['is_remove_price'] );
    
    if( isset( $input['custom_css_product_page'] ) && !empty($input['custom_css_product_page']) ) {
      $new_input['custom_css_product_page'] = sanitize_text_field( $input['custom_css_product_page'] );
    } 
    
    if( isset( $input['is_custom_button'] ) )
      $new_input['is_custom_button'] = sanitize_text_field( $input['is_custom_button'] );
    
    if( isset( $input['button_type'] ) )
      $new_input['button_type'] = sanitize_text_field( $input['button_type'] );
    
    if( isset( $input['button_link'] ) )
      $new_input['button_link'] = sanitize_text_field( $input['button_link'] );
    
    if( isset( $input['button_text'] ) )
      $new_input['button_text'] = sanitize_text_field( $input['button_text'] );
    
    if( isset( $input['button_text_color'] ) )
      $new_input['button_text_color'] = sanitize_text_field( $input['button_text_color'] );
    
    if( isset( $input['button_background_color'] ) )
      $new_input['button_background_color'] = ( $input['button_background_color'] );
    
    if( isset( $input['button_text_color_hover'] ) )
      $new_input['button_text_color_hover'] = sanitize_text_field( $input['button_text_color_hover'] );
    
    if( isset( $input['button_background_color_hover'] ) )
      $new_input['button_background_color_hover'] = sanitize_text_field( $input['button_background_color_hover'] );
      
    
    
    if( isset( $input['button_width'] ) && absint( $input['button_width'] ) != 0 ) {
      $new_input['button_width'] = absint( $input['button_width'] );
    } else {
    	      
    }
    
    if( isset( $input['button_height'] ) && absint($input['button_height']) != 0 ) {
      $new_input['button_height'] = absint( $input['button_height'] );
    } else {
    	   	
    }
    
    if( isset( $input['button_padding'] ) && absint($input['button_padding']) != 0 ) {
      $new_input['button_padding'] = absint( $input['button_padding'] );
    } else {
    	 
         	
    }
    
    if( isset( $input['button_border_size'] ) && absint($input['button_border_size']) != 0 ) {
      $new_input['button_border_size'] = absint( $input['button_border_size'] );
    } else {
    	    	
    }
    
    
    
    
    
    if( isset( $input['button_fornt_size'] ) && absint($input['button_fornt_size']) != 0 ) {
      $new_input['button_fornt_size'] = absint( $input['button_fornt_size'] );
    } else {
    	    	
    }
    
    if( isset( $input['button_border_redius'] ) && absint($input['button_border_redius']) != 0 ) {
      $new_input['button_border_redius'] = absint( $input['button_border_redius'] );
    } else {
    	    	
    }
      
    
    if( isset( $input['button_border_color'] ) )
      $new_input['button_border_color'] = sanitize_text_field( $input['button_border_color'] );
    
    if( isset( $input['button_margin_top'] ) &&  absint($input['button_margin_top']) != 0 ) {
      $new_input['button_margin_top'] = absint( $input['button_margin_top'] );
    } else {
        	
    }
    
    if( isset( $input['button_margin_bottom'] ) && absint($input['button_margin_bottom']) != 0 ) {
      $new_input['button_margin_bottom'] = absint( $input['button_margin_bottom'] );
    }else {
    	    	
    }
    
    if( isset( $input['is_name'] ) )
      $new_input['is_name'] = sanitize_text_field( $input['is_name'] );
    
    if( isset( $input['is_email'] ) )
      $new_input['is_email'] = sanitize_text_field( $input['is_email'] );
    
    if( isset( $input['is_subject'] ) )
      $new_input['is_subject'] = ( $input['is_subject'] );
    
    
    if( isset( $input['is_phone'] ) )
      $new_input['is_phone'] = sanitize_text_field( $input['is_phone'] );
    
    if( isset( $input['is_address'] ) )
      $new_input['is_address'] = ( $input['is_address'] );
    
    if( isset( $input['is_comment'] ) )
      $new_input['is_comment'] = sanitize_text_field( $input['is_comment'] );
    if( isset( $input['is_hide_cart_checkout'] ) )
    	$new_input['is_hide_cart_checkout'] = sanitize_text_field( $input['is_hide_cart_checkout'] );
    
    
    if(!$hasError) {
      add_settings_error(
        "dc_{$this->tab}_settings_name",
        esc_attr( "dc_{$this->tab}_settings_admin_updated" ),
        __('General settings updated', $WC_Woocommerce_Catalog_Enquiry->text_domain),
        'updated'
      );
    }

    return $new_input;
  }

  /** 
   * Print the Section text
   */
  public function default_settings_section_info() {
    global $WC_Woocommerce_Catalog_Enquiry;
    _e('Enter your default settings below', $WC_Woocommerce_Catalog_Enquiry->text_domain);
  }
  
  /** 
   * Print the Section text
   */
  public function custom_settings_section_info() {
    global $WC_Woocommerce_Catalog_Enquiry;
    _e('Configure your button layout settings below', $WC_Woocommerce_Catalog_Enquiry->text_domain);
  }
  
  /** 
   * Print the Section text
   */
  public function enquiry_settings_section_form_info() {
    global $WC_Woocommerce_Catalog_Enquiry;
    _e('Configure your enquiry form settings below', $WC_Woocommerce_Catalog_Enquiry->text_domain);
  }
  
}