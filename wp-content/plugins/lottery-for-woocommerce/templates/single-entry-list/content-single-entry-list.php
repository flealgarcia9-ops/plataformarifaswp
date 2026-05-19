<?php
/**
 * This template is used for pagination.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-entry-list/content-single-entry-list.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 9.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;

if ( post_password_required() ) {
	echo do_shortcode( get_the_password_form() );
	return;
}
?>
<div class='lty-entry-list-content-wrapper'>
	<?php
	/**
	 * Hook: lty_before_lottery_entry_list_content Hook.
	 *
	 * @since 9.0.0
	 */
	do_action( 'lty_before_lottery_entry_list_content' );
	?>
	<?php
	/**
	 * Hook: lty_lottery_entry_list_content.
	 *
	 * @since 9.0.0
	 */
	do_action( 'lty_lottery_entry_list_content', $product );
	?>
	<?php
	/**
	 * Hook: lty_after_lottery_entry_list_content Hook.
	 *
	 * @since 9.0.0
	 */
	do_action( 'lty_after_lottery_entry_list_content' );
	?>
</div>
<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
