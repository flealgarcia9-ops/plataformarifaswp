<?php

/**
 * Widget Instances Class.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Widget_Instances' ) ) {

	/**
	 * Class LTY_Widget_Instances.
	 * */
	class LTY_Widget_Instances {

		/**
		 * Widgets.
		 * */
		private static $widgets = array() ;

		/**
		 * Get Widgets.
		 * */
		public static function get_widgets() {

			if ( ! self::$widgets ) {
				self::load_widgets() ;
			}

			return self::$widgets ;
		}

		/**
		 * Load all Widgets.
		 * */
		public static function load_widgets() {

			$default_widget_classes = array(
				'lty-lottery-products'        => 'LTY_Lottery_Products',
				'lty-recent-viewed-lottery'   => 'LTY_Recent_Viewed_Lottery',
				'lty-lottery-products-search' => 'LTY_Lottery_Products_Search',
					) ;

			foreach ( $default_widget_classes as $file_name => $widget_class ) {

				// Include widget file.
				include 'class-' . $file_name . '.php' ;

				//Add Widget.
				self::add_widget( $widget_class ) ;
			}

			add_action( 'widgets_init' , array( __CLASS__, 'register_widgets' ) ) ;
		}

		/**
		 * Add a widget class.
		 * */
		public static function register_widgets() {
			$widget_classes = self::get_widget_classes() ;

			if ( ! lty_check_is_array( $widget_classes ) ) {
				return $widget_classes ;
			}

			foreach ( $widget_classes as $widget_class ) {

				// Register a widget. 
				register_widget( $widget_class ) ;
			}
		}

		/**
		 * Add a widget.
		 * */
		public static function add_widget( $class ) {
			self::$widgets[] = $class ;

			return self::$widgets ;
		}

		/**
		 * Get widget classes.
		 * */
		public static function get_widget_classes() {
			return self::$widgets ;
		}
	}

}
