<?php
/**
 * This template is used for displaying the dashboard.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/dashboard/dashboard.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
global $current_lottery_menu ;
?>
<div class="lty-dashboard-wrapper">
	<?php
	/**
	 * This hook is used to display the dashboard headers.
	 * 
	 * @hooked LTY_Dashboard::render_dashboard_title - 10.
	 * @hooked LTY_Dashboard::render_dashboard_navigation - 20.
	 * @since 1.0
	 */
	do_action( 'lty_before_dashboard_contents' ) ;
	?>

	<div class ="lty-dashboard-contents">
		<?php
		/**
		 * This hook is used to display the dashboard current menu contents.
		 * 
		 * @since 1.0
		 */
		do_action( 'lty_dashboard_' . $current_lottery_menu . '_contents' ) ;
		/**
		 * This hook is used to display the dashboard contents.
		 * 
		 * @hooked LTY_Dashboard::render_dashboard_menu_contents - 10.
		 * @since 1.0
		 */
		do_action( 'lty_dashboard_contents' ) ;
		?>

	</div>

	<?php
	/**
	 * This hook is used to display the extra content after dashboard contents.
	 * 
	 * @since 1.0
	 */
	do_action( 'lty_after_dashboard_contents' ) ;
	?>
</div>
<?php
