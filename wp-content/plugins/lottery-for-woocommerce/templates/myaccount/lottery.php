<?php
/**
 * This template is used for displaying the myaccount lottery menu.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/myaccount/lottery.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 9.1.0
 * @var string $current_lottery_menu Current lottery menu.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

?>
<div class='lty-myaccount-lottery-wrapper'>
	<?php
	/**
	 * This hook is used to display the myaccount lottery headers.
	 *
	 * @since 9.1.0
	 * @hooked LTY_Myaccount_Handler::render_myaccount_lottery_title - 10.
	 * @hooked LTY_Myaccount_Handler::render_myaccount_lottery_navigation - 20.
	 */
	do_action( 'lty_before_myaccount_lottery_contents', $current_lottery_menu );
	?>

	<div class='lty-myaccount-lottery-contents'>
		<?php
		/**
		 * This hook is used to display the myaccount lottery current menu contents.
		 *
		 * @since 9.1.0
		 */
		do_action( 'lty_myaccount_lottery_' . $current_lottery_menu . '_contents' );

		/**
		 * This hook is used to display the myaccount lottery contents.
		 *
		 * @since 9.1.0
		 * @hooked LTY_Myaccount_Handler::render_myaccount_lottery_contents - 10.
		 */
		do_action( 'lty_myaccount_lottery_contents', $current_lottery_menu );
		?>
	</div>

	<?php
	/**
	 * This hook is used to display the extra content after myaccount lottery contents.
	 *
	 * @since 9.1.0
	 */
	do_action( 'lty_after_myaccount_lottry_contents', $current_lottery_menu );
	?>
</div>
<?php
