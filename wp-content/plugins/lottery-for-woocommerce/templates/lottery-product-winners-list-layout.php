<?php
/**
 * This template is used for displaying the lottery product winners list layout.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/lottery-product-winners-list-layout.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<table class="lty-frontend-table lty-lottery-winners-list-table lty-data-table-wrapper">
	<thead>
	<h2><?php echo wp_kses_post( lty_get_winners_list_shortcode_title() ); ?></h2>
	<tr>
		<?php foreach ( $columns as $column_name ) : ?>
			<th><?php echo esc_html( $column_name ) ; ?></th>
		<?php endforeach ; ?>
	</tr>
</thead>
<tbody>
	<?php
		lty_get_template( 'lottery-product-winners-list.php', array( 'columns' => lty_get_lottery_shortcode_winner_table_header(), 'winner_ids' => $winner_ids, 'offset' => $offset ) );
	?>
</tbody>

<?php if ( $pagination[ 'page_count' ] > 1 ) : ?>
	<tfoot>
		<tr>
			<td colspan="<?php echo esc_attr( count( $columns ) ) ; ?>" class="footable-visible actions" data-action_name="lty_winners_list">
				<?php lty_get_template( 'pagination.php', $pagination ) ; ?>
			</td>
		</tr>
	</tfoot>
<?php endif ; ?>
</table>
<?php
