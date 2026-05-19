<?php
/**
 * This template is used for pagination.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/pagination.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<nav class='pagination pagination-centered woocommerce-pagination'>
	<ul>
		<?php if ( $prev_arrows ) : ?>
			<li><a href='#' class='lty-pagination lty-first-pagination' data-page='1'><<</a>
			<li><a href='#' class='lty-pagination lty-prev-pagination' data-page='<?php echo esc_attr( $prev_page_count ); ?>'><</a></li>
		<?php endif; ?>

		<li><a href='#' class="<?php echo esc_attr( implode( ' ', lty_get_pagination_classes( 1, $current_page ) ) ); ?>" data-page='1'>1</a></li>

		<?php if ( $prev_dot ) : ?>
			<li><span class='lty-prev-pagination-dot' disabled='disabled'>...</a></li> 
			<?php
		endif;

		for ( ; $start_page <= $end_page; $start_page++ ) :
			$page_no = lty_get_pagination_number( $start_page, $page_count, $current_page );
			if ( $page_no ) :
				?>
				<li>
					<a href="#" class="<?php echo esc_attr( implode( ' ', lty_get_pagination_classes( $start_page, $current_page ) ) ); ?>"
						data-page="<?php echo esc_attr( $page_no ); ?>">
							<?php echo esc_html( $page_no ); ?>
					</a>
				</li>
				<?php
			endif;
		endfor;

		if ( $next_dot ) :
			?>
			<li><span class='lty-next-pagination-dot' disabled='disabled'>...</span></li> 
			<?php
		endif;

		if ( $next_arrows ) :
			if ( ( $page_count - 1 ) != $current_page ) :
				?>
				<li><a href='#' class="<?php echo esc_attr( implode( ' ', lty_get_pagination_classes( $page_count, $current_page ) ) ); ?>" data-page='<?php echo esc_attr( $page_count ); ?>'><?php echo esc_html( $page_count ); ?></a></li>
				<?php
			endif;
			?>
			<li><a href='#' class='lty-pagination lty-next-pagination' data-page='<?php echo esc_attr( $next_page_count ); ?>'>></a></li>
			<li><a href='#' class='lty-pagination lty-last-pagination' data-page='<?php echo esc_attr( $page_count ); ?>'>>></a></li>
			<?php endif; ?>
	</ul>
</nav>
<?php
