<?php
/**
 * Admin rendering helpers for Group Password Reset.
 *
 * @package GroupPasswordReset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gpr_render_plugin_details_modal() {
	global $pagenow;

	if ( 'plugins.php' !== $pagenow ) {
		return;
	}
	?>
	<div id="gpr-plugin-modal" class="gpr-plugin-modal" hidden>
		<div class="gpr-plugin-modal__backdrop" data-gpr-modal-close="1"></div>
		<div class="gpr-plugin-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="gpr-plugin-modal-title">
			<button type="button" class="gpr-plugin-modal__close" aria-label="<?php esc_attr_e( 'Close plugin details', 'group-password-reset' ); ?>" data-gpr-modal-close="1">×</button>

			<div class="gpr-plugin-modal__banner">
				<img src="<?php echo esc_url( GPR_PLUGIN_URL . 'assets/images/plugin-banner.png' ); ?>" alt="<?php esc_attr_e( 'Group Password Reset banner', 'group-password-reset' ); ?>">
				<div class="gpr-plugin-modal__banner-copy">
					<h2 id="gpr-plugin-modal-title"><?php echo esc_html( GPR_PLUGIN_NAME ); ?></h2>
				</div>
			</div>

			<div class="gpr-plugin-modal__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Plugin information tabs', 'group-password-reset' ); ?>">
				<button type="button" class="gpr-plugin-modal__tab is-active" data-gpr-tab="description"><?php esc_html_e( 'Description', 'group-password-reset' ); ?></button>
				<button type="button" class="gpr-plugin-modal__tab" data-gpr-tab="installation"><?php esc_html_e( 'Installation', 'group-password-reset' ); ?></button>
				<button type="button" class="gpr-plugin-modal__tab" data-gpr-tab="changelog"><?php esc_html_e( 'Changelog', 'group-password-reset' ); ?></button>
			</div>

			<div class="gpr-plugin-modal__content">
				<div class="gpr-plugin-modal__main">
					<section class="gpr-plugin-modal__panel is-active" data-gpr-panel="description">
						<p class="gpr-plugin-modal__eyebrow"><?php esc_html_e( 'Security Utility', 'group-password-reset' ); ?></p>
						<p><strong><?php esc_html_e( 'Group Password Reset is built for administrators who need to invalidate passwords quickly across a role or an entire site. It is particularly useful for post-hack cleanup, membership sites, and any workflow that requires mass credential rotation.', 'group-password-reset' ); ?></strong></p>
						<ul>
							<li><?php esc_html_e( 'Reset by role or across all users', 'group-password-reset' ); ?></li>
							<li><?php esc_html_e( 'Exclude specific usernames from a run', 'group-password-reset' ); ?></li>
							<li><?php esc_html_e( 'Process large user lists in batches', 'group-password-reset' ); ?></li>
							<li><?php esc_html_e( 'Show success, failed, and skipped results clearly', 'group-password-reset' ); ?></li>
							<li><?php esc_html_e( 'Attempt secure reset-link notifications after passwords are invalidated, or run in no-email mode', 'group-password-reset' ); ?></li>
							<li><?php esc_html_e( 'Provide a JavaScript-enhanced flow with a non-JavaScript fallback', 'group-password-reset' ); ?></li>
						</ul>
						<p class="gpr-plugin-modal__donate-wrap">
							<a class="button gpr-plugin-modal__donate-button" href="https://hackrepair.com/donations/buy-jim-a-coffee" target="_blank" rel="noopener"><?php esc_html_e( 'Donate to this plugin »', 'group-password-reset' ); ?></a>
						</p>
					</section>

					<section class="gpr-plugin-modal__panel" data-gpr-panel="installation" hidden>
						<ol>
							<li><?php esc_html_e( 'Download the official group-password-reset.zip release package from the GitHub releases page.', 'group-password-reset' ); ?></li>
							<li><?php esc_html_e( 'Go to Plugins > Add New Plugin in WordPress.', 'group-password-reset' ); ?></li>
							<li><?php esc_html_e( 'Upload the zip file and activate the plugin.', 'group-password-reset' ); ?></li>
							<li><?php esc_html_e( 'Open Group Password Reset from the WordPress admin menu.', 'group-password-reset' ); ?></li>
						</ol>
						<p><?php esc_html_e( 'Manual installation is also supported by uploading the group-password-reset folder into /wp-content/plugins/. Once installed, new versions are offered as one-click updates on the Plugins screen, checked against this plugin\'s GitHub releases.', 'group-password-reset' ); ?></p>
					</section>

					<section class="gpr-plugin-modal__panel" data-gpr-panel="changelog" hidden>
						<h3>
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: plugin version */
								__( 'Version %s', 'group-password-reset' ),
								GPR_VERSION
							)
						);
						?>
						</h3>
						<ul>
							<li><?php esc_html_e( 'New: checks this plugin\'s GitHub releases and offers one-click updates on the Plugins screen.', 'group-password-reset' ); ?></li>
							<li><?php esc_html_e( 'Change: now runs on PHP 7.4 and newer.', 'group-password-reset' ); ?></li>
							<li><?php esc_html_e( 'New: the update details window falls back to this plugin\'s own readme when GitHub cannot be reached.', 'group-password-reset' ); ?></li>
							<li>
							<?php
							echo esc_html(
								sprintf(
								/* translators: 1: tested up to WordPress version, 2: required PHP version */
									__( 'Compatibility: tested up to WordPress %1$s, requires PHP %2$s or newer.', 'group-password-reset' ),
									GPR_TESTED_UP_TO,
									GPR_REQUIRES_PHP
								)
							);
							?>
							</li>
						</ul>
					</section>
				</div>

				<aside class="gpr-plugin-modal__sidebar">
					<ul class="gpr-plugin-modal__meta">
						<li><strong><?php esc_html_e( 'Version:', 'group-password-reset' ); ?></strong> <?php echo esc_html( GPR_VERSION ); ?></li>
						<li><strong><?php esc_html_e( 'Author:', 'group-password-reset' ); ?></strong> <a href="<?php echo esc_url( GPR_AUTHOR_URI ); ?>" target="_blank" rel="noopener"><?php echo esc_html( GPR_AUTHOR_NAME ); ?></a></li>
						<li><strong><?php esc_html_e( 'Last Updated:', 'group-password-reset' ); ?></strong> <?php echo esc_html( GPR_RELEASE_DATE ); ?></li>
						<li><strong><?php esc_html_e( 'Requires WordPress Version:', 'group-password-reset' ); ?></strong> 
						<?php
						echo esc_html(
							sprintf(
							/* translators: %s: minimum supported WordPress version */
								__( '%s or higher', 'group-password-reset' ),
								GPR_REQUIRES_AT_LEAST
							)
						);
						?>
						</li>
						<li><strong><?php esc_html_e( 'Compatible up to:', 'group-password-reset' ); ?></strong> <?php echo esc_html( GPR_TESTED_UP_TO ); ?></li>
						<li><strong><?php esc_html_e( 'Requires PHP:', 'group-password-reset' ); ?></strong> <?php echo esc_html( GPR_REQUIRES_PHP ); ?></li>
						<li><strong><?php esc_html_e( 'Active Installations:', 'group-password-reset' ); ?></strong> <?php echo esc_html( GPR_INSTALLATION_LABEL ); ?></li>
						<li><a href="<?php echo esc_url( GPR_REPO_URL ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'GitHub Plugin Page »', 'group-password-reset' ); ?></a></li>
						<li><a href="<?php echo esc_url( GPR_PLUGIN_URI ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Plugin Homepage »', 'group-password-reset' ); ?></a></li>
						<li><a href="https://www.reddit.com/user/hackrepair/comments/1s8a24b/new_plugin_group_password_reset_bulk_password/" target="_blank" rel="noopener"><?php esc_html_e( 'Support Forum »', 'group-password-reset' ); ?></a></li>
					</ul>
				</aside>
			</div>

			<div class="gpr-plugin-modal__footer">
				<div class="gpr-plugin-modal__footer-actions">
					<a class="button button-primary" href="<?php echo esc_url( gpr_get_download_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Download Plugin', 'group-password-reset' ); ?></a>
					<a class="button" href="<?php echo esc_url( GPR_PLUGIN_URI ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Plugin Homepage', 'group-password-reset' ); ?></a>
				</div>
			</div>
		</div>
	</div>
	<?php
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
