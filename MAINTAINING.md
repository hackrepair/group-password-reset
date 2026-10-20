# Maintaining Group Password Reset

This plugin is distributed through GitHub releases rather than WordPress.org. The repository should still stay close to WordPress plugin expectations so that future review or distribution changes do not require a large cleanup pass.

Current release: **3.6.1**.

## Baseline

- WordPress compatibility floor: `6.9.4`
- WordPress tested up to: `7.1.3`
- PHP minimum: `7.4` (code stays 7.4-compatible; checked with PHPCompatibilityWP `testVersion 7.4-` and a PHP 7.4 syntax lint)
- Current packaging target: `group-password-reset.zip`
- Zip root directory: `group-password-reset/`
- Update source: this repository's GitHub releases, checked by the vendored Plugin Update Checker (`plugin-update-checker/`, MIT) through `includes/class-gpr-updater.php`

## Repo Standards

- Run WordPress coding standards checks with:
  - `/Volumes/BACKUP8TB2/plugins/PHPCS/run-phpcs.sh "$PWD"`
- Auto-fix safe PHP formatting changes with:
  - `phpcbf --standard=phpcs.xml.dist group-password-reset.php includes`
- Re-run syntax checks before release:
  - `php -l group-password-reset.php`
  - `php -l includes/admin-menu.php`
  - `php -l includes/admin-views.php`
  - `php -l includes/password-reset.php`

## Manual QA Checklist

- Confirm the plugin activates cleanly with no PHP notices or warnings.
- Confirm the plugin row on `plugins.php` shows `View details` exactly once, and that it opens the details window (filled from `readme.txt`).
- Confirm a role-based reset works with JavaScript enabled.
- Confirm an all-users reset works with one or more excluded usernames.
- Confirm excluded usernames are skipped; only the latest 20 per-user rows remain visible, while summary counts cover the entire run.
- Confirm a no-match role finishes cleanly without getting stuck.
- Confirm the non-JavaScript fallback path works through `admin-post.php`.
- Confirm summary totals and per-user statuses stay consistent between async and fallback runs.
- Confirm reset emails are attempted and that failure states are reported cleanly when mail is unavailable.

## Reviewed Security Reports

- A 2026 follow-up security report was reviewed and recorded as `no action required`.
- The password-reset-before-email finding is accurate as an availability tradeoff, but it is already intentional and explicitly documented in the admin UI and docs.
- The missing audit-log IP address finding was treated as an optional forensic enhancement, not a required security fix.
- The AJAX response PII finding was judged overstated because the endpoints are nonce-protected, `manage_options`-gated, and scoped to the owning admin's job.
- The suggested remediation of emailing a reset key before calling `wp_set_password()` is not suitable for WordPress because `wp_set_password()` clears `user_activation_key` and would invalidate the emailed reset key.

## Security Notes

### Reporting a Vulnerability

- Do not ask reporters to open public GitHub issues for security vulnerabilities.
- Prefer GitHub private vulnerability reporting for the repository:
  - https://github.com/hackrepair/group-password-reset/security/advisories/new
- GitHub private reporting documentation:
  - https://docs.github.com/en/code-security/how-tos/report-and-fix-vulnerabilities/privately-reporting-a-security-vulnerability
- If private reporting is unavailable, handle reports through a private maintainer-controlled channel and move discussion out of public threads immediately.
- Ask reporters to include:
  - affected version
  - reproduction steps
  - required privileges or preconditions
  - impact summary
  - proposed fix or mitigation if they have one

### Security Model

- This plugin is an administrator-only WordPress utility.
- Sensitive actions are expected to require:
  - `manage_options`
  - a valid WordPress nonce
  - ownership of the async reset job for continuation requests
- The plugin should never expose reset functionality to unauthenticated users or lower-privileged roles.
- Bulk password reset is intentionally a high-impact administrative action, so security reviews should distinguish:
  - true access-control flaws
  - operational risk if an administrator account is already compromised

### Sensitive Data Handling

- Do not persist row-level reset results longer than required for the active run.
- Completed runs should retain summary-only data wherever possible.
- Do not store raw API keys, passwords, or reset links in plugin options, transients, logs, or audit records.
- Avoid storing target usernames or email addresses in audit records.
- If future features need server-side persistence of user-level result data, document exactly:
  - what fields are stored
  - where they are stored
  - how long they persist
  - how they are cleared

### Secrets and Configuration

- Never commit `.env`, local config overrides, or tokens to the repository.
- Any future secret-bearing configuration file must be gitignored by default.
- Do not log secrets to PHP error logs, browser console output, or audit tables.
- If a secret is ever committed accidentally, rotate it immediately and remove it from active use.

### Audit Logging Expectations

- Audit logging should record:
  - who started the reset
  - the selected scope
  - whether notifications were attempted
  - summary counts for success, failed, and skipped results
  - start and completion events
- Audit logging should not record:
  - target passwords
  - reset keys or links
  - target email addresses
  - target username lists
- Audit data should survive plugin deactivation unless an explicit uninstall policy says otherwise.
- If retention requirements change, add scheduled pruning rather than silently deleting security-relevant history.

### Mail and Reset Behavior

- The plugin intentionally resets the password before attempting to send the WordPress reset email.
- This means mail delivery failure can leave users locked out until an administrator communicates through another channel.
- Keep the admin UI warning and documentation aligned with that behavior.
- Do not implement a naive "email reset link first, then call `wp_set_password()`" flow without accounting for WordPress core invalidating existing reset keys.

### Security Review Guardrails

- Treat admin-only destructive capability as an operational risk unless a real privilege-boundary bypass exists.
- Do not classify `network_site_url()`, `admin_url()`, `home_url()`, or static outbound links as SSRF without a real outbound server-side request sink.
- Do not classify transient TTL usage as a memory leak.
- Before accepting any report that claims sensitive data is persisted, verify the exact stored payload and not just the active in-memory or AJAX response structure.
- Prefer one verified finding over several speculative ones.

### Dependency and Release Hygiene

- Re-run `/Volumes/BACKUP8TB2/plugins/PHPCS/run-phpcs.sh "$PWD"` before release.
- Re-run syntax checks before release:
  - `php -l group-password-reset.php`
  - `php -l includes/admin-menu.php`
  - `php -l includes/admin-views.php`
  - `php -l includes/password-reset.php`
  - `php -l includes/audit-log.php`
- Re-run the WordPress Plugin Check plugin before release packaging.
- Rebuild the release zip only from the final reviewed plugin directory contents.
- If a release changes security-sensitive behavior, update:
  - `README.md`
  - `readme.txt`
  - the admin UI copy where relevant
  - this maintainer document

## Release Checklist

- Update plugin header metadata in `group-password-reset.php` if compatibility or version values changed.
- Update `readme.txt` and `README.md` so requirements, behavior, and changelog stay aligned.
- Confirm `readme.txt` (Requires at least, Tested up to, Requires PHP, Stable tag) matches the plugin header and the `GPR_` constants.
- Run WordPressCS and PHP syntax checks.
- Review on Zendogs at `http://localhost:8081/wp-admin/tools.php?page=group-password-reset`; use only designated disposable users for actual reset tests.
- Build an installable archive whose filename is `group-password-reset.zip`.
- Verify the archive extracts to a top-level `group-password-reset/` directory.
- Attach the installable zip to the GitHub release and use that asset in documentation links.

### Updater Rules

- The release tag must be `v` plus the plugin version, for example `v3.5.1`, and must match `Version:` in `group-password-reset.php`, `GPR_VERSION`, and `Stable tag:` in `readme.txt`.
- The release must carry an uploaded asset named exactly `group-password-reset.zip`. The updater ignores any other asset and never uses GitHub's auto-generated source archive.
- Pre-releases are skipped, so mark test builds as pre-releases.
- The updater is deliberately not gated by `is_admin()`; update checks and auto-updates run on cron.
- `Update URI` in the plugin header must stay the repository URL.
- Keep `plugin-update-checker/` as an unmodified copy of the upstream library. To upgrade, replace the whole folder, keep it excluded from PHPCS, and re-test update detection.
- Update discovery, metadata, and package downloads contact GitHub-hosted services. Reset emails use WordPress’s configured mail transport. Keep readme.txt External services accurate.

## Local Environment Notes

- Current review fixture: Zendogs, `http://localhost:8081/wp-admin/tools.php?page=group-password-reset`.
- Current release: 3.6.1. Inspect the actual installed header before future work.
- The prior Studio environment at `/Users/JW/Studio/my-wordpress-website` is a historical reference, not the designated current review fixture.
- Keep user-facing compatibility metadata separate from the versions actually exercised in this review. Full reset/mail and cross-version tests remain release gates.

## 3.5.0 text and interface checks

- Page sections: Choose accounts; Notifications and confirmation; How password resets work; Latest results.
- Initial scope is Select a scope; no scope is preselected.
- Completion text is **Done! Password reset complete.** The JavaScript status is bold; server-rendered success notices append result counts.
- Completion means processing ended; review success, failure, and skipped counts. Email acceptance is not proof of delivery.
- Preserve the per-user qualification when passwords changed but notification failed.
- Review desktop/mobile, no-JavaScript form, and PHP/JavaScript summary consistency without triggering resets on real users.
- Run focused tests with `php tests/admin-notification.php` and `php tests/update-notices.php`, syntax checks, and the shared PHPCS wrapper before packaging with `python3 scripts/build-release.py`.
- Include a Codex Security diff-review reminder before release; do not run that plugin automatically from these notes.

## Administrator summary notifications (3.5.0)

After a completed reset, use **Notify administrators** to select current administrator accounts, add an optional note, preview the summary, and choose **Send email**. Recipients are not selected automatically. This manual notification works independently of the affected-user email setting, including reset-only mode.

The server keeps the initiating administrator’s latest summary for 15 minutes. A newer completed run replaces it. The email includes site URL, completion time, scope, counts, exclusions count, and notification mode; it excludes passwords, reset links, usernames, and individual email addresses. A personal note is included exactly as sanitized text, so avoid adding sensitive details.

Sending requires administrator permission and a valid nonce. Recipients are checked against current administrator accounts (up to 100 shown); repeat sends are throttled for one minute. Each recipient receives a separate email. Feedback distinguishes acceptance for sending from failure and does not guarantee delivery. Both JavaScript and regular form submission are supported.

Latest results retains only the most recent 20 per-user rows in the current run. Older displayed rows are removed as new results arrive. Summary counts and administrator summary emails continue to cover the whole run. Detailed rows are not persisted after the run.

## 3.5.1 email and recipient checks

- Check the full-width HTML layout, personalized greeting, escaped note/scope/notification content, result table, conditional failure guidance, dashboard button, and privacy footer. Each HTML email must include a plain-text alternative; remove the temporary PHPMailer hook after sending.
- Check `[domain] [Group Password Reset]` tags in both email subjects. User reset-link messages remain plain text.
- Check the tinted panel, two-column desktop/single-column mobile recipient list, All administrators select/clear and indeterminate states, muted addresses, full-width note field, subject preview, and Send email button. Recipients default to unselected.
- Verify nonce/permission checks, summary ownership, replaced/expired summaries, current-recipient validation, the one-minute throttle, mail failures, and no target-account data in summary storage or email.
- Summary preview is plain text; the sent administrator email is HTML plus a plain-text alternative. The summary lives for 15 minutes; the one-use results flash is separate.
- Local Mailpit setup is documented outside the release tree at `/Volumes/BACKUP8TB2/plugins/local-mailpit/README.md`. Capture is fixture-only and does not establish external delivery or email-client compatibility.
- Use the existing updater instance when checking live metadata. Creating another instance for the same slug triggers the library's duplicate-slug error.
- Preserve published ZIPs and tags. Repository-only documentation corrections are included in the next release package; do not silently overwrite a published asset.

Release 3.5.4: ZIP files are normalized to Unix regular-file mode 0644, with an isolated 0600-source regression test (`python3 tests/package-permissions.py`) and build-time verification. Preserve old release ZIPs. Consider the existing Codex Security review reminder separately.
