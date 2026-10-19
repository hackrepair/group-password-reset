<?php
/**
 * Admin page and request wiring for Group Password Reset.
 *
 * @package GroupPasswordReset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gpr_register_settings() {
	register_setting(
		'gpr_settings_group',
		'gpr_excluded_usernames',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'gpr_sanitize_excluded_usernames',
			'default'           => '',
		)
	);
}

function gpr_add_admin_menu() {
	add_management_page(
		__( 'Group Password Reset', 'group-password-reset' ),
		__( 'Group Password Reset', 'group-password-reset' ),
		'manage_options',
		'group-password-reset',
		'gpr_render_admin_page'
	);
}

function gpr_enqueue_admin_assets( $hook_suffix ) {
	$supported_screens = array(
		'tools_page_group-password-reset',
	);

	if ( ! in_array( $hook_suffix, $supported_screens, true ) ) {
		return;
	}

	wp_enqueue_style( 'gpr-brand', GPR_PLUGIN_URL . 'assets/brand/thrg-brand.css', array(), gpr_get_asset_version( 'assets/brand/thrg-brand.css' ) );

	wp_enqueue_style(
		'gpr-admin-styles',
		GPR_PLUGIN_URL . 'assets/css/admin.css',
		array(),
		gpr_get_asset_version( 'assets/css/admin.css' )
	);

	wp_enqueue_script(
		'gpr-admin-script',
		GPR_PLUGIN_URL . 'assets/js/admin.js',
		array(),
		gpr_get_asset_version( 'assets/js/admin.js' ),
		true
	);

	wp_localize_script(
		'gpr-admin-script',
		'gprAdmin',
		array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'gpr_job_nonce' ),
			'chunkSize' => GPR_CHUNK_SIZE,
			'messages'  => array(
				'sendingSummary'   => __( 'Sending summary…', 'group-password-reset' ),
				'startError'       => __( 'Unable to start the password reset job.', 'group-password-reset' ),
				'processError'     => __( 'Unable to continue the password reset job.', 'group-password-reset' ),
				'complete'         => __( 'Done! Password reset complete.', 'group-password-reset' ),
				'preparingJob'     => __( 'Preparing password reset job…', 'group-password-reset' ),
				'processingJob'    => __( 'Processing password resets…', 'group-password-reset' ),
				'noQueuedUsers'    => __( 'No queued users remained after exclusions.', 'group-password-reset' ),
				'requestFailed'    => __( 'Request failed.', 'group-password-reset' ),
				'allUsers'         => __( 'All users', 'group-password-reset' ),
				'scope'            => __( 'Scope', 'group-password-reset' ),
				'matchedUsers'     => __( 'Matched users', 'group-password-reset' ),
				'processed'        => __( 'Processed', 'group-password-reset' ),
				'success'          => __( 'Success', 'group-password-reset' ),
				'failed'           => __( 'Failed', 'group-password-reset' ),
				'skipped'          => __( 'Skipped', 'group-password-reset' ),
				'notificationMode' => __( 'Notification mode', 'group-password-reset' ),
				'excludedAccounts' => __( 'Excluded accounts', 'group-password-reset' ),
			),
		)
	);
}

function gpr_add_plugin_action_links( $actions, $plugin_file ) {
	if ( plugin_basename( GPR_PLUGIN_FILE ) !== $plugin_file ) {
		return $actions;
	}

	$settings_link = sprintf(
		'<a href="%s">%s</a>',
		esc_url( gpr_get_admin_page_url() ),
		esc_html__( 'Tools', 'group-password-reset' )
	);

	array_unshift( $actions, $settings_link );

	return $actions;
}

function gpr_handle_reset_request() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to perform this action.', 'group-password-reset' ) );
	}

	if ( isset( $_GET['gpr_continue'] ) ) {
		$job_id = isset( $_GET['job_id'] ) ? sanitize_text_field( wp_unslash( $_GET['job_id'] ) ) : '';

		if ( ! gpr_is_valid_job_id( $job_id ) ) {
			wp_die( esc_html__( 'Invalid reset job.', 'group-password-reset' ) );
		}

		check_admin_referer( gpr_get_continue_reset_nonce_action( $job_id ) );

		$job = gpr_get_job( $job_id );

		if ( ! is_array( $job ) ) {
			wp_die( esc_html__( 'No reset job is currently active.', 'group-password-reset' ) );
		}

		if ( ! gpr_current_user_owns_job( $job ) ) {
			wp_die( esc_html__( 'You are not allowed to continue this reset job.', 'group-password-reset' ) );
		}

		$batch_state = gpr_process_job_batch( $job );

		if ( $batch_state['completed'] ) {
			gpr_finalize_job( $job, 'fallback' );
			wp_safe_redirect( gpr_get_admin_page_url() );
			exit;
		}

		gpr_store_job( $job );
		wp_safe_redirect( gpr_get_continue_reset_url( $job_id ) );
		exit;
	}

	check_admin_referer( 'gpr_reset_passwords' );

	if ( empty( $_POST['gpr_confirm_reset'] ) ) {
		wp_die( esc_html__( 'You must confirm the password reset before continuing.', 'group-password-reset' ) );
	}

	$role                   = isset( $_POST['gpr_user_role'] ) ? sanitize_key( wp_unslash( $_POST['gpr_user_role'] ) ) : '';
	$raw_excluded_usernames = '';

	if ( ! gpr_is_valid_scope( $role ) ) {
		wp_die( esc_html__( 'Please choose a valid reset scope.', 'group-password-reset' ) );
	}

	if ( isset( $_POST['gpr_excluded_usernames'] ) ) {
		$raw_excluded_usernames = sanitize_text_field( wp_unslash( $_POST['gpr_excluded_usernames'] ) );
	}

	$excluded_usernames = gpr_sanitize_excluded_usernames( $raw_excluded_usernames );
	$skip_email         = isset( $_POST['gpr_skip_email_notifications'] ) ? gpr_should_skip_email_notifications( wp_unslash( $_POST['gpr_skip_email_notifications'] ) ) : false;

	update_option( 'gpr_excluded_usernames', $excluded_usernames );

	$job               = gpr_create_reset_job( $role, $excluded_usernames, get_current_user_id() );
	$job['skip_email'] = $skip_email;
	gpr_log_job_event( $job, 'job_started', __( 'Reset job created.', 'group-password-reset' ) );

	if ( $job['queued_total'] <= 0 ) {
		gpr_store_flash_results( gpr_build_results_payload( $job, 'fallback', array(), false ) );
		gpr_log_job_event( $job, 'job_completed', gpr_get_audit_completion_message( $job ) );
		wp_safe_redirect( gpr_get_admin_page_url() );
		exit;
	}

	$batch_state = gpr_process_job_batch( $job );

	if ( $batch_state['completed'] ) {
		gpr_finalize_job( $job, 'fallback' );
		wp_safe_redirect( gpr_get_admin_page_url() );
		exit;
	}

	gpr_store_job( $job );
	wp_safe_redirect( gpr_get_continue_reset_url( $job['job_id'] ) );
	exit;
}

function gpr_get_admin_page_url() {
	return admin_url( 'tools.php?page=group-password-reset' );
}

function gpr_get_continue_reset_nonce_action( $job_id ) {
	return 'gpr_continue_reset_passwords_' . $job_id;
}

function gpr_get_continue_reset_url( $job_id ) {
	$url = add_query_arg(
		array(
			'action'       => 'gpr_reset_passwords',
			'gpr_continue' => 1,
			'job_id'       => rawurlencode( $job_id ),
		),
		admin_url( 'admin-post.php' )
	);

	return wp_nonce_url( $url, gpr_get_continue_reset_nonce_action( $job_id ) );
}

add_action( 'admin_init', 'gpr_register_settings' );
add_action( 'admin_menu', 'gpr_add_admin_menu' );
add_action( 'admin_enqueue_scripts', 'gpr_enqueue_admin_assets' );
add_action( 'admin_post_gpr_reset_passwords', 'gpr_handle_reset_request' );
add_filter( 'plugin_action_links', 'gpr_add_plugin_action_links', 10, 2 );
