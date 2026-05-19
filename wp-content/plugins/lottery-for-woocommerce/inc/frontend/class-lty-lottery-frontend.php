<?php

/**
 *  Handles the lottery frontend.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'LTY_Lottery_Frontend' ) ) {

	/**
	 * Class.
	 * */
	class LTY_Lottery_Frontend {

		/**
		 * Class Initialization.
		 * */
		public static function init() {

			// Alter shop page query.
			add_action( 'pre_get_posts', array( __CLASS__, 'query_lottery_archive' ), 1, 1 );
			// Alter woocommerce product query.
			add_action( 'pre_get_posts', array( __CLASS__, 'lottery_archive_pre_get_posts' ), 10, 2 );
			// Alter woocommerce product query.
			add_action( 'woocommerce_product_query', array( __CLASS__, 'pre_get_posts' ), 999, 1 );
			// Customize lottery page title.
			add_filter( 'woocommerce_page_title', array( __CLASS__, 'lottery_page_title' ) );
			// Customize lottery page breadcrumb.
			add_filter( 'woocommerce_get_breadcrumb', array( __CLASS__, 'woocommerce_get_breadcrumb' ), 1, 2 );
			// Customize catalog order by.
			add_filter( 'woocommerce_catalog_orderby', array( __CLASS__, 'woocommerce_catalog_orderby' ) );
			// Customize lottery default catalog order by.
			add_filter( 'woocommerce_default_catalog_orderby', array( __CLASS__, 'woocommerce_default_catalog_orderby' ) );
			// Customize nav menu objects.
			add_filter( 'wp_nav_menu_objects', array( __CLASS__, 'nav_menu_item_classes' ), 10 );
			// WooCommerce related products.
			add_filter( 'woocommerce_related_products', array( __CLASS__, 'woocommerce_related_products' ) );
			// WooCommerce Pagination Arguments.
			add_filter( 'woocommerce_pagination_args', array( __CLASS__, 'woocommerce_pagination_args' ), 999, 1 );
			// Handle pdf download.
			add_action( 'wp', array( __CLASS__, 'handle_pdf_download' ) );
		}

		/**
		 * Alter WooCommerce product query.
		 *
		 * @since 1.0.0
		 * @param object $query Query object.
		 * @return mixed
		 */
		public static function pre_get_posts( $query ) {
			// Return if query is not main query.
			if ( ! $query->is_main_query() ) {
				return;
			}

			// Return if admin page.
			if ( is_admin() ) {
				return;
			}

			$orderby = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : false;
			$orderby = ! $orderby && isset( $query->query_vars['is_lottery_archive'] ) ? get_option( 'lty_settings_default_lottery_orderby' ) : $orderby;
			$orderby = ! $orderby && isset( $query->query_vars['is_lottery_entry_list_archive'] ) ? get_option( 'lty_settings_default_entry_list_orderby' ) : $orderby;
			
			switch ( $orderby ) {
				case 'ticket_count':
					$tax_query[] = array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => array( 'lottery' ), // Set product type as term.
						'operator' => 'IN',
					);
					$query->set( 'tax_query', $tax_query );
					$query->set(
						'meta_query',
						array(
							'relation' => 'AND',
							array(
								'key'     => '_lty_ticket_count',
								'value'   => '1',
								'compare' => '>',
							),
							array(
								'key'     => '_lty_lottery_status',
								'value'   => 'lty_lottery_started',
								'compare' => '=',
							),
						)
					);
					$query->set( 'order', 'DESC' );
					break;

				case 'remaining_ticket_count':
					$tax_query[] = array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => array( 'lottery' ), // Set product type as term.
						'operator' => 'IN',
					);
					$query->set( 'tax_query', $tax_query );
					$query->set(
						'meta_query',
						array(
							'key'     => '_lty_lottery_status',
							'value'   => 'lty_lottery_started',
							'compare' => '=',
						)
					);

					$query->set( 'meta_key', '_stock' );
					$query->set( 'meta_type', 'NUMERIC' );
					$query->set( 'orderby', 'meta_value' );
					$query->set( 'order', 'ASC' );
					break;

				case 'remaining_ticket_count-desc':
					$tax_query[] = array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => array( 'lottery' ), // Set product type as term.
						'operator' => 'IN',
					);
					$query->set( 'tax_query', $tax_query );
					$query->set(
						'meta_query',
						array(
							'key'     => '_lty_lottery_status',
							'value'   => 'lty_lottery_started',
							'compare' => '=',
						)
					);

					$query->set( 'meta_key', '_stock' );
					$query->set( 'meta_type', 'NUMERIC' );
					$query->set( 'orderby', 'meta_value' );
					$query->set( 'order', 'DESC' );
					break;

				case 'recently_started':
					$tax_query[] = array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => array( 'lottery' ), // Set product type as term.
						'operator' => 'IN',
					);
					$query->set( 'tax_query', $tax_query );
					$query->set(
						'meta_query',
						array(
							'relation' => 'AND',
							array(
								'key'     => '_lty_start_date_gmt',
								'value'   => LTY_Date_Time::get_mysql_date_time_format( 'now', true ),
								'compare' => '<=',
							),
							array(
								'key'     => '_lty_lottery_status',
								'value'   => 'lty_lottery_started',
								'compare' => '=',
							),
						)
					);
					$query->set( 'order', 'DESC' );
					break;

				case 'ending_soon':
					$tax_query[] = array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => array( 'lottery' ), // Set product type as term.
						'operator' => 'IN',
					);

					$query->set( 'tax_query', $tax_query );
					$query->set(
						'meta_query',
						array(
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
						)
					);

					$query->set( 'meta_key', '_lty_end_date_gmt' );
					$query->set( 'meta_type', 'DATETIME' );
					$query->set( 'orderby', 'meta_value' );
					$query->set( 'order', 'ASC' );
					break;

				case 'closed':
					$tax_query[] = array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => array( 'lottery' ), // Set product type as term.
						'operator' => 'IN',
					);

					$query->set( 'tax_query', $tax_query );
					$query->set(
						'meta_query',
						array(
							array(
								'key'     => '_lty_lottery_status',
								'value'   => 'lty_lottery_closed',
								'compare' => '=',
							),
						)
					);

					$query->set( 'order', 'ASC' );
					break;

				case 'failed':
					$tax_query[] = array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => array( 'lottery' ), // Set product type as term.
						'operator' => 'IN',
					);

					$query->set( 'tax_query', $tax_query );
					$query->set(
						'meta_query',
						array(
							array(
								'key'     => '_lty_lottery_status',
								'value'   => 'lty_lottery_failed',
								'compare' => '=',
							),
						)
					);

					$query->set( 'order', 'ASC' );
					break;

				case 'finished':
					$tax_query[] = array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => array( 'lottery' ), // Set product type as term.
						'operator' => 'IN',
					);

					$query->set( 'tax_query', $tax_query );
					$query->set(
						'meta_query',
						array(
							array(
								'key'     => '_lty_lottery_status',
								'value'   => 'lty_lottery_finished',
								'compare' => '=',
							),
						)
					);

					$query->set( 'order', 'ASC' );
					break;

				case 'on_going':
					$tax_query[] = array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => array( 'lottery' ), // Set product type as term.
						'operator' => 'IN',
					);

					$query->set( 'tax_query', $tax_query );
					$query->set(
						'meta_query',
						array(
							array(
								'key'     => '_lty_lottery_status',
								'value'   => 'lty_lottery_started',
								'compare' => '=',
							),
						)
					);
					break;

				case 'future':
					$tax_query[] = array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => array( 'lottery' ), // Set product type as term.
						'operator' => 'IN',
					);

					$query->set( 'tax_query', $tax_query );
					$query->set(
						'meta_query',
						array(
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
						)
					);
					break;
			}

			// Alter Giveaway Products for Search Widget.
			if ( isset( $_REQUEST['s'], $_REQUEST['lty_product_search'] ) ) {
				self::alter_lottery_products_search( $query );
			}

			// Hide lottery products based on statuses.
			self::hide_lottery_products_based_on_statuses( $query );

			// Return if lottery archive.
			if ( isset( $query->query_vars['is_lottery_archive'] ) || isset( $query->query_vars['is_lottery_entry_list_archive'] ) ) {
				return;
			}

			// Return if lottery hide in category page.
			if ( 'yes' !== get_option( 'lty_settings_restrict_lottery_in_category_page' ) && is_product_category() ) {
				return;
			}

			// Return if lottery hide in tag page.
			if ( 'yes' !== get_option( 'lty_settings_restrict_lottery_in_tag_page' ) && is_product_tag() ) {
				return;
			}

			// Return if lottery hide in shop page.
			if ( 'yes' !== get_option( 'lty_settings_restrict_lottery_in_shop_page' ) && ! is_product_tag() && ! is_product_category() ) {
				return;
			}

			$tax_query = $query->get( 'tax_query' );
			$tax_query = ( ! lty_check_is_array( $tax_query ) ) ? array() : $tax_query;

			$tax_query[] = array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => array( 'lottery' ), // Set product type as term.
				'operator' => 'NOT IN',
			);

			$query->set( 'tax_query', $tax_query );
		}

		/**
		 * Hide lottery products based on statuses.
		 *
		 * @param WP_Query $query Query instance.
		 * @return void.
		 */
		public static function hide_lottery_products_based_on_statuses( $query ) {
			// Return if lottery entry list archive.
			if ( isset( $query->query_vars['is_lottery_entry_list_archive'] ) ) {
				return;
			}

			$lottery_statuses = array();
			if ( 'yes' === get_option( 'lty_settings_hide_lottery_finished_status_products' ) ) {
				$lottery_statuses[] = 'lty_lottery_finished';
			}

			if ( 'yes' === get_option( 'lty_settings_hide_lottery_failed_status_products' ) ) {
				$lottery_statuses[] = 'lty_lottery_failed';
			}

			if ( 'yes' === get_option( 'lty_settings_hide_lottery_closed_status_products' ) ) {
				$lottery_statuses[] = 'lty_lottery_closed';
			}

			if ( ! lty_check_is_array( $lottery_statuses ) ) {
				return;
			}

			$args = array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'tax_query'      => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'name',
						'terms'    => 'lottery',
					),
				),
				'meta_query'     => array(
					array(
						'key'     => '_lty_lottery_status',
						'value'   => $lottery_statuses,
						'compare' => 'IN',
					),
				),
			);

			$query->set( 'post__not_in', get_posts( $args ) );
		}

		/**
		 * Alter Giveaway Product Search Widget.
		 *
		 * @param WP_Query $query Query instance.
		 */
		public static function alter_lottery_products_search( $query ) {
			$tax_query = $query->get( 'tax_query' );
			$tax_query = ( ! lty_check_is_array( $tax_query ) ) ? array() : $tax_query;

			$tax_query[] = array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => array( 'lottery' ), // Set product type as term.
				'operator' => 'IN',
			);

			$query->set( 'tax_query', $tax_query );
		}

		/**
		 * Are we currently on the front page?
		 *
		 * @param WP_Query $q Query instance.
		 * @return bool
		 */
		private static function is_showing_page_on_front( $q ) {
			return $q->is_home() && 'page' === get_option( 'show_on_front' );
		}

		/**
		 * Is the front page a page we define?
		 *
		 * @param int $page_id Page ID.
		 * @return bool
		 */
		private static function page_on_front_is( $page_id ) {
			return absint( get_option( 'page_on_front' ) ) === absint( $page_id );
		}

		/**
		 * Is a one of the lottery page?.
		 *
		 * @since 9.0.0
		 * @param $page_id
		 * @return boolean
		 */
		private static function is_lottery_page( $page_id ) {
			return ( wc_get_page_id( 'lty_lottery_entry_list' ) === $page_id ) || ( wc_get_page_id( 'lty_lottery' ) === $page_id );
		}

		/**
		 * Get the current lottery page.
		 *
		 * @since 9.0.0
		 * @param $page_id
		 * @return string
		 */
		private static function get_current_lottery_archive_query( $page_id ) {
			return wc_get_page_id( 'lty_lottery_entry_list' ) === $page_id ? 'is_lottery_entry_list_archive' : 'is_lottery_archive';
		}

		/**
		 * Get the current lottery page ID.
		 *
		 * @since 9.0.0
		 * @return string
		 */
		private static function get_current_lottery_page_id() {
			$page_id = '';
			if ( get_query_var( 'is_lottery_archive', false ) ) {
				$page_id = absint( wc_get_page_id( 'lty_lottery' ) );
			} elseif ( get_query_var( 'is_lottery_entry_list_archive', false ) ) {
				$page_id = absint( wc_get_page_id( 'lty_lottery_entry_list' ) );
			}

			return $page_id;
		}

		/**
		 * Hook into pre_get_posts to do the main product query.
		 *
		 * @param WP_Query $q Query instance.
		 */
		public static function query_lottery_archive( $q ) {
			// We only want to affect the main query.
			if ( ! $q->is_main_query() ) {
				return;
			}

			// Fixes for queries on static homepages.
			if ( self::is_showing_page_on_front( $q ) && is_object( $q->queried_object ) && ( 'publish' === $q->queried_object->post_status || current_user_can( 'read_post', $q->queried_object ) ) ) {
				// Fix for endpoints on the homepage.
				if ( self::page_on_front_is( wc_get_page_id( 'lty_lottery' ) ) || self::page_on_front_is( wc_get_page_id( 'lty_lottery_entry_list' ) ) ) {
					$_query = wp_parse_args( $q->query );
					if ( empty( $_query ) || ! array_diff( array_keys( $_query ), array( 'preview', 'page', 'paged', 'cpage', 'orderby' ) ) ) {
						$q->set( 'page_id', (int) get_option( 'page_on_front' ) );
						$q->is_page = true;
						$q->is_home = false;

						// WP supporting themes show post type archive.
						if ( current_theme_supports( 'woocommerce' ) ) {
							$q->set( 'post_type', 'product' );
						} else {
							$q->is_singular = true;
						}
					}
				} elseif ( ! empty( $_GET['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
					$q->set( 'page_id', (int) get_option( 'page_on_front' ) );
					$q->is_page     = true;
					$q->is_home     = false;
					$q->is_singular = true;
				}
			}

			// Fix product feeds.
			if ( $q->is_feed() && $q->is_post_type_archive( 'product' ) ) {
				$q->is_comment_feed = false;
			}

			if ( is_object( $q->queried_object ) && isset( $q->queried_object->ID ) && self::is_lottery_page( $q->queried_object->ID ) && ( 'publish' === $q->queried_object->post_status || current_user_can( 'read_post', $q->queried_object ) ) ) {
				$q->set( 'post_type', 'product' );
				$q->set( 'page', '' );
				$q->set( 'pagename', '' );
				$q->set( self::get_current_lottery_archive_query( $q->queried_object->ID ), true );

				// Fix conditional Functions.
				$q->is_archive           = true;
				$q->is_post_type_archive = true;
				$q->is_singular          = false;
				$q->is_page              = false;
			}

			// Special check for shops with the PRODUCT POST TYPE ARCHIVE on front.
			if ( $q->is_page() && 'page' === get_option( 'show_on_front' ) && self::is_lottery_page( absint( $q->get( 'page_id' ) ) ) && is_object( $q->queried_object ) && ( 'publish' === $q->queried_object->post_status || current_user_can( 'read_post', $q->queried_object ) ) ) {
				// This is a front-page lottery.
				$q->set( 'post_type', 'product' );
				$q->set( 'page_id', '' );
				$q->set( 'page_name', '' );
				$q->set( self::get_current_lottery_archive_query( $q->queried_object->ID ), true );

				if ( isset( $q->query['paged'] ) ) {
					$q->set( 'paged', $q->query['paged'] );
				}

				// Define a variable so we know this is the front page shop later on.
				wc_maybe_define_constant( 'LTY_LOTTERY_IS_ON_FRONT', true );
				// Get the actual WP page to avoid errors and let us use is_front_page().
				// This is hacky but works. Awaiting https://core.trac.wordpress.org/ticket/21096.
				global $wp_post_types;

				$shop_page = get_post( $q->queried_object->ID );

				$wp_post_types['product']->ID         = $shop_page->ID;
				$wp_post_types['product']->post_title = $shop_page->post_title;
				$wp_post_types['product']->post_name  = $shop_page->post_name;
				$wp_post_types['product']->post_type  = $shop_page->post_type;
				$wp_post_types['product']->ancestors  = get_ancestors( $shop_page->ID, $shop_page->post_type );

				// Fix conditional Functions like is_front_page.
				$q->is_singular          = false;
				$q->is_post_type_archive = true;
				$q->is_archive           = true;
				$q->is_page              = true;

				// Remove post type archive name from front page title tag.
				add_filter( 'post_type_archive_title', '__return_empty_string', 5 );

				// Fix WP SEO.
				if ( class_exists( 'WPSEO_Meta' ) ) {
					add_filter( 'wpseo_metadesc', array( __CLASS__, 'wpseo_metadesc' ) );
					add_filter( 'wpseo_metakey', array( __CLASS__, 'wpseo_metakey' ) );
					add_filter( 'wpseo_title', array( __CLASS__, 'wpseo_title' ) );
				}
			} elseif ( ! $q->is_post_type_archive( 'product' ) && ! $q->is_tax( get_object_taxonomies( 'product' ) ) ) {
				// Only apply to product categories, the product post archive, the shop page, product tags, and product attribute taxonomies.
				return;
			}
		}

		/**
		 * WP SEO meta description.
		 *
		 * @return string
		 */
		public static function wpseo_metadesc() {
			return WPSEO_Meta::get_value( 'metadesc', self::get_current_lottery_page_id() );
		}

		/**
		 * WP SEO meta key.
		 *
		 * @return string
		 */
		public static function wpseo_metakey() {
			return WPSEO_Meta::get_value( 'metakey', self::get_current_lottery_page_id() );
		}

		/**
		 * WP SEO title.
		 *
		 * @return string
		 */
		public static function wpseo_title() {
			return WPSEO_Meta::get_value( 'title', self::get_current_lottery_page_id() );
		}

		/**
		 * Hook into pre_get_posts to do the main product query.
		 *
		 * @param WP_Query $q Query instance.
		 */
		public static function lottery_archive_pre_get_posts( $query ) {
			// Return if query is not main query.
			if ( ! $query->is_main_query() ) {
				return;
			}

			// Return if admin page.
			if ( is_admin() ) {
				return;
			}

			// Return if lottery is not archive.
			if ( ! isset( $query->query_vars['is_lottery_archive'] ) && ! isset( $query->query_vars['is_lottery_entry_list_archive'] ) ) {
				return;
			}

			$tax_query = $query->get( 'tax_query' );
			$tax_query = ( ! lty_check_is_array( $tax_query ) ) ? array() : $tax_query;

			$tax_query[] = array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => array( 'lottery' ), // Set product type as term.
				'operator' => 'IN',
			);

			$query->set( 'tax_query', $tax_query );

			if ( isset( $query->query_vars['is_lottery_entry_list_archive'] ) ) {
				$meta_query = ( ! lty_check_is_array( $query->get( 'meta_query' ) ) ) ? array() : $query->get( 'meta_query' );
				$query->set( 'meta_query', $meta_query );
			}
		}

		/**
		 * Alter the lottery base page title.
		 *
		 * @return string
		 * */
		public static function lottery_page_title( $title ) {
			if ( get_query_var( 'is_lottery_archive', false ) ) {
				return get_the_title( wc_get_page_id( 'lty_lottery' ) );
			} elseif ( get_query_var( 'is_lottery_entry_list_archive', false ) ) {
				return get_the_title( wc_get_page_id( 'lty_lottery_entry_list' ) );
			}

			return $title;
		}

		/**
		 * Fix for lottery base page breadcrumbs.
		 *
		 * @return string
		 * */
		public static function woocommerce_get_breadcrumb( $crumbs, $WC_Breadcrumb ) {
			// Lottery page.
			if ( get_query_var( 'is_lottery_archive', false ) ) {
				$page_id   = absint( wc_get_page_id( 'lty_lottery' ) );
				$crumbs[1] = array( get_the_title( $page_id ), get_permalink( $page_id ) );

				// Entry list single product page.
			} elseif ( get_query_var( 'lottery_single_entry_list' ) ) {
				global $product;

				$page_id   = absint( wc_get_page_id( 'lty_lottery_entry_list' ) );
				$crumbs[1] = array( get_the_title( $page_id ), get_permalink( $page_id ) );
				$post      = get_page_by_path( get_query_var( 'lottery_single_entry_list', false ), OBJECT, 'product' );

				$post_id = is_object( $post ) ? $post->ID : false;
				$product = is_object( $product ) && ( $post_id === $product->get_id() ) ? $product : wc_get_product( $post_id );
				if ( lty_is_lottery_product( $product ) ) {
					$crumbs[2] = array( $product->get_name(), $product->get_permalink() );
				}

				// Entry list page.
			} elseif ( get_query_var( 'is_lottery_entry_list_archive', false ) ) {

				$page_id   = absint( wc_get_page_id( 'lty_lottery_entry_list' ) );
				$crumbs[1] = array( get_the_title( $page_id ), get_permalink( $page_id ) );
			}

			return $crumbs;
		}

		/**
		 * Add ordering for lottery.
		 *
		 * @return array
		 * */
		public static function woocommerce_catalog_orderby( $catalog_orderby_options ) {
			$is_lottery_archive = get_query_var( 'is_lottery_archive', false ) || get_query_var( 'is_lottery_entry_list_archive', false );

			// Return if lottery hide in category page.
			if ( 'yes' === get_option( 'lty_settings_restrict_lottery_in_category_page' ) && is_product_category() && ! $is_lottery_archive ) {
				return $catalog_orderby_options;
			}

			// Return if lottery hide in tag page.
			if ( 'yes' === get_option( 'lty_settings_restrict_lottery_in_tag_page' ) && is_product_tag() && ! $is_lottery_archive ) {
				return $catalog_orderby_options;
			}

			// Return if lottery hide in shop page.
			if ( 'yes' === get_option( 'lty_settings_restrict_lottery_in_shop_page' ) && is_shop() && ! $is_lottery_archive ) {
				return $catalog_orderby_options;
			}

			$orderby_options = get_query_var( 'is_lottery_entry_list_archive', false ) ? lty_get_entry_list_sorting_options() : lty_get_lottery_sorting_options();

			return array_merge( $catalog_orderby_options, $orderby_options );
		}

		/**
		 * Customize the default lottery catalog order by.
		 *
		 * @return string
		 * */
		public static function woocommerce_default_catalog_orderby( $order_by ) {
			if ( ! get_query_var( 'is_lottery_archive', false ) && ! get_query_var( 'is_lottery_entry_list_archive', false ) ) {
				return $order_by;
			}

			if ( get_query_var( 'is_lottery_entry_list_archive', false ) ) {
				$entry_list_orderby = get_option( 'lty_settings_default_entry_list_orderby', 'on_going' );
				if ( $entry_list_orderby ) {
					return $entry_list_orderby;
				}
			} else {
				$lottery_orderby = get_option( 'lty_settings_default_lottery_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
				if ( $lottery_orderby ) {
					return $lottery_orderby;
				}
			}

			return $order_by;
		}

		/**
		 * Fix active class in nav for lottery page.
		 *
		 * @return array
		 * */
		public static function nav_menu_item_classes( $menu_items ) {
			$page_id = self::get_current_lottery_page_id();
			if ( ! $page_id ) {
				return $menu_items;
			}

			foreach ( (array) $menu_items as $key => $menu_item ) {

				$classes = (array) $menu_item->classes;

				// Unset active class for blog page.
				$menu_items[ $key ]->current = false;

				if ( in_array( 'current_page_parent', $classes ) ) {
					unset( $classes[ array_search( 'current_page_parent', $classes ) ] );
				}

				if ( in_array( 'current-menu-item', $classes ) ) {
					unset( $classes[ array_search( 'current-menu-item', $classes ) ] );
				}

				if ( in_array( 'current_page_item', $classes ) ) {
					unset( $classes[ array_search( 'current_page_item', $classes ) ] );
				}

				// Set active state if this is the shop page link.
				if ( $page_id == $menu_item->object_id && 'page' === $menu_item->object ) {
					$menu_items[ $key ]->current = true;
					$classes[]                   = 'current-menu-item';
					$classes[]                   = 'current_page_item';
				}

				$menu_items[ $key ]->classes = array_unique( $classes );
			}

			return $menu_items;
		}

		/**
		 * WooCommerce related products.
		 *
		 * @return array
		 * */
		public static function woocommerce_related_products( $related_product_ids ) {

			global $product;
			if ( ! is_object( $product ) || 'lottery' !== $product->get_type() ) {
				return $related_product_ids;
			}

			$lottery_product_ids = get_posts(
				array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => '-1',
					'fields'         => 'ids',
					'post__not_in'   => array( $product->get_id() ),
					'tax_query'      => array(
						array(
							'taxonomy' => 'product_type',
							'field'    => 'slug',
							'terms'    => array( 'lottery' ), // Set product type as term.
							'operator' => 'IN',
						),
					),
					'meta_query'     => array(
						array(
							'key'     => '_lty_lottery_status',
							'value'   => 'lty_lottery_started',
							'compare' => '!=',
						),
					),
				)
			);

			// Return related products with started lotteries.
			return array_diff( $related_product_ids, $lottery_product_ids );
		}

		/**
		 * WooCommerce pagination arguments.
		 *
		 * @return array
		 * */
		public static function woocommerce_pagination_args( $args ) {
			$shortcode_name = wc_get_loop_prop( 'shortcode_name' );
			if ( ! $shortcode_name ) {
				return $args;
			}

			$args['add_args'] = array( 'lty_lottery_shortcode' => $shortcode_name );

			return $args;
		}

		/**
		 * Handle the pdf download.
		 *
		 * @since 9.5.0
		 * @return void
		 */
		public static function handle_pdf_download() {
			if ( ! isset( $_GET['action'] ) ) {
				return;
			}

			if ( 'lty-download' !== wc_clean( wp_unslash( $_GET['action'] ) ) ) {
				return;
			}

			// Handle lottery entry list PDF download.
			self::handle_lottery_entry_list_pdf_download();
			// Handle lottery tickets PDF download.
			self::handle_lottery_ticket_pdf_download();
		}

		/**
		 * Handle lottery entry lists pdf download.
		 *
		 * @since 9.5.0
		 * @return void
		 */
		public static function handle_lottery_entry_list_pdf_download() {
			if ( ! isset( $_GET['lty_key'] ) || ! isset( $_GET['lty_pdf_nonce'] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( wc_clean( wp_unslash( $_GET['lty_pdf_nonce'] ) ), 'lty-lottery-entry-list-pdf' ) ) {
				return;
			}

			$args = lty_decode( wc_clean( wp_unslash( $_GET['lty_key'] ) ), true );
			if ( ! isset( $args->lty_lottery_id ) ) {
				return;
			}

			$product = wc_get_product( intval( $args->lty_lottery_id ) );
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			LTY_Generate_PDF_Handler::download_lottery_entry_list( $product );
		}

		/**
		 * Handle lottery tickets pdf download.
		 *
		 * @since 9.5.0
		 * @return void
		 */
		public static function handle_lottery_ticket_pdf_download() {
			if ( ! isset( $_GET['lty_key'] ) ) {
				return;
			}

			$args = lty_decode( wc_clean( wp_unslash( $_GET['lty_key'] ) ), true );
			if ( ! isset( $args->lty_order_id ) ) {
				return;
			}

			$order = wc_get_order( intval( $args->lty_order_id ) );
			if ( ! is_object( $order ) ) {
				return;
			}

			$ticket_ids = array();
			if ( isset( $args->lty_lottery_id ) ) {
				$product    = wc_get_product( intval( $args->lty_lottery_id ) );
				$ticket_ids = lty_is_lottery_product( $product ) ? lty_get_lottery_ticket_ids_by_order_id( $order->get_id(), $product->get_id() ) : array();
			} else {
				$ticket_ids = $order->get_meta( 'lty_ticket_ids_in_order' );
			}

			if ( ! lty_check_is_array( $ticket_ids ) ) {
				return;
			}

			LTY_Generate_PDF_Handler::download_lottery_ticket( $ticket_ids, $order->get_id() );
		}
	}

	LTY_Lottery_Frontend::init();
}
