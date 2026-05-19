<?php

/**
 * Lottery WPML Compatibility.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_WPML_Compatibility' ) ) {

	/**
	 * Class.
	 * */
	class LTY_WPML_Compatibility extends LTY_Compatibility {

		/**
		 * Class Constructor.
		 */
		public function __construct() {

			$this->id = 'wpml' ;

			parent::__construct() ;
		}

		/**
		 * Is plugin enabled?.
		 * 
		 *  @return bool
		 * */
		public function is_plugin_enabled() {

			return function_exists( 'icl_register_string' ) ;
		}

		/**
		 * Admin action.
		 */
		public function admin_action() {

			// Sync lottery product data on saving translation.
			add_action( 'wcml_before_sync_product_data', array( $this, 'sync_lottery_product_data' ), 10 ) ;
			// Sync lottery product data before product update.
			add_action( 'lty_lottery_product_saved', array( $this, 'sync_lottery_product_data' ), 10 ) ;
			// Lottery product id in admin meta box.
			add_filter( 'lty_lottery_product_id_in_meta_box', array( $this, 'get_parent_lottery_product_id' ), 10 ) ;
		}

		/**
		 * Action
		 */
		public function actions() {

			// Update meta in wpml product id.
			add_action( 'lty_update_post_meta', array( $this, 'update_post_meta_in_lottery' ), 10, 3 ) ;
			// Delete meta in wpml product id.
			add_action( 'lty_delete_post_meta', array( $this, 'delete_post_meta_in_lottery' ), 10, 2 ) ;
			// Get lottery product ids in list table.
			add_filter( 'lty_lottery_product_ids_in_list_table', array( $this, 'get_parent_lottery_product_ids' ), 10 ) ;
			// Get lottery product ids in dashboard.
			add_filter( 'lty_lottery_product_ids_in_dashboard', array( $this, 'get_parent_lottery_product_ids' ), 10 ) ;
			// Get parent product id.
			add_filter( 'lty_lottery_product_id', array( $this, 'get_parent_lottery_product_id' ), 10 ) ;
		}

		/**
		 * Sync lottery product data.
		 *
		 */
		public function sync_lottery_product_data( $old_product_id ) {

			global $sitepress ;

			$old_product = wc_get_product( $old_product_id ) ;
			if ( ! lty_is_lottery_product( $old_product ) || ! is_object( $sitepress ) ) {
				return ;
			}

			$translated_lottery_data = $old_product->get_lottery_data() ;
			if ( ! lty_check_is_array( $translated_lottery_data ) ) {
				return ;
			}

			$product_translated_id = $sitepress->get_element_trid( $old_product_id, 'post_product' ) ;
			$product_translations  = $sitepress->get_element_translations( $product_translated_id, 'post_product' ) ;
			if ( ! lty_check_is_array( $product_translations ) ) {
				return ;
			}

			foreach ( $product_translations as $product_translation ) {

				if ( ! is_object( $product_translation ) || ! empty( $product_translation->original ) || $old_product_id == $product_translation->element_id ) {
					continue ;
				}

				foreach ( $translated_lottery_data as $meta_key => $meta_value ) {
					update_post_meta( $product_translation->element_id, '_' . $meta_key, $meta_value ) ;
				}
			}
		}

		/**
		 * Update meta in lottery.
		 *
		 * @return void
		 * */
		public function update_post_meta_in_lottery( $product_id, $key, $value ) {

			global $sitepress ;
			if ( ! is_object( $sitepress ) ) {
				return ;
			}

			$wpml_product_id = $this->get_wpml_product_id( $product_id ) ;
			if ( ! $wpml_product_id ) {
				return ;
			}

			$product_translated_id = $sitepress->get_element_trid( $wpml_product_id, 'post_product' ) ;
			$product_translations  = $sitepress->get_element_translations( $product_translated_id, 'post_product' ) ;
			if ( ! lty_check_is_array( $product_translations ) ) {
				return ;
			}

			foreach ( $product_translations as $product_translation ) {

				if ( ! is_object( $product_translation ) ) {
					continue ;
				}

				update_post_meta( $product_translation->element_id, sanitize_key( '_' . $key ), $value ) ;
			}
		}

		/**
		 * Delete meta in lottery.
		 *
		 * @since 11.2.0
		 * @param int $product_id
		 * @param string $key
		 * */
		public function delete_post_meta_in_lottery( $product_id, $key ) {
			global $sitepress ;
			if ( ! is_object( $sitepress ) ) {
				return ;
			}

			$wpml_product_id = $this->get_wpml_product_id( $product_id ) ;
			if ( ! $wpml_product_id ) {
				return ;
			}

			$product_translated_id = $sitepress->get_element_trid( $wpml_product_id, 'post_product' ) ;
			$product_translations  = $sitepress->get_element_translations( $product_translated_id, 'post_product' ) ;
			if ( ! lty_check_is_array( $product_translations ) ) {
				return ;
			}

			foreach ( $product_translations as $product_translation ) {
				if ( ! is_object( $product_translation ) ) {
					continue ;
				}

				delete_post_meta( $product_translation->element_id, sanitize_key( '_' . $key )) ;
			}
		}

		/**
		 * Get lottery product ids.
		 *
		 * @return void
		 * */
		public function get_parent_lottery_product_ids( $lottery_ids ) {

			global $sitepress ;
			if ( ! is_object( $sitepress ) ) {
				return $lottery_ids ;
			}

			return $this->get_lottery_ids_based_on_wpml_translation( $sitepress->get_default_language(), $lottery_ids ) ;
		}

		/**
		 * Get parent lottery product id.
		 *
		 * @return int
		 * */
		public function get_parent_lottery_product_id( $product_id ) {

			$wpml_product_id = $this->get_wpml_product_id( $product_id ) ;

			return 0 != $wpml_product_id ? $wpml_product_id : $product_id ;
		}

		/**
		 * Get WPML product id.
		 *
		 * @return int
		 * */
		public function get_wpml_product_id( $product_id ) {

			global $sitepress ;

			$wpml_product_id = 0 ;
			// wpml_object_id_filter method from sitepress-multilingual-cms.
			if ( is_object( $sitepress ) && function_exists( 'wpml_object_id_filter' ) && method_exists( $sitepress, 'get_default_language' ) ) {
				$wpml_product_id = wpml_object_id_filter( $product_id, 'product', false, $sitepress->get_default_language() ) ;
			}

			return $wpml_product_id ;
		}

		/**
		 * Get lottery ids based on WPML translation.
		 *
		 * @return array
		 * */
		public function get_lottery_ids_based_on_wpml_translation( $language, $lottery_ids ) {

			global $wpdb ;
			$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' ) ;
			$post_query->select( 'DISTINCT `p`.ID' )
					->leftJoin( $wpdb->prefix . 'icl_translations', 'icl', '`icl`.`element_id` = `p`.`ID`' )
					->where( '`p`.post_type', 'product' )
					->where( '`icl`.element_type', 'post_product' )
					->where( '`p`.post_status', 'publish' )
					->where( 'icl.language_code', $language )
					->whereIn( '`p`.ID', $lottery_ids ) ;

			return $post_query->fetchCol( 'DISTINCT `p`.ID' ) ;
		}
	}

}
