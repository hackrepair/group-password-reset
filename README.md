# The Hack Repair Guy's Group Password Reset

Bulk reset WordPress user passwords by scope, skip excluded usernames, and optionally notify affected users with secure password reset links.

## Requirements

- WordPress 6.8.3 or newer
- PHP 7.4 or newer
- Administrator access to the target site

## Features

- Reset passwords for all users, a single role, or `Administrator (excluding current user)`
- Persist excluded usernames between runs
- Show skipped users in the active results table
- Send secure password reset links by email instead of exposing raw passwords
- Support a reset-only mode that skips email when admins need to communicate through another channel
- Support both JavaScript-enhanced batch processing and a chunked non-JavaScript fallback
- Store async progress per reset run so multiple tabs do not overwrite each other
- Persist only summary counts after completion instead of storing row-level results server-side
- Write audit-log entries for reset job creation and completion
- Check this plugin's own GitHub releases for updates, so the Plugins screen offers a one-click update

## How It Works

1. Open the **Group Password Reset** admin screen.
2. Choose **All users**, a specific role, or **Administrator (excluding current user)**.
3. Enter comma-separated usernames to exclude.
4. Choose whether to attempt email notifications or run in reset-only mode.
5. Confirm that you want to invalidate current passwords.
6. Start the reset.

With JavaScript enabled, the plugin processes users in batches of 20 and updates the results table in place. Without JavaScript, the form submits through WordPress `admin-post.php`, processes the reset in redirecting batches, and returns to the admin screen with the final summary.

When email mode is enabled, the plugin resets passwords first and then attempts to send secure reset-link emails. Mail failures do not roll back the reset, so the no-email mode is the safer option when admins plan to communicate manually.

## Architecture Notes

- `group-password-reset.php` is the bootstrap file and loads the admin and reset subsystems.
- `includes/admin-menu.php` owns the admin screen, asset registration, and the non-JavaScript form handler.
- `includes/password-reset.php` owns shared reset logic, role-scope handling, summary-backed job state, result formatting, and AJAX endpoints.
- `includes/audit-log.php` owns the audit table schema and event logging.
- `includes/class-gpr-updater.php` points WordPress at this plugin's GitHub releases and fills the update details window from `readme.txt`.
- `plugin-update-checker/` is the vendored third-party update library (see Updates below).
- `assets/js/admin.js` progressively enhances the admin screen with async batching.
- `assets/css/admin.css` keeps the UI aligned with a native WordPress admin layout.

## Updates

This plugin is not on the WordPress.org repository, so it checks its own GitHub releases (`hackrepair/group-password-reset`) the way WordPress checks wordpress.org for everything else. When a new version is published there, the Plugins screen shows an "update available" notice and offers a one-click update.

- Only a release whose tag is `v` plus the version (for example `v3.3.0`) and which carries an uploaded `group-password-reset.zip` asset is offered. GitHub's auto-generated source archive is never used, because it would rename the plugin folder and deactivate the plugin.
- Pre-releases are skipped.
- The only outside call is the periodic request to `api.github.com`. Nothing about your site is sent, and there is no telemetry.
- The update library is [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker) by Janis Elsts (MIT), vendored in `plugin-update-checker/`. It is excluded from PHPCS because it is not our code.

## Maintainer Notes

See [CONTRIBUTING.md](CONTRIBUTING.md) for contribution guidelines and [MAINTAINING.md](MAINTAINING.md) for the repository QA checklist, WordPressCS commands, release packaging rules, and local test environment notes.

## Support

- Issues and feature requests: [GitHub issues](https://github.com/hackrepair/group-password-reset/issues)
- Security reports: [report privately](https://github.com/hackrepair/group-password-reset/security/advisories/new) (see [SECURITY.md](SECURITY.md))
- Reddit support/discussion thread: [Group Password Reset support post](https://www.reddit.com/user/hackrepair/comments/1s8a24b/new_plugin_group_password_reset_bulk_password/)

## Testing Notes

Baseline compatibility target:

- WordPress 6.9
- PHP 7.4 (minimum) and PHP 8.3

Recommended manual scenarios:

- Reset a single role with multiple users.
- Reset `Administrator (excluding current user)` and verify the current admin is skipped automatically.
- Reset all users while excluding one or more usernames.
- Run the tool with a role that has no users.
- Trigger a run with JavaScript disabled to verify the fallback path.
- Verify results distinguish `success`, `failed`, and `skipped`.
- Verify the audit log records start and completion events without storing target usernames or emails.
- Verify completed runs retain counts-only summary data after reload.

## Release Checklist

- Update the plugin version and changelog.
- Verify `readme.txt` matches the plugin header and current behavior.
- Confirm the admin screen loads without PHP notices.
- Confirm the reset flow works on WordPress 6.9 with PHP 7.4 and with PHP 8.3, plus any newer local prerelease builds used for forward-compatibility checks.

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).
