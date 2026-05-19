<?php
/**
 * Instant winner prize groups actions.
 *
 * @since 11.1.0
 * @var object $product Product object.
 * @var array  $prize_group_ids Instant prize group IDs.
 * @var int    $total_prize_group_ids_count All the instant winner prize group ID's count.
 * @var int    $current_page Current page.
 * @var int    $page_count Page count.
 * @var int    $items_per_page Number of items per page.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>
<div class='lty-instant-winner-prize-groups-actions-wrapper'>
	<div class='lty-instant-winner-prize-groups-bulk-actions-wrapper'>
		<select class='lty-instant-winner-prize-groups-bulk-action'>
			<option value='' selected><?php esc_html_e( 'Bulk Actions', 'lottery-for-woocommerce' ); ?></option>
			<option value='delete'><?php esc_html_e( 'Delete', 'lottery-for-woocommerce' ); ?></option>
		</select>
		<input type='button' class='button lty-instant-winner-prize-groups-bulk-action-apply-btn' value="<?php esc_html_e( 'Apply', 'lottery-for-woocommerce' ); ?>" >
		<a class='button button-primary lty-add-new-instant-winner-prize-group' href='#'><?php esc_html_e( 'Add New Prize Group', 'lottery-for-woocommerce' ); ?></a>
		<input type='button' class='button button-primary lty-save-instant-winner-prize-groups' value="<?php esc_html_e( 'Save', 'lottery-for-woocommerce' ); ?>" disabled />
	</div>

	<?php if ( $total_prize_group_ids_count ) : ?>
		<div class='lty-instant-winner-prize-groups-pagination-wrapper'>
			<span class='lty-instant-winner-prize-group-items-count'>
				<?php
				/* translators: %1$s: Item count */
				printf( esc_html__( '%1$s items', 'lottery-for-woocommerce' ), esc_attr( $total_prize_group_ids_count ) );
				?>
			</span>
			<span class='button lty-first-page lty-instant-winner-prize-groups-pagination-action' data-page='1' title="<?php esc_html_e( 'First Page', 'lottery-for-woocommerce' ); ?>" <?php echo ( 2 > $page_count || 1 === $current_page ) ? 'disabled' : ''; ?>><<</span>
			<span class='button lty-previous-page lty-instant-winner-prize-groups-pagination-action' data-page="<?php echo esc_attr( 1 < $current_page ? $current_page - 1 : 1 ); ?>" title="<?php esc_html_e( 'Previous Page', 'lottery-for-woocommerce' ); ?>" <?php echo ( 2 > $page_count || 1 === $current_page ) ? 'disabled' : ''; ?>><</span>
			<input type='number' class='lty-current-page' data-page="<?php echo esc_attr( $current_page ); ?>" value="<?php echo esc_attr( $current_page ); ?>" min='1' max="<?php echo esc_attr( $page_count ); ?>" title="<?php esc_html_e( 'Current Page', 'lottery-for-woocommerce' ); ?>">
			<span class='lty-instant-winner-prize-groups-total-page'>
				<?php
				/* translators: %1$s: Total pages */
				printf( esc_html__( 'of %1$s', 'lottery-for-woocommerce' ), esc_attr( $page_count ) );
				?>
			</span>
			<span class='button lty-next-page lty-instant-winner-prize-groups-pagination-action' data-page="<?php echo esc_attr( $page_count < ( $current_page + 1 ) ? $current_page : $current_page + 1 ); ?>" title="<?php esc_html_e( 'Next Page', 'lottery-for-woocommerce' ); ?>" <?php echo ( $page_count === $current_page ) ? 'disabled' : ''; ?>>></span>
			<span class='button lty-last-page lty-instant-winner-prize-groups-pagination-action' data-page="<?php echo esc_attr( $page_count ); ?>" title="<?php esc_html_e( 'Last Page', 'lottery-for-woocommerce' ); ?>" <?php echo ( $page_count === $current_page ) ? 'disabled' : ''; ?>>>></span>
		</div>
	<?php endif; ?>
</div>
<?php
