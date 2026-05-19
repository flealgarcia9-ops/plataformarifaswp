<?php
/**
 * HTML - Lottery search filters.
 *
 * @since 10.2.0
 */

defined( 'ABSPATH' ) || exit;

?>
<button class='button lty-search-field-button'><?php esc_html_e( 'Search Giveaway', 'lottery-for-woocommerce' ); ?></button>
<div class="lty-search-fields-wrapper lty-lottery-filters-wrapper <?php echo isset( $_REQUEST['s'] ) ? '' : 'lty-hide'; ?>">
	<h4><?php esc_html_e( 'Filters', 'lottery-for-woocommerce' ); ?></h4>
	<p class='lty-lottery-ticket-generation-type-filter-field'>
		<label for='lty_lottery_ticket_generation_type_filter'><?php esc_html_e( 'Ticket Generation Type', 'lottery-for-woocommerce' ); ?></label>
		<select name='lty_lottery_filters[ticket_generation_type]' class='lty-lottery-ticket-generation-type-filter' id='lty_lottery_ticket_generation_type_filter'>
			<?php foreach ( lty_get_lottery_ticket_generation_type_filter_options() as $option_key => $option_name ) : ?>
				<option value="<?php echo esc_attr( $option_key ); ?>" <?php echo $option_key == $filter_values['ticket_generation_type'] ? 'selected' : ''; ?>><?php echo esc_html( $option_name ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>

	<p class='lty-lottery-winner-selection-type-filter-field'>
		<label for='lty_lottery_winner_selection_type_filter'><?php esc_html_e( 'Winner Selection Type', 'lottery-for-woocommerce' ); ?></label>
		<select name='lty_lottery_filters[winner_selection_type]' class='lty-lottery-winner-selection-type-filter' id='lty_lottery_winner_selection_type_filter'>
			<?php foreach ( lty_get_lottery_winner_selection_type_filter_options() as $option_key => $option_name ) : ?>
				<option value="<?php echo esc_attr( $option_key ); ?>" <?php echo $option_key == $filter_values['winner_selection_type'] ? 'selected' : ''; ?>><?php echo esc_html( $option_name ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>

	<p class='lty-lottery-search-field search-box'>
		<label class='screen-reader-text' for='lty-lottery-search-input'><?php esc_html_e( 'Search', 'lottery-for-woocommerce' ); ?></label>
		<input type='search' id='lty-lottery-search-input' name='s' value="<?php _admin_search_query(); ?>" placeholder="<?php esc_html_e( 'Search giveaway', 'lottery-for-woocommerce' ); ?>"/>
		<?php submit_button( __( 'Search', 'lottery-for-woocommerce' ), '', '', false, array( 'id' => 'lty-search-submit' ) ); ?>
		<input type='hidden' name='orderby' value="<?php echo esc_attr( $orderby ); ?>" />
		<input type='hidden' name='order' value="<?php echo esc_attr( $order ); ?>" />
		<input type='hidden' name='post_mime_type' value="<?php echo esc_attr( $post_mime_type ); ?>" />
		<input type='hidden' name='detached' value="<?php echo esc_attr( $detached ); ?>" />
	</p>
</div>
