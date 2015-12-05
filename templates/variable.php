<?php
/**
 * Variable product add to cart
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $post;
$variation_attributes = $product->get_variation_attributes();
$attributes   = $product->get_attributes();
$attribute_keys = array_keys($attributes);
$default_arrt_value = get_post_meta( $post->ID, '_default_attributes', true);
$i = 0;
$variation_list = $_SESSION['variation_list'];
if(isset($default_arrt_value) && is_array($default_arrt_value) && (!empty($default_arrt_value))) {
	foreach($default_arrt_value as  $key => $value) {			
		$vname = str_replace('pa_','',$key);
		$vname2 = str_replace('attribute_pa_', '', $vname);
		$vname2 = str_replace('attribute_','',$vname2);				
		$arr = array('variation_name' => $vname2, 'variation_value' => $value, 'product_id' => $post->ID, 'variation_real_name' => 'attribute_'.$key);				
		$variation_list[$i] = $arr;
		$i++;
	}	
	$_SESSION['variation_list'] = $variation_list;	
}
?>
<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo $post->ID; ?>" data-product_variations="<?php echo esc_attr( json_encode( $available_variations ) ) ?>">
	<?php if ( ! empty( $available_variations ) ) : ?>
		<table class="variations" cellspacing="0">
			<tbody>
				<?php $loop = 0; foreach ( $variation_attributes as $name => $options ) : $loop++;  ?>
					<tr>
						<td class="label"><label for="<?php echo sanitize_title( $name ); ?>"><?php echo wc_attribute_label( $name ); ?></label></td>
						<td class="value"><select id="<?php echo esc_attr( sanitize_title( $name ) ); ?>" data-product-id = "<?php echo $post->ID; ?>" name="attribute_<?php echo sanitize_title( $name ); ?>" data-attribute_name="attribute_<?php echo sanitize_title( $name ); ?>">
							<option value=""><?php echo __( 'Choose an option', 'woocommerce' ) ?>&hellip;</option>
							<?php
								if ( is_array( $options ) ) {
									if ( isset( $_REQUEST[ 'attribute_' . sanitize_title( $name ) ] ) ) {
										$selected_value = $_REQUEST[ 'attribute_' . sanitize_title( $name ) ];
									} elseif ( isset( $selected_attributes[ sanitize_title( $name ) ] ) ) {
										$selected_value = $selected_attributes[ sanitize_title( $name ) ];
									} else {
										$selected_value = '';
									}
									// Get terms if this is a taxonomy - ordered
									if ( taxonomy_exists( $name ) ) {

										$terms = wc_get_product_terms( $post->ID, $name, array( 'fields' => 'all' ) );

										foreach ( $terms as $term ) {
											if ( ! in_array( $term->slug, $options ) ) {
												continue;
											}
											$is_selected = '';
											if(is_array($default_arrt_value) && (!empty($default_arrt_value))) {
												if($default_arrt_value[$name] == $term->slug ) {
													$is_selected = 'selected="selected"';
												}
											}
											echo '<option value="' . esc_attr( $term->slug ) . '" ' . $is_selected . '>' . apply_filters( 'woocommerce_variation_option_name', $term->name ) . '</option>';
										}
									} else {

										foreach ( $options as $option ) {
											$is_selected = '';
											if(is_array($default_arrt_value) && (!empty($default_arrt_value))) {
												if($default_arrt_value[$name] == esc_attr( sanitize_title( $option ) ) ) {
													$is_selected = 'selected="selected"';
												}
											}
											echo '<option value="' . esc_attr( sanitize_title( $option ) ) . '" ' . $is_selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
										}

									}
								}
							?>
						</select><div class="<?php echo esc_attr( sanitize_title( $name ) ); ?>" style="color:#f00;" ></div> </td>
						<?php echo end($attribute_keys) === $name ? '<a class="reset_variations" href="#">Clear selection</a>' : ''; ?>
					</tr>
		        <?php endforeach;?>
			</tbody>
		</table>

		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
		 <div class="single_variation_wrap" style="display:none;">
            		<?php do_action('woocommerce_single_variation'); ?>
        	</div>
		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<?php else : ?>

		<p class="stock out-of-stock"><?php _e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>

	<?php endif; ?>

</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
