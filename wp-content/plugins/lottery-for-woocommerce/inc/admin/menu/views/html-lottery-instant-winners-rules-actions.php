<?php
/**
 * Instant winners rules actions.
 *
 * @since 9.5.0
 */
defined('ABSPATH') || exit; // Exit if accessed directly.
?>
<div class='lty-instant-winners-rules-actions-wrapper'>
	<div class='lty-instant-winners-rules-bulk-actions-wrapper'>
		<select class='lty-instant-winners-rules-bulk-action'>
			<option value='' selected><?php esc_html_e('Bulk Actions', 'lottery-for-woocommerce'); ?></option>
			<option value='delete'><?php esc_html_e('Delete', 'lottery-for-woocommerce'); ?></option>
		</select>
		<input type='button' class='button lty-instant-winners-rules-bulk-action-apply-button' value="<?php esc_html_e('Apply', 'lottery-for-woocommerce'); ?>" >
		<a class='lty-add-new-instant-winner-rule button-primary' href='#lty_lottery_instant_winners_rule_modal' rel='modal:open'><?php esc_html_e('Add New Rule', 'lottery-for-woocommerce'); ?></a>
		<input type='button' class='lty-save-instant-winners-rules button-primary' value="<?php esc_html_e('Save', 'lottery-for-woocommerce'); ?>" disabled />
	</div>

	<?php if ($total_instant_winner_count) : ?>
		<div class='lty-lottery-instant-winners-rules-pagination-wrapper'>
			<span class='lty-lottery-instant-winner-items-count'>
			<?php
				/* translators: %1$s: Item count */
				printf(esc_html__('%1$s items', 'lottery-for-woocommerce'), esc_attr($total_instant_winner_count));
			?>
				</span>
			<span class='lty-lottery-instant-winners-rules-pagination-action lty-first-page button' data-page='1' title="<?php esc_html_e('First Page', 'lottery-for-woocommerce'); ?>" <?php echo ( 2 > $page_count || 1 === $current_page ) ? 'disabled' : ''; ?>><<</span>
			<span class='lty-lottery-instant-winners-rules-pagination-action lty-previous-page button' data-page="<?php echo esc_attr(1 < $current_page ? $current_page - 1 : 1 ); ?>" title="<?php esc_html_e('Previous Page', 'lottery-for-woocommerce'); ?>" <?php echo ( 2 > $page_count || 1 === $current_page ) ? 'disabled' : ''; ?>><</span>
			<input type='number' class='lty-current-page' data-page="<?php echo esc_attr($current_page); ?>" value="<?php echo esc_attr($current_page); ?>" min='1' max="<?php echo esc_attr($page_count); ?>" title="<?php esc_html_e('Current Page', 'lottery-for-woocommerce'); ?>">
			<span class='lty-instant-winners-rules-page-number'>
				<?php
				/* translators: %1$s: Total pages */
				printf(esc_html__('of %1$s', 'lottery-for-woocommerce'), esc_attr($page_count));
				?>
			</span>
			<span class='lty-lottery-instant-winners-rules-pagination-action lty-next-page button' data-page="<?php echo esc_attr($page_count < ( $current_page + 1 ) ? $current_page : $current_page + 1 ); ?>" title="<?php esc_html_e('Next Page', 'lottery-for-woocommerce'); ?>" <?php echo ( $page_count === $current_page ) ? 'disabled' : ''; ?>>></span>
			<span class='lty-lottery-instant-winners-rules-pagination-action lty-last-page button' data-page="<?php echo esc_attr($page_count); ?>" title="<?php esc_html_e('Last Page', 'lottery-for-woocommerce'); ?>" <?php echo ( $page_count === $current_page ) ? 'disabled' : ''; ?>>>></span>
		</div>
	<?php endif; ?>

	<?php require LTY_ABSPATH . 'inc/admin/menu/views/html-lottery-instant-winners-rule-popup.php'; ?>
</div>
<?php
