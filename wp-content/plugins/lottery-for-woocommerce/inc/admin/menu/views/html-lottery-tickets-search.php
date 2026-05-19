<?php
/**
 * HTML - Lottery tickets search.
 *
 * @since 9.8.0
 */

defined( 'ABSPATH' ) || exit;

?>
<button class='button lty-search-field-button'><?php esc_html_e( 'Search Tickets', 'lottery-for-woocommerce' ); ?></button>
<div class="lty-search-fields-wrapper lty-lottery-tickets-search-wrapper <?php echo isset( $_REQUEST['s'] ) ? '' : 'lty-hide'; ?>">
	<h4><?php esc_html_e( 'Filters', 'lottery-for-woocommerce' ); ?></h4>
	<p class='lty-users-type-field'>
		<label for='lty_lottery_tickets_purchased_user_type'><?php esc_html_e( 'User Type', 'lottery-for-woocommerce' ); ?></label>
		<select name='lty_lottery_ticket_filters[user_type]' id='lty_lottery_tickets_purchased_user_type' class='lty-lottery-tickets-purchased-user-type'>
			<option value=''><?php esc_html_e( 'All Users', 'lottery-for-woocommerce' ); ?></option>
			<option value='1' <?php selected( '1', $filter_values['user_type'], true ); ?>><?php esc_html_e( 'Registered Users', 'lottery-for-woocommerce' ); ?></option>
			<option value='2' <?php selected( '2', $filter_values['user_type'], true ); ?>><?php esc_html_e( 'Guest Users', 'lottery-for-woocommerce' ); ?></option>
		</select>
	</p>
	<p class='lty-list-table-date-filter-fields'>
		<label for='lty_lottery_tickets_purchased_date_filter'><?php esc_html_e( 'Ticket Purchased Date', 'lottery-for-woocommerce' ); ?></label>
		<select name='lty_lottery_ticket_filters[purchased_date_filter_type]' id='lty_lottery_tickets_purchased_date_filter' class='lty-lottery-tickets-purchased-date-filter'>
			<?php foreach ( lty_get_lottery_tickets_purchased_date_filter_options() as $option_key => $option_name ) : ?>
				<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $filter_values['purchased_date_filter_type'], true ); ?>><?php echo esc_html( $option_name ); ?></option>
			<?php endforeach; ?>
		</select>
		<span class='lty-list-table-filter-date-range-field'>		
			<?php
				lty_get_datepicker_html(
					array(
						'id'          => 'lty_lottery_ticket_filters[purchased_from_date]',
						'with_time'   => false,
						'wp_zone'     => false,
						'value'       => $filter_values['purchased_from_date'],
						'placeholder' => LTY_Date_Time::get_wp_datetime_format(),
					)
				);

				lty_get_datepicker_html(
					array(
						'id'          => 'lty_lottery_ticket_filters[purchased_to_date]',
						'with_time'   => false,
						'wp_zone'     => false,
						'value'       => $filter_values['purchased_to_date'],
						'placeholder' => LTY_Date_Time::get_wp_datetime_format(),
					)
				);
				?>
		</span>
	</p>
	<h4><?php esc_html_e( 'Search', 'lottery-for-woocommerce' ); ?></h4>
	<p class='lty-lottery-tickets-search-columns-field'>
		<label for='lty_lottery_tickets_search_columns'><?php esc_html_e( 'Search Columns', 'lottery-for-woocommerce' ); ?></label>
		<select name='lty_lottery_ticket_filters[search_columns][]' id='lty_lottery_tickets_search_columns' class='lty-lottery-tickets-search-columns lty-tickets-search-columns lty_select2' multiple='multiple'>
			<?php foreach ( lty_get_lottery_ticket_search_columns() as $search_key => $search_column_name ) : ?>
				<option value="<?php echo esc_attr( $search_key ); ?>" <?php echo in_array( $search_key, $filter_values['search_columns'] ) ? 'selected' : ''; ?>><?php echo esc_html( $search_column_name ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p class='lty-lottery-tickets-search-field search-box'>
		<label class='screen-reader-text' for='lty-tickets-search-input'><?php esc_html_e( 'Search', 'lottery-for-woocommerce' ); ?></label>
		<input type='search' id='lty-tickets-search-input' name='s' value="<?php _admin_search_query(); ?>" />
		<select name='lty_lottery_ticket_filters[search_columns_type]' id='lty_lottery_tickets_search_columns_type' class='lty-lottery-tickets-search-columns-type'>
			<option value='1' <?php selected( '1', $filter_values['search_columns_type'], true ); ?>><?php esc_html_e( 'Exact & Related Values', 'lottery-for-woocommerce' ); ?></option>
			<option value='2' <?php selected( '2', $filter_values['search_columns_type'], true ); ?>><?php esc_html_e( 'Only Exact Value', 'lottery-for-woocommerce' ); ?></option>
		</select>
	</p>
	<p class='lty-tickets-search-submit-button'>
		<?php submit_button( __( 'Search', 'lottery-for-woocommerce' ), '', '', false, array( 'id' => 'lty-search-submit' ) ); ?>
		<input type='hidden' name='orderby' value="<?php echo esc_attr( $orderby ); ?>" />
		<input type='hidden' name='order' value="<?php echo esc_attr( $order ); ?>" />
		<input type='hidden' name='post_mime_type' value="<?php echo esc_attr( $post_mime_type ); ?>" />
		<input type='hidden' name='detached' value="<?php echo esc_attr( $detached ); ?>" />
	</p>
</div>
