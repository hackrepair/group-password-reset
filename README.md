<h1 align="center">The Hack Repair Guy's Group Password Reset</h1>

<p align="center">
  <strong>Bulk reset WordPress user passwords by scope, skip excluded usernames, and optionally notify affected users with secure password reset links.</strong><br>
  By Jim Walker, The Hack Repair Guy
</p>

<p align="center">
  <img src="https://img.shields.io/badge/version-3.5.0-b34700?style=flat-square" alt="Version 3.5.0">
  <img src="https://img.shields.io/badge/WordPress-6.9.4%2B-21759b?style=flat-square&logo=wordpress&logoColor=white" alt="WordPress 6.9.4+">
  <img src="https://img.shields.io/badge/tested%20up%20to-7.1.3-21759b?style=flat-square" alt="Tested up to WordPress 7.1.3">
  <img src="https://img.shields.io/badge/PHP-7.4%2B-777bb4?style=flat-square&logo=php&logoColor=white" alt="PHP 7.4+">
  <img src="https://img.shields.io/badge/license-GPLv2%2B-2e7d32?style=flat-square" alt="License GPLv2 or later">
</p>

<p align="center">
  <a href="https://github.com/hackrepair/group-password-reset/releases/latest"><img src="https://img.shields.io/badge/DOWNLOAD%20PLUGIN-b34700?style=for-the-badge" alt="DOWNLOAD PLUGIN" height="64"></a>
</p>

---

**Current release: 3.5.0.** See the [release notes](releases/v3.5.0.md) for changes and validation scope.

## Requirements

| | Minimum | Tested up to |
|---|---|---|
| WordPress | 6.9.4 | 7.1.3 |
| PHP | 7.4 | 8.3 |

Administrator access to the target site is also required.

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

1. Open **Tools → Group Password Reset**. Under **Choose accounts**, use **Accounts to reset** to select a scope. No scope is selected by default.
2. Choose **All users**, a specific role, or **Administrator (excluding current user)**.
3. Enter comma-separated usernames to exclude.
4. Choose whether to attempt email notifications or run in reset-only mode.
5. Confirm that you want to invalidate current passwords.
6. Select **Reset Passwords**, then review progress and **Latest results**. The in-page completion message is **Done! Password reset complete.**, displayed in bold. Check the result counts and details for failures or skipped accounts.

With JavaScript enabled, the plugin processes users in batches of 20 and updates the results table in place. Without JavaScript, the form submits through WordPress `admin-post.php`, processes the reset in redirecting batches, and returns to the admin screen with the final summary.

When email mode is enabled, the plugin resets passwords first and then attempts to send secure reset-link emails. Mail failures do not roll back the reset, so choose no-email mode when you plan to communicate through another channel.

## Architecture Notes

- `group-password-reset.php` is the bootstrap file and loads the admin and reset subsystems.
- `includes/admin-menu.php` owns the admin screen, asset registration, and the non-JavaScript form handler.
- `includes/password-reset.php` owns shared reset logic, role-scope handling, summary-backed job state, result formatting, and AJAX endpoints.
- `includes/audit-log.php` owns the audit table schema and event logging.
- `includes/class-gpr-updater.php` points WordPress at this plugin's GitHub releases and fills the update details window from `readme.txt`.
- `plugin-update-checker/` is the vendored third-party update library (see Updates below).
- `assets/js/admin.js` progressively enhances the admin screen with async batching.
- `assets/css/admin.css` applies the Site Inspector design family to this plugin’s admin page.

## Updates

This plugin is not on the WordPress.org repository, so it checks its own GitHub releases (`hackrepair/group-password-reset`) the way WordPress checks wordpress.org for everything else. When a new version is published there, the Plugins screen shows an "update available" notice and offers a one-click update.

- Only a release whose tag is `v` plus the version (for example `v3.5.0`) and which carries an uploaded `group-password-reset.zip` asset is offered. GitHub's auto-generated source archive is never used, because it would rename the plugin folder and deactivate the plugin.
- Pre-releases are skipped.
- Update discovery and metadata retrieval contact GitHub; installation downloads the published ZIP. Account lists, passwords, and reset links are not included in update requests. These requests expose standard connection information, such as the server IP and HTTP headers. There is no added telemetry.
- Manual update failures explain temporary availability problems. Confirmed GitHub rate limits show a retry time; detailed diagnostics are collapsed. This reporting does not enforce a retry cooldown.
- The update library is [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker) by Janis Elsts (MIT), vendored in `plugin-update-checker/`. It is excluded from PHPCS because it is not our code.

## Maintainer Notes

See [CONTRIBUTING.md](CONTRIBUTING.md) for contribution guidelines and [MAINTAINING.md](MAINTAINING.md) for the repository QA checklist, WordPressCS commands, release packaging rules, and local test environment notes.

## Support

- Issues and feature requests: [GitHub issues](https://github.com/hackrepair/group-password-reset/issues)
- Security reports: [report privately](https://github.com/hackrepair/group-password-reset/security/advisories/new) (see [SECURITY.md](SECURITY.md))
- Reddit support/discussion thread: [Group Password Reset support post](https://www.reddit.com/user/hackrepair/comments/1s8a24b/new_plugin_group_password_reset_bulk_password/)

## Testing Notes

Baseline compatibility target:

- WordPress 6.9.4 (minimum) through 7.1.3
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
- Keep the bold completion text **Done! Password reset complete.** aligned with the localized JavaScript string and completion styling.
- Confirm the admin screen loads without PHP notices.
- Confirm the reset flow works on WordPress 6.9.4 and 7.1.x with PHP 7.4 and with PHP 8.3, plus any newer local prerelease builds used for forward-compatibility checks.

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).

## 3.4.0

View Details now includes **Other THRG Plugins**, a guide to the three companion tools with descriptions and project links.

## 3.4.1

Corrected release packaging to retain the complete bundled updater, including the View Details parser dependencies.

## 3.4.2

View Details uses the approved compact companion-plugin cards, with content contained beside the sidebar and the installed version displayed.

## 3.4.3

Update-check failures now use plain language and clarify that plugin features continue to work. Confirmed GitHub rate limits include a retry time in the site timezone. Technical diagnostics are collapsed. This changes reporting; retry scheduling is unchanged.

## 3.5.0 — interface refresh

Matches the Site Inspector design family with a THRG mascot header, orange section bars, clearer account and notification sections, and status-colored summary tiles. Existing scope selection, exclusions, acknowledgement, notification defaults, reset batching, and server-side fallback remain in place.

The 3.5.0 refresh includes **Choose accounts**, **Notifications and confirmation**, **How password resets work**, and **Latest results**. The completion message **Done! Password reset complete.** is bold.

## Administrator summary notifications (3.5.0)

After a completed reset, use **Notify administrators** to select current administrator accounts, add an optional note, preview the summary, and choose **Email to administrators**. Recipients are not selected automatically. This manual notification works independently of the affected-user email setting, including reset-only mode.

The server keeps the initiating administrator’s latest summary for 15 minutes. A newer completed run replaces it. The email includes site URL, completion time, scope, counts, exclusions count, and notification mode; it excludes passwords, reset links, usernames, and individual email addresses. A personal note is included exactly as sanitized text, so avoid adding sensitive details.

Sending requires administrator permission and a valid nonce. Recipients are checked against current administrator accounts (up to 100 shown); repeat sends are throttled for one minute. Each recipient receives a separate email. Feedback distinguishes acceptance for sending from failure and does not guarantee delivery. Both JavaScript and regular form submission are supported.

Email identification: both plugin email subjects now use `[domain] [Group Password Reset] Password Reset` or `[domain] [Group Password Reset] Password Reset Summary`. The domain comes from the site's configured home URL, preserving subdomains and local ports while omitting scheme and path. User reset messages also identify the site URL in their text. This adapts WordPress core's bracketed site-name convention to a domain prefix.

Latest results retains only the most recent 20 per-user rows in the current run. Older displayed rows are removed as new results arrive. Summary counts and administrator summary emails continue to cover the whole run. Detailed rows are not persisted after the run.

Administrator summaries use a WordPress-style plain-text message with greeting, explanatory paragraphs, labeled site/time/scope, optional sender note, spaced results and notification sections, conditional failure guidance, dashboard link, and closing. Domain/plugin subject tags are preserved.
