<?php

/**
 * Notifications Instances Class.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Notification_Instances' ) ) {

	/**
	 * Class LTY_Notification_Instances.
	 * */
	class LTY_Notification_Instances {

		/**
		 * Notifications.
		 * */
		private static $notifications = array();

		/**
		 * Get Notifications.
		 * */
		public static function get_notifications() {
			if ( ! self::$notifications ) {
				self::load_notifications();
			}

			return self::$notifications;
		}

		/**
		 * Load all Notifications.
		 * */
		public static function load_notifications() {
			if ( ! class_exists( 'LTY_Notifications' ) ) {
				include LTY_PLUGIN_PATH . '/inc/abstracts/abstract-lty-notifications.php';
			}

			$default_notification_classes = array(
				'admin-lottery-started'                   => 'LTY_Admin_Lottery_Started_Notification',
				'admin-lottery-ended'                     => 'LTY_Admin_Lottery_Ended_Notification',
				'admin-lottery-relisted'                  => 'LTY_Admin_Lottery_Relisted_Notification',
				'admin-lottery-extended'                  => 'LTY_Admin_Lottery_Extended_Notification',
				'admin-unlimited-lottery-extended'        => 'LTY_Admin_Unlimited_Lottery_Extended_Notification',
				'admin-lottery-winner'                    => 'LTY_Admin_Lottery_Winner_Notification',
				'admin-lottery-instant-winner'            => 'LTY_Admin_Lottery_Instant_Winner_Notification',
				'admin-lottery-failed'                    => 'LTY_Admin_Lottery_Failed_Notification',
				'admin-lottery-ticket-confirmation'       => 'LTY_Admin_Lottery_Ticket_Confirmation_Notification',
				'admin-lottery-ticket-confirmation-order' => 'LTY_Admin_Lottery_Ticket_Confirmation_Order_Notification',
				'customer-lottery-started'                => 'LTY_Customer_Lottery_Started_Notification',
				'customer-lottery-ended'                  => 'LTY_Customer_Lottery_Ended_Notification',
				'customer-lottery-relisted'               => 'LTY_Customer_Lottery_Relisted_Notification',
				'customer-lottery-extended'               => 'LTY_Customer_Lottery_Extended_Notification',
				'customer-lottery-ending-soon'            => 'LTY_Customer_Lottery_Ending_Soon_Notification',
				'customer-unlimited-lottery-extended'     => 'LTY_Customer_Unlimited_Lottery_Extended_Notification',
				'customer-lottery-winner'                 => 'LTY_Customer_Lottery_Winner_Notification',
				'customer-lottery-instant-winner'         => 'LTY_Customer_Lottery_Instant_Winner_Notification',
				'customer-unlimited-lottery-instant-winner' => 'LTY_Customer_Unlimited_Lottery_Instant_Winner_Notification',
				'customer-lottery-instant-winner-order'   => 'LTY_Customer_Lottery_Instant_Winner_Order_Notification',
				'customer-lottery-multiple-winner'        => 'LTY_Customer_Lottery_Multiple_Winner_Notification',
				'customer-lottery-failed'                 => 'LTY_Customer_Lottery_Failed_Notification',
				'customer-lottery-ticket-confirmation'    => 'LTY_Customer_Lottery_Ticket_Confirmation_Notification',
				'customer-unlimited-lottery-ticket-confirmation' => 'LTY_Customer_Unlimited_Lottery_Ticket_Confirmation_Notification',
				'customer-lottery-ticket-confirmation-order' => 'LTY_Customer_Lottery_Ticket_Confirmation_Order_Notification',
				'customer-lottery-no-luck'                => 'LTY_Customer_Lottery_No_Luck_Notification',
				'customer-lottery-selected-incorrect-answer' => 'LTY_Customer_Lottery_Selected_Incorrect_Answer_Notification',
			);

			foreach ( $default_notification_classes as $file_name => $notification_class ) {

				// Include notification file.
				include 'class-' . $file_name . '.php';

				// Add notification Object.
				self::add_notification( new $notification_class() );
			}
		}

		/**
		 * Add a notification.
		 * */
		public static function add_notification( $notification ) {

			self::$notifications[ $notification->get_id() ] = $notification;

			return new self();
		}

		/**
		 * Get notification by id.
		 * */
		public static function get_notification_by_id( $notification_id ) {
			$notifications = self::get_notifications();

			return isset( $notifications[ $notification_id ] ) ? $notifications[ $notification_id ] : false;
		}

		/**
		 * Reset.
		 * */
		public static function reset() {
			self::$notifications = null;
		}
	}

}
