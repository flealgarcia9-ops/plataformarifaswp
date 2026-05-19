<?php
/**
 * Lottery screen options.
 * 
 * @since 8.2.0
 **/

if (! defined('ABSPATH')) {
	exit ; // Exit if accessed directly.
} ?>

<fieldset class='screen-options lty-lottery-screen-options'>
	<legend><?php esc_html_e('Pagination', 'lottery-for-woocommerce'); ?></legend>
		<?php
		if ('view' === $current_page) : 
			?>
			<p class='lty-lottery-screen-option-field'>
				<label for='lty_lottery_ticket_per_page'><?php esc_html_e('Number of items per page for giveaway tickets:', 'lottery-for-woocommerce'); ?></label>
				<input type='number' step='1' min='1' max='999'
					name='lty_lottery_ticket_per_page'
					id='lty_lottery_ticket_per_page' maxlength='3'
					value="<?php echo esc_attr(lty_get_items_per_page('lottery_ticket')); ?>" />
			</p>
			
			<?php
			if ($display_instant_winners) : 
				?>
				<p class='lty-lottery-screen-option-field'>
					<label for='lty_lottery_instant_winners_per_page'><?php esc_html_e('Number of items per page for instant win prizes:', 'lottery-for-woocommerce'); ?></label>
					<input type='number' step='1' min='1' max='999'
						name='lty_lottery_instant_winners_per_page'
						id='lty_lottery_instant_winners_per_page' maxlength='3'
						value="<?php echo esc_attr(lty_get_items_per_page('lottery_instant_winners')); ?>" />
				</p>
				<?php
			endif;
		else : 
			?>
			<p class='lty-lottery-screen-option-field'>
				<label for='lty_lottery_per_page'><?php esc_html_e('Number of items per page:', 'lottery-for-woocommerce'); ?></label>
				<input type='number' step='1' min='1' max='999'
					name='lty_lottery_per_page'
					id='lty_lottery_per_page' maxlength='3'
					value="<?php echo esc_attr(lty_get_items_per_page('lottery')); ?>" />
			</p>
			<?php 
		endif; 
		?>
</fieldset>
