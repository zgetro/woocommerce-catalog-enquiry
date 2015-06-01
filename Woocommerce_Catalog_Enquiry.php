<?php
/*
Plugin Name: Woocommerce Catalog Enquiry
Plugin URI: http://dualcube.com
Description: Convert your woocommerce store into a catalog website in a click
Author: dualcube, prabhakarkumarshaw
Version: 1.0.1
Author URI: http://dualcube.com
*/

if ( ! class_exists( 'WC_Catalog_Dependencies' ) )
	require_once 'includes/class-dc-catalog-dependencies.php';
require_once 'includes/wc-Woocommerce-Catalog-Enquiry-core-functions.php';
require_once 'config.php';
if(!defined('ABSPATH')) exit; // Exit if accessed directly
if(!defined('WC_WOOCOMMERCE_CATALOG_ENQUIRY_PLUGIN_TOKEN')) exit;
if(!defined('WC_WOOCOMMERCE_CATALOG_ENQUIRY_TEXT_DOMAIN')) exit;

if(!WC_Catalog_Dependencies::woocommerce_active_check()) {
  add_action( 'admin_notices', 'woocommerce_catalog_inactive_notice' );
}

if(!class_exists('WC_Woocommerce_Catalog_Enquiry')) {
	require_once( 'classes/class-wc-Woocommerce-Catalog-Enquiry.php' );
	global $WC_Woocommerce_Catalog_Enquiry;
	$WC_Woocommerce_Catalog_Enquiry = new WC_Woocommerce_Catalog_Enquiry( __FILE__ );
	$GLOBALS['WC_Woocommerce_Catalog_Enquiry'] = $WC_Woocommerce_Catalog_Enquiry;
}
?>
