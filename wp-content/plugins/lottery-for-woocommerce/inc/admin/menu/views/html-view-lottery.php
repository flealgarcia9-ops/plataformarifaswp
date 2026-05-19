<?php
/**
 * View lottery.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class = "wrap <?php echo esc_attr( self::$plugin_slug ); ?>_wrapper_cover woocommerce">
	<div class="lty_register_table_header">
		<h1 class="wp-heading-inline"><?php echo esc_html( $lty_product->get_title() ); ?></h1>
		<?php wc_back_link( '', lty_get_lottery_page_url() ); ?>
		<a class="page-title-action" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=product&lty_lottery_product=new' ) ); ?>"> <?php esc_html_e( 'Add New Giveaway', 'lottery-for-woocommerce' ); ?></a>
		<a class="page-title-action" href="<?php echo esc_url( get_edit_post_link( $lty_product->get_id() ) ); ?>"><?php esc_html_e( 'Edit Giveaway', 'lottery-for-woocommerce' ); ?></a>
		<a class="page-title-action" href="<?php echo esc_url( get_permalink( $lty_product->get_id() ) ); ?>"><?php esc_html_e( 'View Giveaway', 'lottery-for-woocommerce' ); ?></a>
		<button class='page-title-action lty-export-popup' data-export_type='lottery_tickets' 
				data-extra_data="<?php echo esc_attr(wp_json_encode(array( 'product_id' => $lty_product->get_id(), 'export_lottery' => 'single' ))); ?>">
					<?php esc_html_e('Export all tickets of this giveaway CSV', 'lottery-for-woocommerce'); ?>
		</button>
		<hr class="wp-header-end">
	</div>
	<form method = "post" id="lty_lottery_form" enctype = "multipart/form-data">
		<div class = "<?php echo esc_attr( self::$plugin_slug ); ?>_wrapper">
			<nav class = "nav-tab-wrapper woo-nav-tab-wrapper <?php echo esc_attr( self::$plugin_slug ); ?>_tab_ul">
				<?php foreach ( $tabs as $name => $label ) { ?>
					<a href="
					<?php
					echo esc_url(
						lty_get_lottery_page_url(
							array(
								'lty_action' => 'view',
								'product_id' => $lottery_id,
								'tab'        => $name,
							)
						)
					);
					?>
								" class="nav-tab <?php echo esc_html( self::$plugin_slug ); ?>_tab_a <?php echo esc_attr( $name ) . '_a ' . ( $current_tab == $name ? 'nav-tab-active' : '' ); ?>">
						<span><?php echo esc_html( $label ); ?></span>
					</a>
				<?php } ?>
			</nav>
			<div class="<?php echo esc_attr( self::$plugin_slug ); ?>_tab_content lty_<?php echo esc_attr( $current_tab ); ?>_tab_content_wrapper">
				<?php
				/**
				 * This hook is used to display the lottery current tab sections.
				 *
				 * @since 1.0
				 */
				do_action( sanitize_key( self::$plugin_slug . '_lottery_' . $current_tab . '_sections' ) );
				?>
				<div class="<?php echo esc_attr( self::$plugin_slug ); ?>_tab_inner_content lty_<?php echo esc_attr( $current_tab ); ?>_tab_inner_content">
					<?php
					/**
					 * This hook is used to display the lottery current tab contents.
					 *
					 * @since 1.0
					 */
					do_action( sanitize_key( self::$plugin_slug . '_lottery_' . $current_tab . '_content' ) );
					?>
				</div>
			</div>
		</div>
	</form>
</div>
<?php
