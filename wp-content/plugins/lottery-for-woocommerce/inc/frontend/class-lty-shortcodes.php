<?php

/**
 * Shortcodes.
 *
 * @since 1.0.0
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Shortcodes' ) ) {

	/**
	 * Class.
	 *
	 * @since 1.0.0
	 */
	class LTY_Shortcodes {

		/**
		 * Shortcodes.
		 *
		 * @since 11.8.0
		 * @var array
		 */
		protected static $shortcodes = array(
			'lty_dashboard',
			'lty_lottery_products_winners_list',
			'lty_lottery_winners_by_date',
			'lty_lottery_instant_winners_by_date',
			'lty_my_lottery_products',
			'lty_all_lottery_products',
			'lty_ongoing_lottery_products',
			'lty_ending_soon_lottery_products',
			'lty_future_lottery_products',
			'lty_featured_lottery_products',
			'lty_closed_lottery_products',
			'lty_finished_lottery_products',
			'lty_random_lottery_products',
		);

		/**
		 * Class.
		 *
		 * @since 1.0.0
		 * */
		public static function init() {
			foreach ( self::$shortcodes as $shortcode_name ) {
				$callback_method = 'process_' . str_replace( 'lty_', '', $shortcode_name ) . '_shortcode';
				// Add a shortcode.
				add_shortcode( $shortcode_name, array( __CLASS__, $callback_method ) );
			}
		}

		/**
		 * Process dashboard shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 */
		public static function process_dashboard_shortcode( $atts, $content ) {
			ob_start();
			if ( ! is_user_logged_in() ) {
				echo wp_kses_post( lty_get_guest_message() );
			} else {
				LTY_Dashboard::output();
			}

			$content = ob_get_contents();
			ob_end_clean();

			return $content;
		}

		/**
		 * Process lottery products winners list shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_lottery_products_winners_list_shortcode( $atts, $content ) {
			if ( '2' === get_option( 'lty_settings_single_product_lottery_winner_toggle' ) ) {
				return;
			}

			$winner_ids = lty_get_lottery_winner_ids();
			if ( ! lty_check_is_array( $winner_ids ) ) {
				return;
			}

			$post_per_page = get_option( 'lty_settings_winners_list_per_page', 10 );
			$current_page  = isset( $_REQUEST['page_no'] ) ? wc_clean( wp_unslash( absint( $_REQUEST['page_no'] ) ) ) : '1';
			$offset        = ( $post_per_page * $current_page ) - $post_per_page;
			$page_count    = ceil( count( $winner_ids ) / $post_per_page );

			return lty_get_template_html(
				'lottery-product-winners-list-layout.php',
				array(
					'winner_ids' => array_slice( $winner_ids, $offset, $post_per_page ),
					'offset'     => $offset,
					'columns'    => lty_get_lottery_shortcode_winner_table_header(),
					'pagination' => lty_prepare_pagination_arguments( $current_page, $page_count ),
				)
			);
		}

		/**
		 * Process the lottery winners by date shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_lottery_winners_by_date_shortcode( $atts, $content ) {
			$order                 = isset( $atts['order'] ) ? $atts['order'] : 'DESC';
			$date_filter_number    = isset( $atts['date_filter_number'] ) ? $atts['date_filter_number'] : '';
			$date_filter_unit      = isset( $atts['date_filter_unit'] ) ? $atts['date_filter_unit'] : '';
			$start_date            = lty_prepare_winning_dates_start_date( $date_filter_number, $date_filter_unit );
			$lottery_winning_dates = lty_get_lottery_winning_dates( $order, $start_date );
			if ( ! lty_check_is_array( $lottery_winning_dates ) ) {
				return;
			}

			$current_page = 1;
			$per_page     = isset( $atts['posts_per_page'] ) ? intval( $atts['posts_per_page'] ) : 99999;
			$paginate     = isset( $atts['paginate'] ) ? $atts['paginate'] : false;
			$offset       = ( $paginate ) ? ( $per_page * $current_page ) - $per_page : 0;
			$page_count   = ceil( count( $lottery_winning_dates ) / $per_page );

			$table_args = array(
				'lottery_winning_dates' => array_slice( $lottery_winning_dates, $offset, $per_page ),
				'paginate'              => $paginate,
				'per_page'              => $per_page,
				'order'                 => $order,
				'date_filter_number'    => $date_filter_number,
				'date_filter_unit'      => $date_filter_unit,
				'pagination'            => lty_prepare_pagination_arguments( $current_page, $page_count ),
			);

			return lty_get_template_html( 'shortcodes/lottery-winners-by-date-layout.php', $table_args );
		}

		/**
		 * Process the lottery instant winners by date shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_lottery_instant_winners_by_date_shortcode( $atts, $content ) {
			$order                         = isset( $atts['order'] ) ? $atts['order'] : 'DESC';
			$date_filter_number            = isset( $atts['date_filter_number'] ) ? $atts['date_filter_number'] : '';
			$date_filter_unit              = isset( $atts['date_filter_unit'] ) ? $atts['date_filter_unit'] : '';
			$start_date                    = lty_prepare_winning_dates_start_date( $date_filter_number, $date_filter_unit );
			$lottery_instant_winning_dates = lty_get_lottery_instant_winning_dates( $order, $start_date );
			if ( ! lty_check_is_array( $lottery_instant_winning_dates ) ) {
				return;
			}

			$current_page = 1;
			$per_page     = isset( $atts['posts_per_page'] ) ? intval( $atts['posts_per_page'] ) : 99999;
			$paginate     = isset( $atts['paginate'] ) ? $atts['paginate'] : false;
			$offset       = ( $paginate ) ? ( $per_page * $current_page ) - $per_page : 0;
			$page_count   = ceil( count( $lottery_instant_winning_dates ) / $per_page );

			$table_args = array(
				'lottery_instant_winning_dates' => array_slice( $lottery_instant_winning_dates, $offset, $per_page ),
				'paginate'                      => $paginate,
				'per_page'                      => $per_page,
				'order'                         => $order,
				'date_filter_number'            => $date_filter_number,
				'date_filter_unit'              => $date_filter_unit,
				'pagination'                    => lty_prepare_pagination_arguments( $current_page, $page_count ),
			);

			return lty_get_template_html( 'shortcodes/lottery-instant-winners-by-date-layout.php', $table_args );
		}

		/**
		 * Process my lottery products shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_my_lottery_products_shortcode( $atts, $content ) {
			if ( ! is_user_logged_in() ) {
				return lty_get_guest_message();
			}

			$attributes             = array_filter( self::shortcode_attributes( $atts ) );
			$args                   = array_merge( $attributes, array( 'post__in' => lty_get_my_lotteries() ) );
			$args['shortcode_name'] = 'lty_my_lottery';

			return self::render_lottery_products( $args );
		}

		/**
		 * Process all lottery products shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_all_lottery_products_shortcode( $atts, $content ) {
			$attributes = array_filter( self::shortcode_attributes( $atts ) );
			$args       = array(
				'tax_query' => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => array( 'lottery' ), // Set product type as term.
						'operator' => 'IN',
					),
				),
			);

			$args                   = array_merge( $attributes, $args );
			$args['shortcode_name'] = 'lty_all_lottery';

			return self::render_lottery_products( $args );
		}

		/**
		 * Process ongoing lottery products shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_ongoing_lottery_products_shortcode( $atts, $content ) {
			$attributes = array_filter( self::shortcode_attributes( $atts ) );
			$args       = array(
				'tax_query'  => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => array( 'lottery' ), // Set product type as term.
						'operator' => 'IN',
					),
				),
				'meta_query' => array(
					array(
						'key'     => '_lty_lottery_status',
						'value'   => 'lty_lottery_started',
						'compare' => '=',
					),
				),
			);

			$args                   = array_merge( $attributes, $args );
			$args['shortcode_name'] = 'lty_ongoing_lottery';

			return self::render_lottery_products( $args );
		}

		/**
		 * Process ending soon lottery products shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_ending_soon_lottery_products_shortcode( $atts, $content ) {
			$attributes = array_filter( self::shortcode_attributes( $atts ) );
			$args       = array(
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
				'meta_key'   => '_lty_end_date_gmt',
				'meta_type'  => 'DATETIME',
				'orderby'    => 'meta_value',
				'order'      => 'ASC',
			);

			$args                   = array_merge( $attributes, $args );
			$args['shortcode_name'] = 'lty_ending_soon_lottery';

			return self::render_lottery_products( $args );
		}

		/**
		 * Process future lottery products shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_future_lottery_products_shortcode( $atts, $content ) {
			$attributes = array_filter( self::shortcode_attributes( $atts ) );
			$args       = array(
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
			);

			$args                   = array_merge( $attributes, $args );
			$args['shortcode_name'] = 'lty_future_lottery';

			return self::render_lottery_products( $args );
		}

		/**
		 * Process featured lottery products shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_featured_lottery_products_shortcode( $atts, $content ) {
			$attributes                  = array_filter( self::shortcode_attributes( $atts ) );
			$product_visibility_term_ids = wc_get_product_visibility_term_ids();
			$args                        = array(
				'tax_query' => array(
					array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['featured'],
					),
					array(
						'taxonomy' => 'product_type',
						'field'    => 'name',
						'terms'    => 'lottery',
					),
				),
			);

			$args                   = array_merge( $attributes, $args );
			$args['shortcode_name'] = 'lty_featured_lottery';

			return self::render_lottery_products( $args );
		}

		/**
		 * Process closed lottery products shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_closed_lottery_products_shortcode( $atts, $content ) {
			$attributes = array_filter( self::shortcode_attributes( $atts ) );
			$args       = array(
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
			);

			$args                   = array_merge( $attributes, $args );
			$args['shortcode_name'] = 'lty_closed_lottery';

			return self::render_lottery_products( $args );
		}

		/**
		 * Process finished lottery products shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_finished_lottery_products_shortcode( $atts, $content ) {
			$attributes = array_filter( self::shortcode_attributes( $atts ) );
			$args       = array(
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
						'value'   => 'lty_lottery_finished',
						'compare' => '=',
					),
				),
			);

			$args                   = array_merge( $attributes, $args );
			$args['shortcode_name'] = 'lty_finished_lottery';

			return self::render_lottery_products( $args );
		}

		/**
		 * Process random lottery products shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_random_lottery_products_shortcode( $atts, $content ) {
			$attributes = array_filter( self::shortcode_attributes( $atts ) );
			$args       = array(
				'tax_query' => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'name',
						'terms'    => 'lottery',
					),
				),
				'orderby'   => 'rand',
			);

			$args                   = array_merge( $attributes, $args );
			$args['shortcode_name'] = 'lty_random_lottery';

			return self::render_lottery_products( $args );
		}

		/**
		 * Get the shortcode attributes.
		 *
		 * @since 1.0.0
		 * @param array $atts Shortcode Attributes.
		 * @return array
		 */
		public static function shortcode_attributes( $atts ) {
			$atts = array_filter(
				shortcode_atts(
					array(
						'posts_per_page' => '',
						'order'          => '',
						'orderby'        => '',
						'paginate'       => '',
						'category'       => '',
					),
					$atts,
					'lty_lottery_products'
				)
			);

			$attributes = array(
				'posts_per_page' => isset( $atts['posts_per_page'] ) ? $atts['posts_per_page'] : '-1',
				'order'          => isset( $atts['order'] ) ? $atts['order'] : 'DESC',
				'orderby'        => isset( $atts['orderby'] ) ? $atts['orderby'] : 'date',
				'paginate'       => isset( $atts['paginate'] ) ? $atts['paginate'] : false,
				'category'       => isset( $atts['category'] ) ? $atts['category'] : '',
			);

			switch ( $attributes['orderby'] ) {
				case 'start_date':
					$attributes['meta_key'] = '_lty_start_date_gmt';
					$attributes['orderby']  = 'meta_value';
					break;
				case 'end_date':
					$attributes['meta_key'] = '_lty_end_date_gmt';
					$attributes['orderby']  = 'meta_value';
					break;
				case 'finished_date':
					$attributes['meta_key'] = '_lty_finished_date_gmt';
					$attributes['orderby']  = 'meta_value';
					break;
				case 'closed_date':
					$attributes['meta_key'] = '_lty_closed_date_gmt';
					$attributes['orderby']  = 'meta_value';
					break;
				case 'failed_date':
					$attributes['meta_key'] = '_lty_failed_date_gmt';
					$attributes['orderby']  = 'meta_value';
					break;
				case 'remaining_ticket_count':
					$attributes['meta_key']  = '_stock';
					$attributes['orderby']   = 'meta_value';
					$attributes['meta_type'] = 'NUMERIC';
					break;
			}

			return $attributes; // nosem
		}

		/**
		 * Render lottery products.
		 *
		 * @since 1.0.0
		 * @param array $args Query arguments.
		 * @return string|HTML
		 */
		public static function render_lottery_products( $args = array() ) {
			$object = new LTY_Shortcode_Products( $args );

			return $object->get_content();
		}
	}

	LTY_Shortcodes::init();
}
