<?php
/**
 *  Admin HTML Settings 
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<div class = "wrap <?php echo esc_attr( self::$plugin_slug ) ; ?>_wrapper_cover woocommerce">
	<?php
	/**
	 * This hook is used to display the extra content before settings form.
	 * 
	 * @since 1.0
	 */
	do_action( sanitize_key( self::$plugin_slug . '_before_settings_form' ) ) ;
	?>
	<form method = "post" id="lty_lottery_settings_form" enctype = "multipart/form-data">
		<div class = "<?php echo esc_attr( self::$plugin_slug ) ; ?>_wrapper">
			<nav class = "nav-tab-wrapper woo-nav-tab-wrapper <?php echo esc_attr( self::$plugin_slug ) ; ?>_tab_ul">
				<?php foreach ( $tabs as $name => $label ) { ?>
					<a href="<?php echo esc_url( lty_get_settings_page_url( array( 'tab' => $name ) ) ) ; ?>" class="nav-tab <?php echo esc_html( self::$plugin_slug ) ; ?>_tab_a <?php echo esc_attr( $name ) . '_a ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) ; ?>">
						<span><?php echo esc_html( $label ) ; ?></span>
					</a>
				<?php } ?>
			</nav>
			<div class="<?php echo esc_attr( self::$plugin_slug ) ; ?>_tab_content lty_<?php echo esc_attr( $current_tab ) ; ?>_tab_content_wrapper">
				<?php
				/**
				 * This hook is used to display the settings current tab sections.
				 * 
				 * @since 1.0
				 */
				do_action( sanitize_key( self::$plugin_slug . '_sections_' . $current_tab ) ) ;
				?>
				<div class="<?php echo esc_attr( self::$plugin_slug ) ; ?>_tab_inner_content lty_<?php echo esc_attr( $current_tab ) ; ?>_tab_inner_content">
					<?php
					/**
					 * This hook is used to display the extra content before setting current tab content.
					 * 
					 * @since 1.0
					 */
					do_action( sanitize_key( self::$plugin_slug . '_before_settings_current_tab_content' ) ) ;

					/**
					 * Display error or warning messages. 
					 * */
					self::show_messages() ;

					/**
					 * This hook is used to display the settings current tab content.
					 * 
					 * @hooked LTY_Settings_Page->output - 10 (content).
					 * @hooked LTY_Settings_Page->output_buttons - 20 (buttons).
					 * @hooked LTY_Settings_Page->output_extra_fields - 30 (extra fields).
					 * @since 1.0
					 */
					do_action( sanitize_key( self::$plugin_slug . '_settings_' . $current_tab ) ) ;
					?>
				</div>
			</div>
		</div>
	</form>
	<?php
	/**
	 * This hook is used to display the extra content after settings form.
	 * 
	 * @since 1.0
	 */
	do_action( sanitize_key( self::$plugin_slug . '_after_settings_form' ) ) ;
	?>
</div>
<?php
