<?php

/**
 * This template is displaying the single lottery entry list details.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-entry-list-page.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 9.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header( 'shop' );
?>

<?php

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 *
 * @since 9.0.0
 */
do_action( 'woocommerce_before_main_content' );

lty_get_template( 'single-entry-list/content-single-entry-list.php' );

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 *
 * @since 9.0.0
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 *
 * @since 9.0.0
 */
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
