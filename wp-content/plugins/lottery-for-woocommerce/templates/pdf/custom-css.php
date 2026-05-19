<?php
/**
 * This template is used to customize the custom css.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/pdf/custom-css.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 9.5.0
 * */

defined( 'ABSPATH' ) || exit;
?>

.lty-lottery-ticket-wrapper {
	padding: 15px;
	background-repeat: no-repeat; 
	margin-bottom: 20px !important;
	box-sizing: border-box;
}

.lty-lottery-ticket-wrapper tr td {
	background: none !important;
	padding: 0 !important;
	color: #fff !important;
	vertical-align: top;
}

.lty-lottery-ticket-wrapper h4 {
	color: #f57436;
	margin: 0 !important;    
}

.lty-lottery-ticket-wrapper tr td small {
	color: #000;
}

.lty-lottery-ticket-wrapper .lty-lottery-ticket-content {
	background: none !important;
	padding: 0 !important;
	margin: 0 !important;
}
.lty-lottery-ticket-wrapper .lty-lottery-ticket-content tr td {
	padding: 10px 15px !important;
}

.lty-lottery-ticket-wrapper .lty-lottery-ticket-content tr td span {
	padding: 0 !important;
	font-weight: 500;
}

.lty-lottery-ticket-wrapper .lty-lottery-ticket-title {
	text-transform: uppercase;
	border-radius: 5px;
	font-size: 18px;    
}

.lty-lottery-ticket-wrapper .lty-product-name b {
	font-size: 18px !important;
	color:#000 !important;
}

.lty-lottery-ticket-wrapper .lty-user-name b,
.lty-lottery-ticket-wrapper .lty-phone-number b,
.lty-lottery-ticket-wrapper .lty-user-email b,
.lty-lottery-ticket-wrapper .lty-billing-address b {
	margin-bottom: 10px;
}

.lty-lottery-ticket-wrapper .lty-ticket-number,
.lty-lottery-ticket-wrapper .lty-purchased-date {
	color: #000 !important;
}

<?php

