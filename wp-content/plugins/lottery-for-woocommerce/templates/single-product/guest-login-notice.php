<?php
/**
 * This template is used for displaying the Guest Error notice. 
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/guest-login-notice.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?> <p class="lty-guest-error"><?php echo wp_kses_post($guest_error_message); ?></p>
										 <?php

