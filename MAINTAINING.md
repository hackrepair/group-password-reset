# Maintaining Group Password Reset

This plugin is distributed through GitHub releases rather than WordPress.org. The repository should still stay close to WordPress plugin expectations so that future review or distribution changes do not require a large cleanup pass.

## Baseline

- WordPress compatibility floor: `6.8.3`
- WordPress tested up to: `6.9.4`
- PHP minimum: `8.3`
- Current packaging target: `group-password-reset.zip`
- Zip root directory: `group-password-reset/`

## Repo Standards

- Run WordPress coding standards checks with:
  - `phpcs --standard=phpcs.xml.dist .`
- Auto-fix safe PHP formatting changes with:
  - `phpcbf --standard=phpcs.xml.dist group-password-reset.php includes`
- Re-run syntax checks before release:
  - `php -l group-password-reset.php`
  - `php -l includes/admin-menu.php`
  - `php -l includes/admin-views.php`
  - `php -l includes/password-reset.php`

## Manual QA Checklist

- Confirm the plugin activates cleanly with no PHP notices or warnings.
- Confirm the plugin row on `plugins.php` shows the expected metadata and `View details` modal.
- Confirm the modal opens, scrolls to the footer, and the download/homepage links are correct.
- Confirm a role-based reset works with JavaScript enabled.
- Confirm an all-users reset works with one or more excluded usernames.
- Confirm excluded usernames appear in the results summary and are skipped.
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

- Re-run `phpcs --standard=phpcs.xml.dist .` before release.
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
- Confirm the modal metadata still matches the canonical constants in `group-password-reset.php`.
- Run WordPressCS and PHP syntax checks.
- Test on the local WordPress installation at `/Users/JW/Studio/my-wordpress-website`.
- Build an installable archive whose filename is `group-password-reset.zip`.
- Verify the archive extracts to a top-level `group-password-reset/` directory.
- Attach the installable zip to the GitHub release and use that asset in documentation links.

## Local Environment Notes

- Primary local site: `http://my-wordpress-website.wp.local/`
- WordPress admin: `http://my-wordpress-website.wp.local/wp-admin`
- Local plugin install path: `/Users/JW/Studio/my-wordpress-website/wp-content/plugins/group-password-reset`
- Forward-compatibility testing on prerelease WordPress builds is useful, but the documented compatibility baseline should remain grounded in the stable target versions above.
