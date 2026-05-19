<?php

/**
 * Lottery Product Widget.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Lottery_Products' ) ) {

	/**
	 * Class LTY_Lottery_Products.
	 * */
	class LTY_Lottery_Products extends WC_Widget {

		/**
		 * Class Constructor.
		 * */
		public function __construct() {

			$this->widget_cssclass    = 'woocommerce lty_lottery_products' ;
			$this->widget_description = __( 'Display the Giveaway products based on the selected filter.', 'lottery-for-woocommerce' ) ;
			$this->widget_id          = 'lty_lottery_products' ;
			$this->widget_name        = __( 'Giveaway Products', 'lottery-for-woocommerce' ) ;

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
			 * This hook is used to alter the lottery products widget settings.
			 * 
			 * @since 1.0
			 */
			$this->settings = apply_filters( 'lty_lottery_widget_settings', array(
				'title'            => array(
					'type'  => 'text',
					'std'   => __( 'Giveaway Products', 'lottery-for-woocommerce' ),
					'label' => __( 'Title', 'lottery-for-woocommerce' ),
				),
				'lty_widgets_type' => array(
					'class'   => 'lty_lottery_product_widget_type',
					'type'    => 'select',
					'std'     => 'random',
					'label'   => __( 'Products to display', 'lottery-for-woocommerce' ),
					'options' => array(
						'ending'     => __( 'Ending Soon Giveaway Products', 'lottery-for-woocommerce' ),
						'future'     => __( 'Future Giveaway Products', 'lottery-for-woocommerce' ),
						'my_lottery' => __( 'My Giveaway Products', 'lottery-for-woocommerce' ),
						'featured'   => __( 'Featured Giveaway Products', 'lottery-for-woocommerce' ),
						'random'     => __( 'Random Giveaway Products', 'lottery-for-woocommerce' ),
						'closed'     => __( 'Closed Giveaway Products', 'lottery-for-woocommerce' ),
					),
				),
				'lty_limit'        => array(
					'class' => 'lty_limit',
					'type'  => 'text',
					'std'   => '5',
					'label' => __( 'Product Limit', 'lottery-for-woocommerce' ),
				),
				'lty_order'        => array(
					'class'   => 'lty_order',
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
				$instance[ 'title' ] = __( 'Giveaway Products', 'lottery-for-woocommerce' ) ;
			}

			$this->widget_start( $args, $instance ) ;

			$lottery_product_ids = $this->get_lottery_products( $args, $instance ) ;

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
			}

			$this->widget_end( $args ) ;

			wp_reset_postdata() ;
		}

		/**
		 * Get Giveaway Products.
		 *
		 * */
		public function get_lottery_products( $args, $instance ) {

			$lottery_widget_type = ! empty( $instance[ 'lty_widgets_type' ] ) ? sanitize_title( $instance[ 'lty_widgets_type' ] ) : $this->settings[ 'lty_widgets_type' ][ 'std' ] ;
			$order               = ! empty( $instance[ 'lty_order' ] ) ? sanitize_title( $instance[ 'lty_order' ] ) : $this->settings[ 'lty_order' ][ 'std' ] ;
			$limit               = ! empty( $instance[ 'lty_limit' ] ) ? sanitize_title( $instance[ 'lty_limit' ] ) : $this->settings[ 'lty_limit' ][ 'std' ] ;

			$default_args = array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'order'          => strtoupper( $order ),
				'posts_per_page' => $limit,
				'fields'         => 'ids',
					) ;

			$query_args = array() ;

			switch ( $lottery_widget_type ) {

				case 'ending':
					$query_args                  = array(
						'tax_query'  => array(
							array(
								'taxonomy' => 'product_type',
								'field'    => 'name',
								'terms'    => 'lottery',
							),
						),
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key'     => '_lty_end_date_gmt',
								'value'   => LTY_Date_Time::get_mysql_date_time_format( 'now', true ),
								'compare' => '>=',
							),
							array(
								'key'     => '_lty_lottery_status',
								'value'   => 'lty_lottery_started',
								'compare' => '=',
							),
						),
							) ;
					break ;
				case 'future':
					$query_args                  = array(
						'tax_query'  => array(
							array(
								'taxonomy' => 'product_type',
								'field'    => 'name',
								'terms'    => 'lottery',
							),
						),
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key'     => '_lty_start_date_gmt',
								'value'   => LTY_Date_Time::get_mysql_date_time_format( 'now', true ),
								'compare' => '>=',
							),
							array(
								'key'     => '_lty_lottery_status',
								'value'   => 'lty_lottery_not_started',
								'compare' => '=',
							),
						),
							) ;
					break ;
				case 'my_lottery':
					$query_args                  = array(
						'post__in' => lty_get_my_lotteries(),
							) ;
					break ;
				case 'featured':
					$product_visibility_term_ids = wc_get_product_visibility_term_ids() ;
					$query_args                  = array(
						'tax_query' => array(
							array(
								'taxonomy' => 'product_visibility',
								'field'    => 'term_taxonomy_id',
								'terms'    => $product_visibility_term_ids[ 'featured' ],
							),
							array(
								'taxonomy' => 'product_type',
								'field'    => 'name',
								'terms'    => 'lottery',
							),
					),
							) ;
					break ;
				case 'random':
					$query_args                  = array(
						'tax_query' => array(
							array(
								'taxonomy' => 'product_type',
								'field'    => 'name',
								'terms'    => 'lottery',
							),
						),
						'orderby'   => 'rand',
							) ;
					break ;
				case 'closed':
					$query_args                  = array(
						'tax_query'  => array(
							array(
								'taxonomy' => 'product_type',
								'field'    => 'name',
								'terms'    => 'lottery',
							),
						),
						'meta_query' => array(
							array(
								'key'     => '_lty_lottery_status',
								'value'   => 'lty_lottery_closed',
								'compare' => '=',
							),
						),
							) ;
					break ;
			}

			$query_args = array_merge( $default_args, $query_args ) ;

			/**
			 * This hook is used to alter the lottery products widget query arguments.
			 * 
			 * @since 1.0
			 */
			return new WP_Query( apply_filters( $this->widget_id . '_widget_query_args', $query_args ) ) ;
		}
	}

}
