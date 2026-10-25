<?php
/**
 * Companion plugins for the native View Details dialog.
 *
 * @package GroupPasswordReset
 */

defined( 'ABSPATH' ) || exit;

/**
 * Append the companion guide only to this plugin's information.
 *
 * @param mixed  $result Plugin information or the original API result.
 * @param string $action Requested API action.
 * @param object $args API arguments.
 * @return mixed
 */
function gpr_companion_plugin_info( $result, $action, $args ) {
	if ( 'plugin_information' !== $action || ! is_object( $args ) || ! isset( $args->slug ) || 'group-password-reset' !== $args->slug || ! is_object( $result ) || ! isset( $result->sections ) || ! is_array( $result->sections ) ) {
		return $result;
	}
	wp_enqueue_style( 'gpr-brand', GPR_PLUGIN_URL . 'assets/brand/thrg-brand.css', array(), GPR_VERSION );
	$details_css     = GPR_PLUGIN_DIR . 'assets/brand/thrg-details.css';
	$details_version = is_readable( $details_css ) ? GPR_VERSION . '.' . filemtime( $details_css ) : GPR_VERSION;
	wp_enqueue_style( 'gpr-brand-details', GPR_PLUGIN_URL . 'assets/brand/thrg-details.css', array( 'gpr-brand' ), $details_version );
	wp_enqueue_style( 'gpr-plugin-details', plugins_url( 'assets/css/gpr-plugin-details.css', GPR_PLUGIN_FILE ), array(), GPR_VERSION );
	// Remote metadata can omit banners; always use this installed plugin's artwork.
	if ( is_readable( GPR_PLUGIN_DIR . 'assets/images/plugin-banner.png' ) ) {
		$result->banners = array(
			'low'  => GPR_PLUGIN_URL . 'assets/images/plugin-banner.png',
			'high' => GPR_PLUGIN_URL . 'assets/images/plugin-banner.png',
		);
	}
	$result->version                        = GPR_VERSION;
	$result->sections['other_THRG_plugins'] = '<div class="gpr-toolkit"><p class="gpr-toolkit-intro">More tools from The Hack Repair Guy</p><p>Five focused ways to look after your WordPress site. Use only what you need.</p><div class="gpr-toolkit-card"><h3>Disable Other Plugin Notices</h3><p>Group plugin and theme notices into a collapsible panel while keeping WordPress core notices visible.</p><p><a href="https://github.com/hackrepair/disable-other-plugin-notices" target="_blank" rel="noopener noreferrer">Explore Disable Other Plugin Notices &rarr;</a></p></div><div class="gpr-toolkit-card"><h3>Plugin Detective</h3><p>Find the plugin behind a conflict, with recovery tools for when your dashboard is unavailable.</p><p><a href="https://github.com/hackrepair/hackrepair-plugin-detective" target="_blank" rel="noopener noreferrer">Explore Plugin Detective &rarr;</a></p></div><div class="gpr-toolkit-card"><h3>Site Inspector</h3><p>Review security-related observations, keep inspection reports, and see what changed.</p><p><a href="https://github.com/hackrepair/thrg-site-inspector" target="_blank" rel="noopener noreferrer">Explore Site Inspector &rarr;</a></p></div><div class="gpr-toolkit-card"><h3>Post Update Email Notifier</h3><p>Notify selected team members when published content changes, with configurable update emails.</p><p><a href="https://github.com/tvcnet/hackrepair-post-update-email-notifier" target="_blank" rel="noopener noreferrer">Explore Post Update Email Notifier &rarr;</a></p></div><div class="gpr-toolkit-card"><h3>Plugin Archiver</h3><p>Move unused plugins out of the Plugins list without deleting them, and restore their files when needed.</p><p><a href="https://wordpress.org/plugins/hackrepair-plugin-archiver/" target="_blank" rel="noopener noreferrer">Explore Plugin Archiver &rarr;</a></p></div><p class="gpr-toolkit-note">Each plugin works independently. Links open in a new tab.</p></div>';
	return $result;
}
add_filter( 'plugins_api', 'gpr_companion_plugin_info', 100, 3 );
