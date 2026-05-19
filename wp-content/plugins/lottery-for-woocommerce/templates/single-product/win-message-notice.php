<?php
/**
 * This template is used for displaying the Win message notice. 
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/win-message-notice.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?> <p class="lty-winner-message"><?php echo wp_kses_post($win_message); ?></p>
											<?php

