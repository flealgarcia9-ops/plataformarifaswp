<?php
/**
 * Notifications Table.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<tr valign="top">
	<td class="lty_notifications_wrapper">
		<table class="lty_notifications_table widefat striped">
			<thead>
				<tr>
					<?php
					/**
					 * This hook is used to alter the email notification column.
					 *
					 * @since 1.0
					 */
					$columns = apply_filters(
						'lty_lottery_email_notifications_cloumn',
						array(
							'status'      => __( 'Status', 'lottery-for-woocommerce' ),
							'name'        => __( 'Email', 'lottery-for-woocommerce' ),
							'description' => __( 'Description', 'lottery-for-woocommerce' ),
							'recipient'   => __( 'Recipient(s)', 'lottery-for-woocommerce' ),
							'pdf'         => __( 'PDF', 'lottery-for-woocommerce' ),
							'actions'     => __( 'Actions', 'lottery-for-woocommerce' ),
						)
					);
					foreach ( $columns as $key => $column ) :
						echo '<th class="lty-notification-settings-table-' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
					endforeach;
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ( LTY_Notification_Instances::get_notifications() as $key => $notification ) :

					if ( ! $notification->get_in_table() ) :
						continue;
					endif;
					?>
					<tr>
						<?php foreach ( $columns as $cloumn_key => $label ) : ?>
							<?php
							switch ( $cloumn_key ) :
								case 'status':
									?>
									<td data-title="<?php echo esc_attr( $label ); ?>">
										<input type="checkbox" name="<?php echo esc_attr( $notification->get_option_key( 'enabled' ) ); ?>" <?php checked( true, $notification->is_enabled() ); ?>/>
									</td>
									<?php
									break;
								case 'name':
									?>
									<td data-title="<?php echo esc_attr( $label ); ?>">
										<a href="
										<?php
										echo esc_url(
											lty_get_settings_page_url(
												array(
													'tab' => 'notifications',
													'section' => $key,
												)
											)
										);
										?>
										"><?php echo esc_html( $notification->get_title() ); ?></a>
									</td>
									<?php
									break;
								case 'description':
									?>
									<td data-title="<?php echo esc_attr( $label ); ?>">
										<?php echo esc_html( $notification->get_description() ); ?>
									</td>
									<?php
									break;
								case 'recipient':
									?>
									<td data-title="<?php echo esc_attr( $label ); ?>">
										<?php
										$admin_emails = explode( ',', $notification->get_admin_emails() );
										$recipient    = ( 'customer' === $notification->get_type() ) ? __( 'Customer', 'lottery-for-woocommerce' ) : implode( ' , ', $admin_emails );
										echo esc_html( $recipient );
										?>
									</td>
									<?php
									break;
								case 'pdf':
									?>
										<td data-title="<?php echo esc_attr( $label ); ?>">
											<?php
											if ( ! $notification->is_support_pdf_attachment() ) :
												esc_html_e( 'Not Supported', 'lottery-for-woocommerce' );
											else :
												?>
												<input type='checkbox' name="<?php echo esc_attr( $notification->get_option_key( 'pdf_attachment' ) ); ?>" <?php checked( 'yes', $notification->get_option( 'pdf_attachment' ), true ); ?>>
											<?php endif; ?>
										</td>
										<?php
									break;
								case 'actions':
									?>
									<td data-title="<?php echo esc_attr( $label ); ?>">
										<a href="
										<?php
										echo esc_url(
											lty_get_settings_page_url(
												array(
													'tab' => 'notifications',
													'section' => $key,
												)
											)
										);
										?>
										" class="button"><?php esc_html_e( 'Manage', 'lottery-for-woocommerce' ); ?></a>
									</td>
									<?php
									break;
								default:
									/**
									 * This hook is used to display email notification table custom column content.
									 *
									 * @since 1.0
									 */
									do_action( 'lty_lottery_email_notifications_' . $key, $notification );
									break;
							endswitch;
						endforeach;
						?>
					</tr>
					<?php
				endforeach;
				?>
			</tbody>
		</table>
	</td>
</tr>
<?php
