<?php

/**
 * Giveaway Products Search Widget.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Lottery_Products_Search' ) ) {

	/**
	 * Class LTY_Lottery_Products_Search.
	 * */
	class LTY_Lottery_Products_Search extends WC_Widget {

		/**
		 * Class Constructor.
		 * */
		public function __construct() {

			$this->widget_cssclass    = 'lty_lottery_products_search' ;
			$this->widget_description = __( 'Displays the Giveaway Product Search form.', 'lottery-for-woocommerce' ) ;
			$this->widget_id          = 'lty_lottery_products_search' ;
			$this->widget_name        = __( 'Giveaway Product Search', 'lottery-for-woocommerce' ) ;

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
			 * This hook is used to alter the lottery product search widget settings.
			 * 
			 * @since 1.0
			 */
			$this->settings = apply_filters( 'LTY_lottery_product_search_widget_settings', array(
				'title' => array(
					'type'  => 'text',
					'std'   => __( 'Giveaway Products Search', 'lottery-for-woocommerce' ),
					'label' => __( 'Title', 'lottery-for-woocommerce' ),
				),
					) ) ;
		}

		/**
		 * Output widget.
		 *
		 * */
		public function widget( $args, $instance ) {

			if ( ! isset( $instance[ 'title' ] ) ) {
				$instance[ 'title' ] = __( 'giveaway Products Search', 'lottery-for-woocommerce' ) ;
			}

			$this->widget_start( $args, $instance ) ;

			lty_get_template( 'widgets/lottery-products-search.php' ) ;

			$this->widget_end( $args ) ;
		}
	}

}
