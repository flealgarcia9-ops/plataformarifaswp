<?php
/**
 * View Lottery Tickets.
 *
 * @since 1.0.0
 * */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$relist_data = array();
$product_id  = isset($_GET['product_id']) ? absint($_GET['product_id']) : ''; // @codingStandardsIgnoreLine.
$lty_product = wc_get_product( $product_id );
if ( ! is_object( $lty_product ) ) {
	return;
}
?>

<!--overview of old data-->
<div class="lty-product-overview">
	<div class="lty-product-overview-container">
		<div class="lty-product-status">
			<h3><?php esc_html_e( 'Giveaway Status', 'Lottery-for-woocommerce' ); ?></h3>
			<?php
			if ( lty_check_is_array( $lottery_ticket_overview_datas ) ) :

				foreach ( $lottery_ticket_overview_datas as $product_status_data ) :
					?>
					<p>
						<span class="lty-table-cell"><?php echo wp_kses_post( $product_status_data['label'] ); ?></span>
						<span class="lty-table-value-separator">:</span>
						<span class="lty-table-cell"><?php echo wp_kses_post( $product_status_data['value'] ); ?> </span>
					</p>
					<?php
				endforeach;

			endif;
			?>
		</div>
		<div class="lty-lottery-configuration">
			<h3><?php esc_html_e( 'Giveaway Configuration', 'lottery-for-woocommerce' ); ?></h3>
			<?php
			if ( lty_check_is_array( $product_ticket_config_datas ) ) :
				$hide_content = '';
				$line_item    = 1;
				foreach ( $product_ticket_config_datas as $key => $product_config_data ) :
					$hide_content = $line_item > 6 ? 'lty-hidden-content' : '';
					switch ( $key ) :
						case 'question_answers':
							?>
							<h3> <?php esc_html_e( 'Q & A', 'lottery-for-woocommerce' ); ?></h3>
							<?php
							foreach ( $product_config_data as $question ) :
								?>
								<p class="<?php echo esc_attr( $hide_content ); ?>">
									<span class="lty-table-cell"><?php echo wp_kses_post( $question['label'] ); ?></span>
									<span class="lty-table-value-separator">:</span>
									<span class="lty-table-cell"><?php echo wp_kses_post( $question['value'] ); ?> </span>
								</p>
								<?php
							endforeach;

							break;
						case 'predefined_buttons':
							?>
							<h3><?php esc_html_e( 'Predefined Buttons', 'lottery-for-woocommerce' ); ?></h3>

							<p class="<?php echo esc_attr( $hide_content ); ?>">
								<span class="lty-table-cell"><?php echo wp_kses_post( $product_config_data['label'] ); ?></span>
								<span class="lty-table-value-separator">:</span>
								<span class="lty-table-cell"><?php echo wp_kses_post( $product_config_data['value'] ); ?> </span>
							</p>

							<?php
							break;
						case 'start_date':
						case 'end_date':
							?>
							<p class="<?php echo esc_attr( $hide_content ); ?>">
								<span class="lty-table-cell"><?php echo esc_html( $product_config_data['label'] ); ?></span>
								<span class="lty-table-value-separator">:</span>
								<span class="lty-table-cell"><?php echo wp_kses_post( LTY_Date_Time::get_wp_format_datetime_from_gmt( $product_config_data['value'] ) ); ?> </span>
							</p>

							<?php
							break;
						default:
							/**
							 * This hook is used to display the content before the lottery configuration information.
							 *
							 * @since 6.7
							 */
							do_action( 'lty_before_configuration_info', $key, $product_config_data );
							?>
							<p class="<?php echo esc_attr( $hide_content ); ?>">
								<span class="lty-table-cell"><?php echo wp_kses_post( $product_config_data['label'] ); ?></span>
								<span class="lty-table-value-separator">:</span>
								<span class="lty-table-cell"><?php echo wp_kses_post( $product_config_data['value'] ); ?> </span>
							</p>
							<?php
							/**
							 * This hook is used to display the content after the lottery configuration information.
							 *
							 * @since 6.7
							 */
							do_action( 'lty_after_configuration_info', $key, $product_config_data );
							break;
					endswitch;

					$line_item++;
				endforeach;

			endif;
			?>
			<a href='#' class='lty-toggle-lottery-configuration-info' data-action='view_more'><?php esc_html_e( 'View More', 'lottery-for-woocommerce' ); ?></a>
		</div>
	</div>
</div>

<div id="lty-view-ticket-log" class="panel">
	<div class="lty-lottery-ticket-heading">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Giveaway Tickets', 'lottery-for-woocommerce' ); ?></h1>
	</div>

	<div id='lty-ticket-list-table'><?php lty_render_ticket_list_table(); ?></div>
</div>

<?php
global $current_section;

$lottery_status = $lty_product->has_lottery_status( 'lty_lottery_finished' );
$relist_data    = array_reverse( (array) $lty_product->get_lty_relists() );
if ( $lty_product->get_lty_relisted() && isset( $_GET['section'] ) && ! empty( absint( $_GET['section'] ) ) ) {
	$index          = isset( $_GET['section'] ) ? absint( $_GET['section'] - 1 ) : 0;
	$relist_status  = isset( $relist_data[ $index ]['lottery_status'] ) ? $relist_data[ $index ]['lottery_status'] : 'lty_lottery_failed';
	$lottery_status = 'lty_lottery_finished' == $relist_status ? true : false;
}

if ( $lottery_status ) :
	$from_date    = false;
	$to_date      = false;
	$relist_count = false;
	if ( $current_section ) {
		$from_date    = isset( $relist_data[ $current_section - 1 ]['start_date_gmt'] ) ? $relist_data[ $current_section - 1 ]['start_date_gmt'] : false;
		$to_date      = isset( $relist_data[ $current_section - 1 ]['finished_date_gmt'] ) ? $relist_data[ $current_section - 1 ]['finished_date_gmt'] : false;
		$relist_count = count( $relist_data ) - intval( $current_section );
	} else {
		$from_date = $lty_product->get_current_start_date_gmt();
	}

	$_columns = array(
		'id'            => __( 'Winner ID', 'lottery-for-woocommerce' ),
		'user_name'     => __( 'User Name', 'lottery-for-woocommerce' ),
		'ticket_number' => __( 'Ticket Number', 'lottery-for-woocommerce' ),
		'answer'        => __( 'Chosen Answer', 'lottery-for-woocommerce' ),
		'order_id'      => __( 'Order ID', 'lottery-for-woocommerce' ),
		'gift_products' => __( 'Gift Products', 'lottery-for-woocommerce' ),
		'date'          => __( 'Date', 'lottery-for-woocommerce' ),
	);

	if ( ! $lty_product->is_valid_question_answer() ) {
		unset( $_columns['answer'] );
	}

	$args = array( 'product_id' => $product_id );
	if ( $lty_product->is_unlimited_scheduled_lottery( $relist_count ) ) {
		$args['list_count'] = $relist_count;
	} else {
		$args['start_date'] = $from_date;
		$args['end_date']   = $to_date;
	}

	$winner_tickets_ids = lty_get_lottery_winner_ids( $args );
	?>

	<div id="lty-view-winner-log" class="panel">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Giveaway Winners', 'lottery-for-woocommerce' ); ?></h1>
		<?php
		if ( lty_check_is_array( $winner_tickets_ids ) ) {
			ob_start();
			include_once LTY_ABSPATH . 'inc/admin/menu/views/html-lottery-winner-details.php';
			$contents = ob_get_contents();
			ob_end_clean();
			echo wp_kses_post( $contents );
		} else {
			?>
			<div class="lty_log_empty_container">
				<?php esc_html_e( 'No Winners Found.', 'lottery-for-woocommerce' ); ?>
			</div>
		<?php } ?>
	</div>
	<?php
endif;

$instant_winner = true;

if ( $current_section ) {
	$relist_count = count( $relist_data ) - $current_section;
	if ( isset( $relist_data[ $relist_count ]['instant_winner'] ) ) {
		$instant_winner = 'yes' !== $relist_data[ $relist_count ]['instant_winner'] ? false : true;
	}
} elseif ( ! $lty_product->is_instant_winner() ) {
	$instant_winner = false;
}

if ( $instant_winner ) :
	?>
	<div id="lty-view-instant-winners-log" class="panel">
		<div class="lty-instant-winners-prizes-heading">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Instant Win Prizes', 'lottery-for-woocommerce' ); ?></h1>
		</div>
		<div>
			<?php
			if ( ! class_exists( 'LTY_Lottery_Instant_Winners_Table' ) ) {
				require_once LTY_PLUGIN_PATH . '/inc/admin/menu/wp-list-table/class-lty-lottery-instant-winners-list-table.php';
			}

			$post_table = new LTY_Lottery_Instant_Winners_Table();
			$post_table->render();
			?>
		</div>
	</div>
	<?php
endif;

/**
 * This hook is used to display the content after the lottery tickets contents.
 *
 * @since 6.7
 */
do_action( 'lty_after_lottery_tickets_contents' );
?>
