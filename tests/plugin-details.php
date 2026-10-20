<?php
/** Standalone regression for own-plugin banner enrichment. */
define('ABSPATH', __DIR__ . '/');
define('GPR_PLUGIN_DIR', dirname(__DIR__) . '/');
define('GPR_PLUGIN_URL', 'https://example.test/wp-content/plugins/group-password-reset/');
define('GPR_PLUGIN_FILE', GPR_PLUGIN_DIR . 'group-password-reset.php');
define('GPR_VERSION', 'test');
function add_filter(...$args) {}
function wp_enqueue_style(...$args) {}
function plugins_url($path, $file) { return GPR_PLUGIN_URL . $path; }
require GPR_PLUGIN_DIR . 'includes/companion-plugins.php';
$info = (object) array('sections'=>array('description'=>'Keep this'));
$result = gpr_companion_plugin_info($info, 'plugin_information', (object) array('slug'=>'group-password-reset'));
if ($result->sections['description'] !== 'Keep this' || $result->banners['high'] !== GPR_PLUGIN_URL . 'assets/images/plugin-banner.png' || $result->banners['low'] !== $result->banners['high']) {
    throw new RuntimeException('Own-plugin metadata did not preserve description and restore banner.');
}
$other = (object) array('sections'=>array('description'=>'Other'));
gpr_companion_plugin_info($other, 'plugin_information', (object) array('slug'=>'other'));
if (isset($other->banners)) { throw new RuntimeException('Other-plugin metadata changed.'); }
if (false !== gpr_companion_plugin_info(false, 'plugin_information', (object) array('slug'=>'group-password-reset'))) {
    throw new RuntimeException('Empty response changed.');
}
echo "PASS: own-plugin banner, retained description, unrelated plugin and empty response.\n";
