===== The Hack Repair Guy's Group Password Reset =====

Contributors: hackrepair, tvcnet
Donate link: https://hackrepair.com/donations/buy-jim-a-coffee
Tags: password reset, bulk actions, user management, security
Requires at least: 6.9.4
Tested up to: 7.1.3
Stable tag: 3.5.1
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
3. Open Tools > Group Password Reset. Under Choose accounts, use Accounts to reset to select a scope. No scope is selected by default.
4. Choose `All users`, a specific role, or `Administrator (excluding current user)`.
5. Enter any excluded usernames. Under Notifications and confirmation, choose whether to skip emails, acknowledge the password change, and select Reset Passwords.
6. Review progress and Latest results. The in-page completion message is **Done! Password reset complete.**, displayed in bold. Always review the result counts and details.

== Updates ==

This plugin is not distributed on WordPress.org. It checks its own GitHub releases and shows the usual "update available" notice on the Plugins screen. See "External services" below.

== External services ==

This plugin asks GitHub whether a newer release exists, the same way WordPress asks wordpress.org about other plugins.

* **GitHub API** (`api.github.com`): the latest published release of `hackrepair/group-password-reset`, so the Plugins screen can offer an update. Account lists, passwords, and reset links are not included. Standard connection information, such as the server IP and HTTP headers, is visible to GitHub. [Terms of Service](https://docs.github.com/en/site-policy/github-terms/github-terms-of-service) and [Privacy Statement](https://docs.github.com/en/site-policy/privacy-policies/github-general-privacy-statement).

Update metadata and release downloads can also use GitHub-hosted URLs. Password-reset notifications use the site’s WordPress email configuration. No additional telemetry is added.

== Support ==

For support, feedback, or discussion, visit:

https://www.reddit.com/user/hackrepair/comments/1s8a24b/new_plugin_group_password_reset_bulk_password/

== Frequently Asked Questions ==

= Can I notify administrators after a reset? =

Yes. In Notify administrators, select administrator accounts, optionally add a note, preview the summary, and choose Email to administrators. This works independently of reset-link emails to affected users. Only your latest completed summary is available, for 15 minutes. Each recipient receives a separate email; acceptance for sending does not guarantee delivery. Wait one minute between sends.


= Can I reset passwords for only one role? =

Yes. Choose a specific role from the dropdown before starting the reset.

= Can I reset all users at once? =

Yes. Explicitly select `All users` under Accounts to reset. The initial Select a scope placeholder does not select any accounts.

= Can I reset administrator accounts without locking myself out? =

Yes. Choose `Administrator (excluding current user)` to target administrator accounts while automatically skipping the currently logged-in administrator.

= What happens to excluded usernames? =

Excluded usernames are skipped, shown in the results table, and persisted for the next run.

= What email do users receive? =

When notifications are enabled, the plugin attempts to email each affected user a secure WordPress password reset link. No-email mode skips those attempts. Acceptance for sending does not guarantee delivery.

= What happens if email delivery fails? =

Passwords are still reset first. If your mail configuration fails, users may be locked out until they receive a reset link through another channel. Use the no-email option if you plan to communicate through another channel.

= How do updates work? =

This plugin is not distributed on the WordPress.org repository, so it checks its own GitHub releases instead. When a new version is published there, the Plugins screen shows an "update available" notice and offers a one-click update. Update requests do not include account lists, passwords, or reset links.

= Why can an update check be temporarily unavailable? =

GitHub may be unreachable or may limit requests from the hosting server’s IP address. The plugin explains the failure and shows a retry time when GitHub provides usable rate-limit information. Technical details are available for troubleshooting. This does not affect password-reset features.

= What does the completion message mean? =

**Done! Password reset complete.** means the in-page run has finished processing. Review the summary and each account’s result: failed rows can include a password change followed by a reset-link or email failure. Email acceptance does not confirm delivery.

= Does the plugin require JavaScript? =

No. JavaScript enables the batched in-page progress UI, but the main reset flow also works through a non-JavaScript fallback.

== Changelog ==

= 3.5.1 =
* Add a full-width branded HTML administrator summary email with result table, personal note, failure guidance, dashboard button, and plain-text alternative.


= 3.5.0 =
* Format administrator summary emails with WordPress-style paragraphs, greeting, site information, results, follow-up guidance, and dashboard link.
* Limit Latest results to the most recent 20 per-user rows, removing older rows automatically while retaining full-run summary counts.
* Identify both user reset emails and administrator summaries with [domain] [Group Password Reset] subject tags; include the site URL in reset email text.
* Add a Notify administrators container: select recipients, preview a completed summary, add a personal note, and send manually. Summaries expire after 15 minutes and exclude passwords, reset links, and per-user results.
* Match Site Inspector with a branded header, orange section bars, and clearer reset and results cards.
* Preserve explicit scope selection, password acknowledgement, notification choices, and batched processing.
* Display **Done! Password reset complete.** in bold and clarify email acceptance and result wording.

= 3.4.3 =
* Explain temporary update-check failures clearly and move diagnostic errors into Technical details.
* Show a retry time for confirmed GitHub rate limits.

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
