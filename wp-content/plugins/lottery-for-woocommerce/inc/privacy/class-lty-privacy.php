<?php
/**
 * GDPR Compliance.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly
}

if ( ! class_exists( 'LTY_Privacy' ) ) :

	/**
	 * LTY_Privacy class.
	 * */
	class LTY_Privacy {

		/**
		 * LTY_Privacy constructor.
		 * */
		public function __construct() {
			$this->init_hooks() ;
		}

		/**
		 * Register plugin.
		 * */
		public function init_hooks() {
			// This hook registers Booking System privacy content.
			add_action( 'admin_init' , array( __CLASS__, 'register_privacy_content' ) , 20 ) ;
		}

		/**
		 * Register Privacy Content.
		 * */
		public static function register_privacy_content() {
					
			if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
				return ;
			}

			$content = self::get_privacy_message() ;
						
			if ( $content ) {
				wp_add_privacy_policy_content( __( 'Giveaway(formerly Lottery) For WooCommerce' , 'lottery-for-woocommerce' ) , $content ) ;
			}
		}

		/**
		 * Prepare Privacy Content.
		 * */
		public static function get_privacy_message() {

			return self::get_privacy_message_html() ;
		}

		/**
		 * Get Privacy Content.
		 * */
		public static function get_privacy_message_html() {
					
			ob_start() ;
			?>
			<p><?php esc_html_e( 'This includes the basics of what personal data your store may be collecting, storing and sharing. Depending on what settings are enabled and which additional plugins are used, the specific information shared by your store will vary.' , 'lottery-for-woocommerce' ) ; ?></p>
			<h2><?php esc_html_e( 'WHAT DOES THE PLUGIN DO?' , 'lottery-for-woocommerce' ) ; ?></h2>
			<p><?php esc_html_e( 'Using this plugin, you can create and manage Online Giveaways on your WooCommerce Shop.' , 'lottery-for-woocommerce' ) ; ?> </p>
			<h2><?php esc_html_e( 'WHAT WE COLLECT AND STORE?' , 'lottery-for-woocommerce' ) ; ?></h2>
			<h2><?php esc_html_e( 'User ID and Username' , 'lottery-for-woocommerce' ) ; ?></h2>
			<p><?php esc_html_e( "We record the User Id's of" , 'lottery-for-woocommerce' ) ; ?></p>
			<p><?php esc_html_e( '- Users who participate in the Giveaway' , 'lottery-for-woocommerce' ) ; ?></p>
			<p><?php esc_html_e( '- The User who has won the Giveaway' , 'lottery-for-woocommerce' ) ; ?></p>
			<h2><?php esc_html_e( 'Order ID' , 'lottery-for-woocommerce' ) ; ?></h2>
			<p><?php esc_html_e( "We record the Order Id's of" , 'lottery-for-woocommerce' ) ; ?></p>
			<p><?php esc_html_e( '- Payment made for purchasing a giveaway ticket' , 'lottery-for-woocommerce' ) ; ?></p>
			<?php
			$contents = ob_get_contents() ;
			ob_end_clean() ;

			return $contents ;
		}
	}

	new LTY_Privacy() ;

endif;
