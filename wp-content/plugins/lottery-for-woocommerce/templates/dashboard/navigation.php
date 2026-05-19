<?php
/**
 * This template is used for displaying the navigation.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/dashboard/navigation.php
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
<div class="lty-dashboard-navigation">
	<nav> 
		<?php
		global $post ;

		foreach ( lty_dashboard_menus() as $menu_key => $menu_values ) :

			$class_name = array( strtolower( str_replace( '_' , '-' , $menu_key ) ) ) ;

			if ( $current_lottery_menu === $menu_key ) {
				$class_name[] = 'lty-current' ;
			}
			?>
			<a href ="<?php echo esc_url( lty_get_endpoint_url( array( 'lty_dashboard_menu' => $menu_key ) ) ); ?>"
			   class="<?php echo esc_attr( implode( ' ' , $class_name ) ) ; ?>">
				<span class="dashicons dashicons-<?php echo esc_attr( $menu_values[ 'code' ] ); ?>"></span>
				<?php echo esc_html( $menu_values[ 'label' ] ) ; ?>
			</a>
			<?php
		endforeach ;
		?>
		 
	</nav>
</div>
<?php
