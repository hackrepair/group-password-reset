<?php
/**
 * Admin rendering helpers for Group Password Reset.
 *
 * @package GroupPasswordReset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gpr_render_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$results_payload    = gpr_get_flash_results();
	$roles              = gpr_get_available_roles();
	$excluded_usernames = get_option( 'gpr_excluded_usernames', '' );
	$screen_notice      = gpr_get_screen_notice( $results_payload );
	$has_result_rows    = $results_payload && ! empty( $results_payload['results'] );
	?>
	<div class="wrap gpr-admin-page">
		<h1>
		<?php
		echo esc_html(
			sprintf(
			/* translators: %s: plugin version */
				__( 'Group Password Reset %s', 'group-password-reset' ),
				GPR_VERSION
			)
		);
		?>
		</h1>
		<p class="gpr-lead">
			<?php esc_html_e( 'Reset passwords for an explicitly selected scope, then optionally attempt secure reset-link notifications for affected accounts.', 'group-password-reset' ); ?>
		</p>

		<?php if ( $screen_notice ) : ?>
			<div class="notice <?php echo esc_attr( $screen_notice['class'] ); ?> gpr-notice">
				<p><?php echo esc_html( $screen_notice['message'] ); ?></p>
			</div>
		<?php endif; ?>

		<div class="gpr-card">
			<h2><?php esc_html_e( 'Choose user role for', 'group-password-reset' ); ?></h2>
			<form id="gpr-reset-form" class="gpr-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'gpr_reset_passwords' ); ?>
				<input type="hidden" name="action" value="gpr_reset_passwords">

				<div class="gpr-field">
					<label for="gpr-user-role"><?php esc_html_e( 'Choose user role for', 'group-password-reset' ); ?></label>
					<select id="gpr-user-role" name="gpr_user_role" required>
						<option value="" selected disabled><?php esc_html_e( 'Choose user role for', 'group-password-reset' ); ?></option>
						<?php foreach ( $roles as $role_key => $role_label ) : ?>
							<option value="<?php echo esc_attr( $role_key ); ?>"><?php echo esc_html( $role_label ); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php esc_html_e( 'Choose All users, a specific role, or an administrator reset that excludes the current account.', 'group-password-reset' ); ?></p>
				</div>

				<div class="gpr-field">
					<label for="gpr-excluded-usernames"><?php esc_html_e( 'Excluded usernames', 'group-password-reset' ); ?></label>
					<textarea id="gpr-excluded-usernames" name="gpr_excluded_usernames" rows="4"><?php echo esc_textarea( $excluded_usernames ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Enter usernames separated by commas. These users will be skipped and listed in the results.', 'group-password-reset' ); ?></p>
				</div>

				<div class="gpr-field gpr-confirmation">
					<label>
						<input type="checkbox" name="gpr_confirm_reset" value="1" required>
						<?php esc_html_e( 'I understand this will invalidate current passwords for the selected users.', 'group-password-reset' ); ?>
					</label>
				</div>

				<div class="gpr-field gpr-confirmation">
					<label>
						<input type="checkbox" name="gpr_skip_email_notifications" value="1">
						<?php esc_html_e( 'Do not email affected users. Reset passwords only.', 'group-password-reset' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'Use this when you want to invalidate passwords first and notify users through another channel.', 'group-password-reset' ); ?></p>
				</div>

				<div id="gpr-email-warning" class="gpr-warning">
					<p><strong><?php esc_html_e( 'Warning:', 'group-password-reset' ); ?></strong> <?php esc_html_e( 'When email mode is enabled, passwords are reset before notification emails are attempted. Mail failures do not roll back the reset and may leave users locked out until they are contacted through another channel.', 'group-password-reset' ); ?></p>
				</div>

				<div id="gpr-progress-panel" class="gpr-progress" hidden aria-live="polite">
					<p class="gpr-progress__status"><?php esc_html_e( 'Preparing password reset job…', 'group-password-reset' ); ?></p>
					<progress class="gpr-progress__bar" value="0" max="100"></progress>
					<p class="gpr-progress__meta"></p>
				</div>

				<div class="gpr-actions">
					<button type="submit" class="button button-primary"><?php esc_html_e( 'Reset Passwords', 'group-password-reset' ); ?></button>
					<p class="description">
					<?php
					echo esc_html(
						sprintf(
						/* translators: %d: number of users processed per JavaScript batch */
							__( 'JavaScript-enabled browsers process accounts in batches of %d. Without JavaScript, the reset runs on form submission and returns to this page with the results.', 'group-password-reset' ),
							GPR_CHUNK_SIZE
						)
					);
					?>
					</p>
				</div>
			</form>
		</div>

			<div class="gpr-card">
				<h2><?php esc_html_e( 'What happens', 'group-password-reset' ); ?></h2>
				<ol class="gpr-steps">
				<li><?php esc_html_e( 'Choose All users, a specific role, or Administrator (excluding current user).', 'group-password-reset' ); ?></li>
				<li><?php esc_html_e( 'List any usernames that must be excluded from the reset.', 'group-password-reset' ); ?></li>
				<li><?php esc_html_e( 'Start the reset. Passwords are invalidated immediately, then secure reset-link emails are attempted unless you choose the no-email option.', 'group-password-reset' ); ?></li>
				<li><?php esc_html_e( 'Review the summary and per-user results on this screen.', 'group-password-reset' ); ?></li>
			</ol>
		</div>

		<div id="gpr-results-panel" class="gpr-card"<?php echo $results_payload ? '' : ' hidden'; ?>>
			<h2><?php esc_html_e( 'Latest results', 'group-password-reset' ); ?></h2>
			<div id="gpr-results-summary">
				<?php if ( $results_payload ) : ?>
					<?php gpr_render_summary( $results_payload ); ?>
				<?php endif; ?>
			</div>
			<p id="gpr-results-note" class="description gpr-results-note"<?php echo $results_payload && empty( $results_payload['details_available'] ) ? '' : ' hidden'; ?>>
				<?php echo esc_html( $results_payload['details_notice'] ?? gpr_get_results_persistence_notice() ); ?>
			</p>
			<table id="gpr-results-table" class="widefat striped gpr-results-table"<?php echo $has_result_rows ? '' : ' hidden'; ?>>
				<thead>
					<tr>
						<th><?php esc_html_e( 'Username', 'group-password-reset' ); ?></th>
						<th><?php esc_html_e( 'Email', 'group-password-reset' ); ?></th>
						<th><?php esc_html_e( 'Role', 'group-password-reset' ); ?></th>
						<th><?php esc_html_e( 'Status', 'group-password-reset' ); ?></th>
						<th><?php esc_html_e( 'Details', 'group-password-reset' ); ?></th>
					</tr>
				</thead>
				<tbody id="gpr-results-body">
					<?php if ( $has_result_rows ) : ?>
						<?php foreach ( $results_payload['results'] as $result ) : ?>
							<?php gpr_render_result_row( $result ); ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
}

function gpr_get_screen_notice( $results_payload ) {
	if ( ! $results_payload ) {
		return null;
	}

	$summary = $results_payload['summary'];

	if ( $summary['failed'] > 0 ) {
		/* translators: 1: successful resets 2: failed resets 3: skipped users */
		$message = __( 'Password reset finished with %1$d success, %2$d failed, and %3$d skipped.', 'group-password-reset' );

		if ( ! empty( $results_payload['notify_users'] ) ) {
			/* translators: 1: successful resets 2: failed resets 3: skipped users */
			$message = __( 'Password reset finished with %1$d success, %2$d failed, and %3$d skipped. Some affected users may not have received email notifications.', 'group-password-reset' );
		}

		return array(
			'class'   => 'notice-warning',
			'message' => sprintf(
				/* translators: 1: successful resets 2: failed resets 3: skipped users */
				$message,
				$summary['success'],
				$summary['failed'],
				$summary['skipped']
			),
		);
	}

	return array(
		'class'   => 'notice-success',
		'message' => sprintf(
			/* translators: 1: successful resets 2: skipped users */
			__( 'Password reset finished with %1$d success and %2$d skipped.', 'group-password-reset' ),
			$summary['success'],
			$summary['skipped']
		),
	);
}

function gpr_render_summary( $results_payload ) {
	$summary = $results_payload['summary'];
	?>
	<div class="gpr-summary-grid">
		<div><strong><?php esc_html_e( 'Scope', 'group-password-reset' ); ?>:</strong> <?php echo esc_html( $results_payload['scope_label'] ); ?></div>
		<div><strong><?php esc_html_e( 'Matched users', 'group-password-reset' ); ?>:</strong> <?php echo esc_html( (string) $summary['total'] ); ?></div>
		<div><strong><?php esc_html_e( 'Processed', 'group-password-reset' ); ?>:</strong> <?php echo esc_html( (string) $summary['processed'] ); ?></div>
		<div><strong><?php esc_html_e( 'Success', 'group-password-reset' ); ?>:</strong> <?php echo esc_html( (string) $summary['success'] ); ?></div>
		<div><strong><?php esc_html_e( 'Failed', 'group-password-reset' ); ?>:</strong> <?php echo esc_html( (string) $summary['failed'] ); ?></div>
		<div><strong><?php esc_html_e( 'Skipped', 'group-password-reset' ); ?>:</strong> <?php echo esc_html( (string) $summary['skipped'] ); ?></div>
	</div>
	<p><strong><?php esc_html_e( 'Notification mode', 'group-password-reset' ); ?>:</strong> <?php echo esc_html( $results_payload['notification_label'] ?? '' ); ?></p>
	<p><strong><?php esc_html_e( 'Excluded accounts', 'group-password-reset' ); ?>:</strong> <?php echo esc_html( (string) ( $results_payload['excluded_count'] ?? 0 ) ); ?></p>
	<?php
}

function gpr_render_result_row( $result ) {
	$status_classes = array(
		'success' => 'gpr-status gpr-status--success',
		'failed'  => 'gpr-status gpr-status--failed',
		'skipped' => 'gpr-status gpr-status--skipped',
	);

	$status_class = isset( $status_classes[ $result['status'] ] ) ? $status_classes[ $result['status'] ] : 'gpr-status';
	?>
	<tr>
		<td><?php echo esc_html( $result['username'] ); ?></td>
		<td><?php echo esc_html( $result['email'] ); ?></td>
		<td><?php echo esc_html( $result['role'] ); ?></td>
		<td><span class="<?php echo esc_attr( $status_class ); ?>"><?php echo esc_html( ucfirst( $result['status'] ) ); ?></span></td>
		<td><?php echo esc_html( $result['message'] ); ?></td>
	</tr>
	<?php
}
