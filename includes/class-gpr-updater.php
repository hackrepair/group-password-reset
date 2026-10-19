<?php
/**
 * Update checks against this plugin's GitHub releases.
 *
 * @package GroupPasswordReset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
use YahnisElsts\PluginUpdateChecker\v5p7\Vcs\Api;

/**
 * Points WordPress at GitHub releases for this plugin's updates.
 *
 * This plugin is not distributed on the WordPress.org repository, so it
 * would otherwise never show an "update available" notice. This class
 * wires in the Plugin Update Checker library (vendored in
 * `plugin-update-checker/`) so WordPress checks this plugin's GitHub
 * releases the same way it checks wordpress.org for everything else.
 *
 * A release's own uploaded zip asset (`group-password-reset.zip`) is used
 * instead of GitHub's auto-generated source archive, because the
 * auto-generated archive extracts into a version-stamped folder name
 * (for example `group-password-reset-3.3.0/`) which would change the
 * installed plugin's directory name and deactivate it on update.
 *
 * @since 3.3.0
 */
final class GPR_Updater {

	const BRANCH        = 'main';
	const SLUG          = 'group-password-reset';
	const ASSET_PATTERN = '/^group-password-reset\.zip$/';

	/**
	 * Update checker instance.
	 *
	 * @var \YahnisElsts\PluginUpdateChecker\v5\Vcs\Api|null
	 */
	private $checker = null;

	/**
	 * Build and configure the update checker.
	 *
	 * Safe to call even if the vendored library failed to load (for example
	 * a stripped-down copy of the plugin folder); it just does nothing.
	 */
	public function init() {
		// Registered before the library check below: the details window works
		// from this plugin's own readme.txt, so it does not depend on GitHub.
		add_filter( 'plugins_api', array( $this, 'local_plugin_info' ), 30, 3 );

		if ( ! class_exists( PucFactory::class ) ) {
			return;
		}

		$this->checker = PucFactory::buildUpdateChecker(
			trailingslashit( GPR_REPO_URL ),
			GPR_PLUGIN_FILE,
			self::SLUG
		);

		$this->checker->setBranch( self::BRANCH );

		$api = $this->checker->getVcsApi();

		// A different loaded version of the update checker may lack one of the
		// controls below. Disable its hooks instead of offering a source archive.
		if ( ! $api || ! method_exists( $api, 'enableReleaseAssets' ) ||
			! method_exists( $api, 'setReleaseFilter' ) || ! method_exists( $this->checker, 'addFilter' ) ) {
			$this->checker->removeHooks();
			$this->checker = null;
			return;
		}

		require_once __DIR__ . '/class-gpr-update-notices.php';
		new GPR_Update_Notices( $this->checker );

		$api->enableReleaseAssets( self::ASSET_PATTERN, Api::REQUIRE_RELEASE_ASSETS );

		// The library normally tries a tag and then a branch when a release is
		// rejected. Both alternatives download an auto-generated source archive.
		$this->checker->addFilter(
			'vcs_update_detection_strategies',
			static function ( $strategies ) {
				return isset( $strategies[ Api::STRATEGY_LATEST_RELEASE ] )
					? array( Api::STRATEGY_LATEST_RELEASE => $strategies[ Api::STRATEGY_LATEST_RELEASE ] )
					: array();
			}
		);

		// Only accept a release whose tag is `v` plus its version and which
		// carries the correctly named zip asset.
		$api->setReleaseFilter(
			static function ( $version, $release ) {
				if ( ! is_string( $version ) || ! preg_match( '/^[0-9]+(?:\.[0-9]+){2}$/', $version ) ||
					! isset( $release->tag_name, $release->assets ) ||
					'v' . $version !== $release->tag_name || ! is_array( $release->assets ) ) {
					return false;
				}

				foreach ( $release->assets as $asset ) {
					if ( isset( $asset->name ) && is_string( $asset->name ) &&
						preg_match( self::ASSET_PATTERN, $asset->name ) ) {
						return true;
					}
				}

				return false;
			},
			Api::RELEASE_FILTER_SKIP_PRERELEASE,
			1
		);
	}

	/**
	 * Expose the underlying checker instance, mostly for debugging.
	 *
	 * @return \YahnisElsts\PluginUpdateChecker\v5\Vcs\Api|null
	 */
	public function checker() {
		return $this->checker;
	}

	/**
	 * Fill the update notice's "View version details" window from the installed readme.txt.
	 *
	 * The update checker answers this window from the GitHub copy of readme.txt.
	 * When that copy can't be reached (GitHub is down, or the site blocks
	 * outside requests) WordPress would otherwise show "Plugin not found."
	 * This runs after the update checker and only steps in when nobody else
	 * has answered.
	 *
	 * @param false|object|array $result Result so far.
	 * @param string             $action API action.
	 * @param object             $args   Request arguments.
	 * @return false|object|array
	 */
	public function local_plugin_info( $result, $action = null, $args = null ) {
		if ( false !== $result || 'plugin_information' !== $action || ! is_object( $args ) ||
			! isset( $args->slug ) || self::SLUG !== $args->slug ) {
			return $result;
		}

		$readme_file = GPR_PLUGIN_DIR . 'readme.txt';
		if ( ! is_readable( $readme_file ) || ! class_exists( 'PucReadmeParser' ) ) {
			return $result;
		}

		$parser = new \PucReadmeParser();
		$readme = $parser->parse_readme( $readme_file );
		if ( empty( $readme['sections'] ) ) {
			return $result;
		}

		// The readme parser files the FAQ under its long heading; WordPress's
		// details window calls that tab "faq".
		$wanted = array(
			'description'                => 'description',
			'installation'               => 'installation',
			'frequently_asked_questions' => 'faq',
			'changelog'                  => 'changelog',
		);

		$sections = array();
		foreach ( $wanted as $from => $to ) {
			if ( ! empty( $readme['sections'][ $from ] ) ) {
				$sections[ $to ] = $readme['sections'][ $from ];
			}
		}

		$info = new \stdClass();

		$info->name         = GPR_PLUGIN_NAME;
		$info->slug         = self::SLUG;
		$info->version      = GPR_VERSION;
		$info->author       = '<a href="' . esc_url( GPR_AUTHOR_URI ) . '">' . esc_html( GPR_AUTHOR_NAME ) . '</a>';
		$info->homepage     = GPR_PLUGIN_URI;
		$info->requires     = GPR_REQUIRES_AT_LEAST;
		$info->tested       = GPR_TESTED_UP_TO;
		$info->requires_php = GPR_REQUIRES_PHP;
		$info->last_updated = gmdate( 'Y-m-d H:i:s', (int) filemtime( GPR_PLUGIN_FILE ) );
		$info->sections     = $sections;
		$info->icons        = array(
			'1x' => GPR_PLUGIN_URL . 'assets/icon-128x128.png',
			'2x' => GPR_PLUGIN_URL . 'assets/icon-256x256.png',
		);

		if ( is_readable( GPR_PLUGIN_DIR . 'assets/images/plugin-banner.png' ) ) {
			$info->banners = array(
				'low'  => GPR_PLUGIN_URL . 'assets/images/plugin-banner.png',
				'high' => GPR_PLUGIN_URL . 'assets/images/plugin-banner.png',
			);
		}

		return $info;
	}
}
