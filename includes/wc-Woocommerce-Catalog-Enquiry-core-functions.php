<?php
if(!function_exists('get_Woocommerce_Catalog_Enquiry_settings')) {
  function get_Woocommerce_Catalog_Enquiry_settings($name = '', $tab = '') {
    if(empty($tab) && empty($name)) return '';
    if(empty($tab)) return get_option($name);
    if(empty($name)) return get_option("dc_{$tab}_settings_name");
    $settings = get_option("dc_{$tab}_settings_name");
    if(!isset($settings[$name])) return '';
    return $settings[$name];
  }
}
?>
