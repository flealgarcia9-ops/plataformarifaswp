<?php
/**
 * Instant winners tab.
 *
 * @since 8.0.0
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div id='lty_instant_winner_tab' class='panel woocommerce_options_panel lty_lottery_product_tab'>
	<div class="<?php echo esc_attr( $wrapper_class_name ); ?>">
		<div class='options_group show_if_lottery'>
			<?php
			woocommerce_wp_checkbox(
				array(
					'id'    => 'lty_instant_winners',
					'value' => is_callable( array( $product_object, 'get_lty_instant_winners' ) ) ? $product_object->get_lty_instant_winners() : 'no',
					'class' => 'lty-instant-winners',
					'label' => __( 'Enable Instant Win Prizes', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_select(
				array(
					'id'      => 'lty_display_instant_winner_image',
					'label'   => __( 'Instant Win Prize Image', 'lottery-for-woocommerce' ),
					'value'   => is_callable( array( $product_object, 'get_lty_display_instant_winner_image' ) ) ? $product_object->get_lty_display_instant_winner_image( 'edit' ) : '2',
					'class'   => 'lty-instant-winner-rule-field',
					'options' => array(
						'1' => __( 'Enable', 'lottery-for-woocommerce' ),
						'2' => __( 'Disable', 'lottery-for-woocommerce' ),
					),
				)
			);
			woocommerce_wp_select(
				array(
					'id'          => 'lty_instant_winner_display_mode',
					'label'       => __( 'Instant Win Prize Display Mode', 'lottery-for-woocommerce' ),
					'value'       => is_callable( array( $product_object, 'get_lty_instant_winner_display_mode' ) ) ? $product_object->get_lty_instant_winner_display_mode( 'edit' ) : '1',
					'class'       => 'lty-instant-winner-rule-field',
					'options'     => array(
						'1' => __( 'Default', 'lottery-for-woocommerce' ),
						'2' => __( 'Display Prizes by Group', 'lottery-for-woocommerce' ),
					),
					'description' => __( '<b>Default:</b> Instant win prizes will be displayed in a table format(Displaying each prize with its corresponding ticket number). <b>Display Prizes by Group:</b> Instant win prizes will be displayed as a group(Group the ticket numbers under a common prize).', 'lottery-for-woocommerce' ),
					'desc_tip'    => true,
				)
			);
			?>
		</div>
		<?php lty_render_lottery_instant_winners_rules( $product_object ); ?>
	</div>
</div>
<?php
