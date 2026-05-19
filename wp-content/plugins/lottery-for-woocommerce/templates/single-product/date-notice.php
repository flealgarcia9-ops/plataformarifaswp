<?php
/**
 * This template is used for displaying the date notice.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/date-notice.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 9.2.0
 * @var object $product Product object.
 */

defined( 'ABSPATH' ) || exit;

if ( ! $product->is_started() ) :
	if ( lty_display_date_starts_on_label_in_single_product() ) : ?>
		<p class='lty-lottery-start-time-label'>
			<span><?php echo wp_kses_post( lty_get_single_product_page_start_label( $product->get_id(), $product ) ); ?></span>
		</p>
		<?php
	endif;
elseif ( lty_display_date_ends_on_label_in_single_product() ) :
	?>
	<p class='lty-lottery-end-time-label'>
		<span><?php echo wp_kses_post( lty_get_single_product_page_end_label( $product ) ); ?></span>
	</p>
	<?php
endif;


