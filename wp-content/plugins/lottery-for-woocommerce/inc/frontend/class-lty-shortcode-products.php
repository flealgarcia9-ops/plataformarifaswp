<?php

/**
 * Shortcode Products.
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
if (!class_exists('LTY_Shortcode_Products')) {

	/**
	 * Class.
	 */
	class LTY_Shortcode_Products {

		/**
		 * Post type.
		 * */
		protected $post_type;

		/**
		 * Default arguments.
		 * */
		protected $query_args = array();

		/**
		 * Constructor.
		 * */
		public function __construct( $attributes = array(), $post_type = 'products' ) {
			$this->post_type = $post_type;
			$this->query_args = $this->parse_query_args($attributes);
		}

		/**
		 * Parse query arguments.
		 * */
		public function parse_query_args( $attributes ) {

			$attributes = array_merge($this->default_args(), $attributes);

			$attributes['class'] = 'lty_lottery_products';

			if (wc_string_to_bool($attributes['paginate'])) {
				$page = absint(empty($_GET['product-page'])) ? 1 : absint($_GET['product-page']);
				$shortcode_name = isset($_GET['lty_lottery_shortcode']) ? wc_clean(wp_unslash($_GET['lty_lottery_shortcode'])) : '';
				if (!empty($attributes['shortcode_name']) && !empty($shortcode_name) && $attributes['shortcode_name'] != $shortcode_name) {
					$page = 1;
				}

				$attributes['page'] = $page;
			}

			$attributes['posts_per_page'] = intval($attributes['posts_per_page']);
			if (1 < $attributes['posts_per_page']) {
				$attributes['paged'] = absint($attributes['page']);
			}

			if (!empty($attributes['category'])) {
				$attributes = self::get_category_args($attributes);
			}

			return $attributes;
		}

		/**
		 * Default arguments.
		 * */
		public static function default_args() {

			return array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'order' => 'DESC',
				'posts_per_page' => -1,
				'fields' => 'ids',
				'columns' => ( (float) WC()->version >= (float) '3.3.0' ) ? wc_get_default_products_per_row() : '4',
				'class' => '',
				'paginate' => false,
				'page' => '',
				'category' => '',
				'cat_operator' => 'IN',
				'suppress_filters' => false,
				'short_description' => false,
			);
		}

		/**
		 * Get category arguments.
		 * */
		public static function get_category_args( $attributes ) {

			$categories = array_map('sanitize_title', explode(',', $attributes['category']));
			$field = 'slug';

			if (is_numeric($categories[0])) {
				$field = 'term_id';
				$categories = array_map('absint', $categories);
				// Check numeric slugs.
				foreach ($categories as $cat) {
					$the_cat = get_term_by('slug', $cat, 'product_cat');
					if (false !== $the_cat) {
						$categories[] = $the_cat->term_id;
					}
				}
			}

			$attributes['tax_query'][] = array(
				'taxonomy' => 'product_cat',
				'terms' => $categories,
				'field' => $field,
				'operator' => $attributes['cat_operator'],
			);

			return $attributes;
		}

		/**
		 * Get content.
		 * */
		public function get_content() {
			return $this->product_loop();
		}

		/**
		 * Product loop.
		 * */
		public function product_loop() {

			$columns = '' != $this->query_args['columns'] ? absint($this->query_args['columns']) : wc_get_default_products_per_row();
			$classes = $this->get_wrapper_classes($columns);
			$products = $this->get_product_data();

			ob_start();

			if ($products && $products->ids) {

				if ((float) WC()->version >= (float) '3.3.0') {

					wc_setup_loop(array(
						'columns' => $columns,
						'name' => $this->post_type,
						'is_shortcode' => true,
						'is_search' => false,
						'total' => $products->total,
						'total_pages' => $products->total_pages,
						'per_page' => $products->per_page,
						'current_page' => $products->current_page,
						'shortcode_name' => isset($this->query_args['shortcode_name']) ? $this->query_args['shortcode_name'] : '',
						'short_description' => isset($this->query_args['short_description']) ? $this->query_args['short_description'] : '',
					));
				}

				global $post;
				$original_post = $post;
				$reference_post = &$post;

				woocommerce_product_loop_start();

				if ((float) WC()->version >= (float) '3.3.0') {

					// wc_get_loop_prop('total') added in WooCommerce since '3.3.0'.
					if (wc_get_loop_prop('total')) {

						foreach ($products->ids as $product_id) {

							$reference_post = get_post($product_id);
							//Set up global post data.
							setup_postdata($reference_post);
							// Render product template.
							wc_get_template_part('content', 'product');
						}
					}
				} else {

					foreach ($products->ids as $product_id) {

						$reference_post = get_post($product_id);
						//Set up global post data.
						setup_postdata($reference_post);
						// Render product template.
						wc_get_template_part('content', 'product');
					}
				}

				$reference_post = $original_post;

				woocommerce_product_loop_end();

				// Fire standard shop loop hooks when paginating results so we can show result counts and so on.
				// wc_string_to_bool Check pagination is bool value or not. 
				if (wc_string_to_bool($this->query_args['paginate'])) {
					/**
					 * This hook is used to do extra action after shop loop.
					 * 
					 * @since 1.0
					 */
					do_action('woocommerce_after_shop_loop');
				}

				wp_reset_postdata();

				if ((float) WC()->version >= (float) '3.3.0') {
					wc_reset_loop();
				}
			} else {
				esc_html_e('No Products Found.', 'lottery-for-woocommerce');
			}

			return '<div class="' . esc_attr(implode(' ', $classes)) . '">' . ob_get_clean() . '</div>';
		}

		/**
		 * Get wrapper classes.
		 * */
		public function get_wrapper_classes( $columns ) {

			$classes = array( 'woocommerce' );

			if ('product' !== $this->post_type) {
				$classes[] = 'columns-' . $columns;
			}

			$classes[] = $this->query_args['class'];

			return $classes;
		}

		/**
		 * Get product data.
		 * */
		public function get_product_data() {

			$query = new WP_Query($this->query_args);

			$paginated = !$query->get('no_found_rows');

			$data = (object) array(
						'ids' => wp_parse_id_list($query->posts),
						'total' => $paginated ? (int) $query->found_posts : count($query->posts),
						'total_pages' => $paginated ? (int) $query->max_num_pages : 1,
						'per_page' => (int) $query->get('posts_per_page'),
						'current_page' => $paginated ? (int) max(1, $query->get('paged', 1)) : 1,
			);

			return $data;
		}
	}

}
