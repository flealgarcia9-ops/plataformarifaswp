<?php

/**
 * Recent Viewed Lottery Widget.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Recent_Viewed_Lottery' ) ) {

	/**
	 * Class LTY_Recent_Viewed_Lottery.
	 * */
	class LTY_Recent_Viewed_Lottery extends WC_Widget {

		/**
		 * Class Constructor.
		 * */
		public function __construct() {

			$this->widget_cssclass    = 'woocommerce lty_recent_viewed_lottery' ;
			$this->widget_description = __( 'Display Recently Viewed Giveaway', 'lottery-for-woocommerce' ) ;
			$this->widget_id          = 'lty_recent_viewed_lottery' ;
			$this->widget_name        = __( 'Recently Viewed Giveaway', 'lottery-for-woocommerce' ) ;

			$this->populate_settings() ;

			parent::__construct() ;
		}

		/**
		 * Populate settings.
		 *          
		 * @return array
		 * */
		private function populate_settings() {
			/**
			 * This hook is used to alter the recent viewed lottery widget settings.
			 * 
			 * @since 1.0
			 */
			$this->settings = apply_filters( 'lty_recent_viewed_lottery_widget_settings', array(
				'title'     => array(
					'type'  => 'text',
					'std'   => __( 'Recently Viewed Giveaway', 'lottery-for-woocommerce' ),
					'label' => __( 'Title', 'lottery-for-woocommerce' ),
				),
				'lty_limit' => array(
					'type'  => 'text',
					'std'   => '5',
					'label' => __( 'Product Limit', 'lottery-for-woocommerce' ),
				),
				'lty_order' => array(
					'type'    => 'select',
					'std'     => 'DESC',
					'label'   => __( 'Order', 'lottery-for-woocommerce' ),
					'options' => array(
						'ASC'  => __( 'Ascending', 'lottery-for-woocommerce' ),
						'DESC' => __( 'Descending', 'lottery-for-woocommerce' ),
					),
				),
					) ) ;
		}

		/**
		 * Output widget.
		 *
		 * */
		public function widget( $args, $instance ) {

			if ( ! isset( $instance[ 'title' ] ) ) {
				$instance[ 'title' ] = __( 'Recently Viewed Giveaway', 'lottery-for-woocommerce' ) ;
			}

			$viewed_products = ! empty( $_COOKIE[ 'lty_recent_viewed_lottery' ] ) ? ( array ) explode( '|', wc_clean( wp_unslash( $_COOKIE[ 'lty_recent_viewed_lottery' ] ) ) ) : array() ; // @codingStandardsIgnoreLine
			$viewed_products = array_filter( array_map( 'absint', $viewed_products ) ) ;

			if ( empty( $viewed_products ) ) {
				return ;
			}

			$this->widget_start( $args, $instance ) ;

			$lottery_product_ids = $this->get_viewed_lottery_products( $args, $instance, $viewed_products ) ;

			if ( $lottery_product_ids->have_posts() ) {

				$template_args = array(
					'widget_id' => $args[ 'widget_id' ],
						) ;

				// WooCommerce class 'product_list_widget'
				echo '<ul class="product_list_widget lty_lottery_product_list">' ;

				while ( $lottery_product_ids->have_posts() ) {
					$lottery_product_ids->the_post() ;
					// WooCommerce Template content-widget-product
					wc_get_template( 'content-widget-product.php', $template_args ) ;
				}

				echo '</ul>' ;

				$this->widget_end( $args ) ;
			}

			wp_reset_postdata() ;
		}

		/**
		 * Get Viewed Giveaway Products.
		 *
		 * */
		public function get_viewed_lottery_products( $args, $instance, $viewed_products ) {

			$limit = ! empty( $instance[ 'lty_limit' ] ) ? absint( $instance[ 'lty_limit' ] ) : $this->settings[ 'lty_limit' ][ 'std' ] ;
			$order = ! empty( $instance[ 'lty_order' ] ) ? strtoupper( $instance[ 'lty_order' ] ) : strtoupper( $this->settings[ 'lty_order' ][ 'std' ] ) ;

			$query_args = array(
				'posts_per_page' => $limit,
				'post_status'    => 'publish',
				'post_type'      => 'product',
				'post__in'       => $viewed_products,
				'order'          => $order,
				'fields'         => 'ids',
					) ;

			/**
			 * This hook is used to alter the recent viewed lottery widget query arguments.
			 * 
			 * @since 1.0
			 */
			return new WP_Query( apply_filters( $this->widget_id . '_widget_query_args', $query_args ) ) ;
		}
	}

}
