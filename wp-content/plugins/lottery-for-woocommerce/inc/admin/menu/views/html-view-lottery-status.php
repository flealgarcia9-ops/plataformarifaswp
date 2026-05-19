<?php
/**
 * View Lottery.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

global $post ;

/**
 * This hook is used to alter the lottery product ID.
 * 
 * @since 1.0
 */
$product_id = apply_filters( 'lty_lottery_product_id_in_meta_box' , $post->ID ) ;
$product    = wc_get_product( $product_id ) ;
?>
<p>
	<span><?php esc_html_e( 'Status :' , 'lottery-for-woocommerce' ) ; ?></span>
	<span><?php echo wp_kses_post( lty_display_status( $product->get_lty_lottery_status() ) ) ; ?></span>
</p>

<div class='lty-winner-selection-link' >
	<?php
	if ( $product->has_lottery_status( 'lty_lottery_closed' ) && '2' == $product->get_lty_winner_selection_method() ) {
		?>
		<p>
			<?php
			$ticket_log_url = add_query_arg( array( 'lty_action' => 'view', 'product_id' => $product->get_id(), 'tab' => 'tickets #lty-ticket-list-table' ) , lty_get_lottery_page_url() ) ;
			?>
			<span><?php printf( 'Click <a href="%s">%s</a> to select the winner(s) manually' , esc_url( $ticket_log_url ) , 'here' , 'lottery-for-woocommerce' ) ; ?></span>
		</p>
		<?php
	}
	?>
</div>

<div class='lty-view-lotteries'>

	<p>
		<span><?php esc_html_e( 'Ticket Count :' , 'lottery-for-woocommerce' ) ; ?></span>
		<span><?php echo esc_html( $product->get_purchased_ticket_count() ) ; ?></span>
	</p>
	<?php
	$ticket_log_url = add_query_arg( array( 'lty_action' => 'view', 'product_id' => $product->get_id(), 'tab' => 'tickets' ) , lty_get_lottery_page_url() ) ;

	echo '<a href="' . esc_url( $ticket_log_url ) . '">' . esc_html__( 'View tickets Log' , 'lottery-for-woocommerce' ) . '</a> | ' ;

	if ( 'lty_lottery_finished' == $product->get_lty_lottery_status() ) {
		$winner_log_url = add_query_arg( array( 'lty_action' => 'view', 'product_id' => $product->get_id(), 'tab' => 'winners' ) , lty_get_lottery_page_url() ) ;
		echo '<a href="' . esc_url( $winner_log_url ) . '">' . esc_html__( 'View Winners log' , 'lottery-for-woocommerce' ) . '</a> | ' ;
	}

	echo '<a href="' . esc_url( lty_get_lottery_page_url() ) . '">' . esc_html__( 'Giveaways' , 'lottery-for-woocommerce' ) . '</a>' ;
	?>
</div>
