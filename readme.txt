===== The Hack Repair Guy's Group Password Reset =====

Contributors: hackrepair, tvcnet
Donate link: https://hackrepair.com/donations/buy-jim-a-coffee
Tags: password reset, bulk actions, user management, security
Requires at least: 6.9.4
Tested up to: 7.1.3
Stable tag: 3.4.2
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Bulk reset WordPress passwords by scope, skip excluded usernames, and send secure reset links to affected users.

== Description ==

The Hack Repair Guy's Group Password Reset is a native WordPress admin tool for administrators who need to invalidate passwords at scale without manually editing each account.

Use it to:

- reset passwords for a single role
- reset passwords for all users
- reset administrator accounts while automatically excluding the current administrator
- exclude specific usernames from a run
- attempt to notify each affected user with a secure password reset link
- run in reset-only mode without sending email
- review success, failure, and skipped summaries after each run
- retain a durable audit trail for security-sensitive operations

The plugin supports two execution paths:

- JavaScript-enabled browsers process users in batches of 20 and update the results table in place.
- Browsers without JavaScript fall back to a chunked WordPress `admin-post.php` flow that redirects between batches and returns to the admin screen with the same final summary data.

Operational notes:

- Detailed per-user result rows are shown during the active JavaScript run, but only summary counts are persisted after completion.
- When email mode is enabled, passwords are invalidated before reset-link emails are attempted. Mail failures do not roll back the reset.

Compatibility baseline:

- WordPress 6.9.4 or newer (tested up to 7.1.3)
- PHP 7.4 or newer

== Installation ==

1. Download `group-password-reset.zip` from the [GitHub releases page](https://github.com/hackrepair/group-password-reset/releases) and upload it under Plugins > Add New Plugin, or upload the plugin folder to `/wp-content/plugins/`.
2. Activate the plugin through the `Plugins` screen in WordPress.
3. Open `Group Password Reset` from the WordPress admin menu.
4. Choose `All users`, a specific role, or `Administrator (excluding current user)`.
5. Enter any excluded usernames, confirm the reset, and start the run.

== Updates ==

This plugin is not distributed on WordPress.org. It checks its own GitHub releases and shows the usual "update available" notice on the Plugins screen. See "External services" below.

== External services ==

This plugin asks GitHub whether a newer release exists, the same way WordPress asks wordpress.org about other plugins.

* **GitHub API** (`api.github.com`): the latest published release of `hackrepair/group-password-reset`, so the Plugins screen can offer an update. Nothing about your site or its users is sent. [Terms of Service](https://docs.github.com/en/site-policy/github-terms/github-terms-of-service) and [Privacy Statement](https://docs.github.com/en/site-policy/privacy-policies/github-general-privacy-statement).

The plugin makes no other outside requests.

== Support ==

For support, feedback, or discussion, visit:

https://www.reddit.com/user/hackrepair/comments/1s8a24b/new_plugin_group_password_reset_bulk_password/

== Frequently Asked Questions ==

= Can I reset passwords for only one role? =

Yes. Choose a specific role from the dropdown before starting the reset.

= Can I reset all users at once? =

Yes. Leave the role selector on `All users`.

= Can I reset administrator accounts without locking myself out? =

Yes. Choose `Administrator (excluding current user)` to target administrator accounts while automatically skipping the currently logged-in administrator.

= What happens to excluded usernames? =

Excluded usernames are skipped, shown in the results table, and persisted for the next run.

= What email do users receive? =

Each affected user receives a secure WordPress password reset link so they can choose a new password themselves.

= What happens if email delivery fails? =

Passwords are still reset first. If your mail configuration fails, users may be locked out until they receive a reset link through another channel. Use the no-email option if you plan to communicate manually.

= How do updates work? =

This plugin is not distributed on the WordPress.org repository, so it checks its own GitHub releases instead. When a new version is published there, the Plugins screen shows an "update available" notice and offers a one-click update. Nothing about your site is sent.

= Does the plugin require JavaScript? =

No. JavaScript enables the batched in-page progress UI, but the main reset flow also works through a non-JavaScript fallback.

== Changelog ==

= 3.4.2 =
* Match the compact companion-plugin cards and sidebar layout used by Disable Other Plugin Notices.
* Display the installed version in View Details.

= 3.4.1 =
* Restore bundled updater parser dependencies required by View Details.

= 3.4.0 =
* Added an Other THRG Plugins tab to View Details with companion tools and project links.

= 3.3.0 =

- Fix: The Plugins screen showed "View details" twice. It now shows once, and opens the details window built from this readme.
- Change: Requires WordPress 6.9.4 or newer, tested up to 7.1.3 (same baseline as the Hack Repair Guy's Plugin Detective).
- Change: Removed the separate built-in details popup (its release notes were outdated); the standard WordPress details window is used instead.
- Change: Now runs on PHP 7.4 and newer (was PHP 8.3). No code changes were needed; the code was already 7.4-compatible, so only the declared minimum, the docs and the compatibility checks changed.
- New: Checks this plugin's GitHub releases for updates, so the Plugins screen offers a one-click update. Only a release tagged `v` plus its version with an uploaded `group-password-reset.zip` is offered.
- New: Vendored the Plugin Update Checker library (MIT) in `plugin-update-checker/`.
- New: The update details window falls back to this plugin's own readme when GitHub cannot be reached.

= 3.2.0 =

- Expanded maintainer security guidance with reporting, audit, secret-handling, and review guardrail notes.
- Kept release hygiene and maintainer documentation aligned with the current security model.

= 3.1.0 =

- Added a durable audit log table for reset start and completion events.
- Reduced persisted sensitive data by keeping only summary counts after runs complete.
- Added clearer warnings around email-mode mail failures and the safer no-email workflow.
- Improved release hygiene and admin asset cache-busting for updated builds.

= 3.0.0 =

- Modernized the admin experience with a native WordPress layout.
- Added a non-JavaScript fallback for the reset workflow.
- Reworked batching and job state handling for safer progress tracking.
- Added an `Administrator (excluding current user)` scope.
- Improved result reporting with success, failed, and skipped statuses.
- Updated documentation and compatibility targets.

= 2.2.0 =

- Added feature to exclude specific usernames from group password reset.

= 2.1.0 =

- Updated readme files and name.
