<?php
/**
 * Plugin Name: The Hack Repair Guy's Group Password Reset
 * Plugin URI: https://hackrepair.com/plugins/group-password-reset
 * Description: Bulk reset WordPress user passwords by role, exclude selected users, and optionally notify affected users with secure reset links.
 * Version: 3.3.0
 * Requires at least: 6.8.3
 * Requires PHP: 7.4
 * Tested up to: 6.9
 * Author: Jim Walker
 * Author URI: https://hackrepair.com
 * Update URI: https://github.com/hackrepair/group-password-reset
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: group-password-reset
 *
 * @package GroupPasswordReset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GPR_VERSION', '3.3.0' );
define( 'GPR_PLUGIN_NAME', "The Hack Repair Guy's Group Password Reset" );
define( 'GPR_PLUGIN_URI', 'https://hackrepair.com/plugins/group-password-reset' );
define( 'GPR_AUTHOR_NAME', 'Jim Walker' );
define( 'GPR_AUTHOR_URI', 'https://hackrepair.com' );
define( 'GPR_REPO_URL', 'https://github.com/hackrepair/group-password-reset' );
define( 'GPR_REQUIRES_AT_LEAST', '6.8.3' );
define( 'GPR_REQUIRES_PHP', '7.4' );
define( 'GPR_TESTED_UP_TO', '6.9' );
define( 'GPR_RELEASE_DATE', 'October 17, 2026' );
define( 'GPR_INSTALLATION_LABEL', 'Direct GitHub release' );
define( 'GPR_PLUGIN_FILE', __FILE__ );
define( 'GPR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GPR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GPR_CHUNK_SIZE', 20 );
define( 'GPR_RESULTS_TRANSIENT_TTL', 15 * MINUTE_IN_SECONDS );
define( 'GPR_JOB_TRANSIENT_TTL', 30 * MINUTE_IN_SECONDS );

function gpr_get_asset_version( $relative_path ) {
	$file_path = GPR_PLUGIN_DIR . ltrim( $relative_path, '/\\' );

	if ( file_exists( $file_path ) ) {
		$modified_time = filemtime( $file_path );

		if ( false !== $modified_time ) {
			return GPR_VERSION . '.' . $modified_time;
		}
	}

	return GPR_VERSION;
}

function gpr_get_release_url() {
	return trailingslashit( GPR_REPO_URL ) . 'releases/tag/v' . GPR_VERSION;
}

function gpr_get_download_url() {
	return trailingslashit( GPR_REPO_URL ) . 'releases/download/v' . GPR_VERSION . '/group-password-reset.zip';
}

require_once GPR_PLUGIN_DIR . 'includes/password-reset.php';
require_once GPR_PLUGIN_DIR . 'includes/audit-log.php';
require_once GPR_PLUGIN_DIR . 'includes/admin-views.php';
require_once GPR_PLUGIN_DIR . 'includes/admin-menu.php';

$gpr_update_checker = GPR_PLUGIN_DIR . 'plugin-update-checker/plugin-update-checker.php';

if ( is_readable( $gpr_update_checker ) ) {
	require_once $gpr_update_checker;
}
unset( $gpr_update_checker );

require_once GPR_PLUGIN_DIR . 'includes/class-gpr-updater.php';

/*
 * Deliberately NOT gated by is_admin(): WordPress's own update-check and
 * auto-update machinery runs on cron, where is_admin() is false. Gating the
 * updater the way an admin-only screen is gated would silently break update
 * checks and auto-updates.
 */
$gpr_updater = new GPR_Updater();
$gpr_updater->init();
unset( $gpr_updater );

function gpr_activate_plugin() {
	gpr_maybe_upgrade_audit_schema();
}

function gpr_deactivate_plugin() {
	gpr_clear_runtime_state();
}

add_action( 'plugins_loaded', 'gpr_maybe_upgrade_audit_schema' );
register_activation_hook( __FILE__, 'gpr_activate_plugin' );
register_deactivation_hook( __FILE__, 'gpr_deactivate_plugin' );
