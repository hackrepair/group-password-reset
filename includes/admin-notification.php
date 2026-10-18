<?php
/**
 * Administrator notifications for completed reset summaries.
 *
 * @package GroupPasswordReset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gpr_save_notification_summary( $payload ) {
	// Keep only summary data, separate from the one-use results notice.
	$entry = array(
		'id'                 => wp_generate_uuid4(),
		'time'               => time(),
		'summary'            => $payload['summary'],
		'scope_label'        => $payload['scope_label'],
		'notification_label' => $payload['notification_label'],
		'excluded_count'     => $payload['excluded_count'],
	);
	set_transient( 'gpr_results_email_' . get_current_user_id(), $entry, GPR_RESULTS_TRANSIENT_TTL );
}

function gpr_notification_recipients() {
	return array_filter(
		get_users(
			array(
				'role'    => 'administrator',
				'number'  => 100,
				'orderby' => 'display_name',
			)
		),
		static function ( $user ) {
			return is_email( $user->user_email );
		}
	);
}

function gpr_notification_text( $entry, $note = '' ) {
	$lines = array(
		__( 'Hello,', 'group-password-reset' ),
		'',
		__( 'A site administrator has shared the results of a completed password reset run with you.', 'group-password-reset' ),
		'',
		__( 'Site:', 'group-password-reset' ) . ' ' . home_url( '/' ),
		__( 'Completed:', 'group-password-reset' ) . ' ' . wp_date( 'Y-m-d H:i T', $entry['time'] ),
		__( 'Scope:', 'group-password-reset' ) . ' ' . $entry['scope_label'],
		'',
	);
	if ( '' !== $note ) {
		$lines[] = __( 'A note from the sender:', 'group-password-reset' );
		$lines[] = $note;
		$lines[] = '';
	}
	$lines[] = __( 'Password reset results', 'group-password-reset' );
	$lines[] = '----------------------';
	$labels  = array(
		'total'     => __( 'Matched users', 'group-password-reset' ),
		'processed' => __( 'Processed', 'group-password-reset' ),
		'success'   => __( 'Success', 'group-password-reset' ),
		'failed'    => __( 'Failed', 'group-password-reset' ),
		'skipped'   => __( 'Skipped', 'group-password-reset' ),
	);
	foreach ( $labels as $key => $label ) {
		$lines[] = $label . ': ' . (int) $entry['summary'][ $key ];
	}
	$lines[] = __( 'Excluded accounts', 'group-password-reset' ) . ': ' . (int) $entry['excluded_count'];
	$lines[] = '';
	$lines[] = __( 'User notifications', 'group-password-reset' );
	$lines[] = $entry['notification_label'];
	$lines[] = '';
	if ( ! empty( $entry['summary']['failed'] ) ) {
		$lines[] = __( 'This run finished with failures. Please follow up with the administrator who ran the reset. A failed result can mean that a password was changed but its notification email could not be sent.', 'group-password-reset' );
		$lines[] = '';
	}
	$lines[] = __( 'Email acceptance does not confirm delivery. This summary does not contain passwords, reset links, or individual account details.', 'group-password-reset' );
	$lines[] = '';
	$lines[] = __( 'To open Group Password Reset, sign in to your WordPress dashboard:', 'group-password-reset' );
	$lines[] = admin_url( 'tools.php?page=group-password-reset' );
	$lines[] = '';
	$lines[] = __( 'If you did not expect this activity, contact your site administrator promptly.', 'group-password-reset' );
	$lines[] = '';
	$lines[] = __( 'Thank you,', 'group-password-reset' );
	$lines[] = __( "The Hack Repair Guy's Group Password Reset", 'group-password-reset' );
	$lines[] = home_url( '/' );
	return implode( "\r\n", $lines );
}

function gpr_render_notification_panel() {
	$entry      = get_transient( 'gpr_results_email_' . get_current_user_id() );
	$recipients = gpr_notification_recipients();
	$status     = get_transient( 'gpr_results_email_notice_' . get_current_user_id() );
	delete_transient( 'gpr_results_email_notice_' . get_current_user_id() );
	?>
	<section id="gpr-admin-notification" class="gpr-block" aria-labelledby="gpr-notify-title">
		<h2 id="gpr-notify-title" class="gpr-section"><?php esc_html_e( 'Notify administrators', 'group-password-reset' ); ?></h2>
		<div class="gpr-card">
			<p><?php esc_html_e( 'Email a completed reset summary to selected administrators. This is separate from reset-link emails to affected users.', 'group-password-reset' ); ?></p>
			<p><?php esc_html_e( 'The summary is available for 15 minutes after completion. It contains counts, scope, completion time, and notification mode, without passwords, reset links, or per-user results.', 'group-password-reset' ); ?></p>
			<p id="gpr-notify-empty"<?php echo $entry ? ' hidden' : ''; ?>><?php esc_html_e( 'Complete a password reset to prepare a summary.', 'group-password-reset' ); ?></p>
			<form id="gpr-notify-form" class="gpr-notify-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"<?php echo $entry ? '' : ' hidden'; ?>>
				<input type="hidden" name="action" value="gpr_email_summary">
				<input type="hidden" name="report" value="<?php echo esc_attr( $entry['id'] ?? '' ); ?>">
				<?php wp_nonce_field( 'gpr_email_summary' ); ?>
				<fieldset class="gpr-field">
					<legend><strong><?php esc_html_e( 'Send summary to:', 'group-password-reset' ); ?></strong></legend>
					<?php if ( $recipients ) : ?>
						<p class="gpr-notify-all"><label><input type="checkbox" id="gpr-notify-all">
						<?php
							/* translators: %d: number of administrator recipients. */
							echo esc_html( sprintf( __( 'All administrators (%d)', 'group-password-reset' ), count( $recipients ) ) );
						?>
						</label></p>
					<?php endif; ?>
					<div class="gpr-notify-list">
					<?php foreach ( $recipients as $recipient ) : ?>
						<label class="gpr-notify-recipient"><input type="checkbox" name="recipients[]" value="<?php echo esc_attr( $recipient->ID ); ?>"> <?php echo esc_html( $recipient->display_name ); ?> <span class="gpr-notify-muted"><?php echo esc_html( '<' . $recipient->user_email . '>' ); ?></span></label>
					<?php endforeach; ?>
					</div>
					<?php if ( ! $recipients ) : ?>
						<p><?php esc_html_e( 'No administrator accounts with a valid email address were found.', 'group-password-reset' ); ?></p>
					<?php endif; ?>
				</fieldset>
				<div class="gpr-field">
					<label for="gpr-notify-note"><?php esc_html_e( 'Add a personal note (optional)', 'group-password-reset' ); ?></label>
					<textarea id="gpr-notify-note" name="note" rows="3" maxlength="1000" placeholder="<?php esc_attr_e( 'For example: Please review these results and follow up on any failed notifications.', 'group-password-reset' ); ?>"></textarea>
				</div>
				<details class="gpr-how"><summary><?php esc_html_e( 'Preview summary', 'group-password-reset' ); ?></summary><pre id="gpr-notify-preview"><?php echo esc_html( $entry ? gpr_notification_text( $entry ) : '' ); ?></pre></details>
				<p class="gpr-notify-subject"><span class="gpr-notify-muted"><?php esc_html_e( 'Subject:', 'group-password-reset' ); ?></span> <?php echo esc_html( gpr_email_subject( __( 'Password Reset Summary', 'group-password-reset' ) ) ); ?></p>
				<button type="submit" class="button button-primary"<?php disabled( ! $recipients ); ?>><?php esc_html_e( 'Send email', 'group-password-reset' ); ?></button>
			</form>
			<p id="gpr-notify-status" role="status" aria-live="polite"><?php echo esc_html( $status ? $status : '' ); ?></p>
		</div>
	</section>
	<?php
}

function gpr_send_notification( $report_id, $user_ids, $note ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return new WP_Error( 'forbidden', __( 'You are not allowed to send this summary.', 'group-password-reset' ) );
	}
	$key   = 'gpr_results_email_' . get_current_user_id();
	$entry = get_transient( $key );
	if ( ! $entry || ! hash_equals( $entry['id'], $report_id ) ) {
		return new WP_Error( 'expired', __( 'This summary has expired or been replaced by a newer reset. Refresh the page to see the latest available summary.', 'group-password-reset' ) );
	}
	$recipients = array_filter(
		gpr_notification_recipients(),
		static function ( $user ) use ( $user_ids ) {
			return in_array( (int) $user->ID, $user_ids, true );
		}
	);
	if ( ! $recipients ) {
		return new WP_Error( 'recipients', __( 'Select at least one current administrator.', 'group-password-reset' ) );
	}
	if ( get_transient( $key . '_cooldown' ) ) {
		return new WP_Error( 'cooldown', __( 'Please wait one minute before sending another summary.', 'group-password-reset' ) );
	}
	set_transient( $key . '_cooldown', true, MINUTE_IN_SECONDS );
	$body = gpr_notification_text( $entry, $note );
	$sent = 0;
	foreach ( $recipients as $recipient ) {
		$alternative = static function ( $mailer ) use ( $body ) {
			$mailer->AltBody = $body; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- PHPMailer property.
		};
		add_action( 'phpmailer_init', $alternative );
		try {
			$html = gpr_notification_html( $entry, $note, (string) $recipient->display_name );
			if ( wp_mail( $recipient->user_email, gpr_email_subject( __( 'Password Reset Summary', 'group-password-reset' ) ), $html, array( 'Content-Type: text/html; charset=UTF-8' ) ) ) {
				++$sent;
			}
		} finally {
			remove_action( 'phpmailer_init', $alternative );
		}
	}
	return sprintf(
		/* translators: 1: emails accepted for sending, 2: emails not accepted */
		__( 'Email accepted for sending: %1$d. Failed: %2$d. Acceptance does not confirm delivery.', 'group-password-reset' ),
		$sent,
		count( $recipients ) - $sent
	);
}

function gpr_handle_notification() {
	check_admin_referer( 'gpr_email_summary' );
	$report = isset( $_POST['report'] ) && is_string( $_POST['report'] ) ? sanitize_text_field( wp_unslash( $_POST['report'] ) ) : '';
	$ids    = isset( $_POST['recipients'] ) && is_array( $_POST['recipients'] ) ? array_map( 'absint', wp_unslash( $_POST['recipients'] ) ) : array();
	$note   = isset( $_POST['note'] ) && is_string( $_POST['note'] ) ? substr( sanitize_textarea_field( wp_unslash( $_POST['note'] ) ), 0, 1000 ) : '';
	$result = gpr_send_notification( $report, $ids, $note );
	if ( wp_doing_ajax() ) {
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message(), 400 );
		}
		wp_send_json_success( $result );
	}
	set_transient( 'gpr_results_email_notice_' . get_current_user_id(), is_wp_error( $result ) ? $result->get_error_message() : $result, MINUTE_IN_SECONDS );
	wp_safe_redirect( gpr_get_admin_page_url() . '#gpr-admin-notification' );
	exit;
}

function gpr_ajax_notification_summary() {
	check_ajax_referer( 'gpr_job_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( __( 'Unauthorized.', 'group-password-reset' ), 403 );
	}
	$entry = get_transient( 'gpr_results_email_' . get_current_user_id() );
	$data  = $entry ? array(
		'id'   => $entry['id'],
		'text' => gpr_notification_text( $entry ),
	) : null;
	wp_send_json_success( $data );
}

add_action( 'admin_post_gpr_email_summary', 'gpr_handle_notification' );
add_action( 'wp_ajax_gpr_email_summary', 'gpr_handle_notification' );
add_action( 'wp_ajax_gpr_notification_summary', 'gpr_ajax_notification_summary' );
