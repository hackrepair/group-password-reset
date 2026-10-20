# Group Password Reset project

Current release: **3.6.0**, tag `v3.6.0`, packaged October 20, 2026 and awaiting publication.

Repository: https://github.com/hackrepair/group-password-reset
Release: https://github.com/hackrepair/group-password-reset/releases/tag/v3.6.0

## Current interface and behavior

- THRG mascot header and Site Inspector design family, with orange section bars and full-run summary tiles.
- **Choose accounts**: **Accounts to reset** starts at **Select a scope**. Choose All users, a role, or Administrator (excluding current user); optionally provide excluded usernames.
- **Notifications and confirmation**: affected-user email choice and required acknowledgement. Passwords change before reset-link email attempts. Mail failure does not undo the reset.
- JavaScript processes batches of 20. The non-JavaScript fallback uses redirecting admin-post batches.
- **Done! Password reset complete.** is bold in the in-page progress status. Completion means processing finished; inspect counts for failures and skipped users.
- **Latest results** keeps the most recent 20 per-user rows, removing older rows as new results arrive. Full-run counts remain intact. Per-user rows are not stored for later review.
- **Notify administrators** uses a tinted panel with a two-column desktop list, single-column mobile list, All administrators select/clear control with partial-selection state, muted email addresses, optional note, plain-text summary preview, subject preview, and **Send email**. No recipients are selected automatically.
- The initiating administrator's latest completed summary is available for 15 minutes; a newer completed run replaces it. It contains counts and summary metadata, not target-user lists. The one-use results notice is separate.
- Summary sending requires manage_options, a nonce, an owned current summary, and current administrator recipients with valid addresses (up to 100 shown). Each receives a separate message; repeat sends are throttled for one minute.
- Administrator summaries use full-width branded HTML with personalized greeting, note, count table, notification mode, conditional failure guidance, dashboard button, and privacy footer. A plain-text alternative is included. The dashboard button does not imply stored report history.
- User reset-link emails remain plain text. Subjects use `[domain] [Group Password Reset] Password Reset` and `[domain] [Group Password Reset] Password Reset Summary`; both bodies identify the site URL.
- **Support** container at the bottom of the screen (anchor `#support`): the buy-Jim-a-coffee link and a GitHub Discussions link, same wording as Post Update Email Notifier. Rendered by `gpr_render_support_panel()` in `includes/admin-views.php`.
- Job start/completion audit records retain actor, scope and count data without target-account lists or reset secrets.

## Implementation map

- `includes/password-reset.php`: reset engine, jobs, batching, result payloads and AJAX.
- `includes/admin-menu.php` and `includes/admin-views.php`: admin wiring, localization and UI.
- `includes/admin-notification.php`: summary storage, recipients, previews and email endpoints.
- `includes/email.php` and `includes/email-template.php`: domain/plugin subjects and HTML email.
- `assets/js/admin.js` and `assets/css/admin.css`: progress, row limit, recipient controls and styling.
- `includes/audit-log.php`: durable audit records.
- `includes/class-gpr-updater.php`: GitHub release discovery and local details fallback.

## Release and verification

The 3.5.1 ZIP was discovered using Zendogs' existing updater instance and installed from GitHub through WordPress Plugin_Upgrader. All installed package files matched the published ZIP; the plugin was active as 3.5.1. Main and tag matched the release commit and GitHub checks passed. The local View Details fallback supplied 3.5.1 metadata and banner; the companion section was present in details metadata.

Checks completed: shared PHPCS, packaged PHP and JavaScript syntax, notification and updater regressions, source/package/version integrity, result-row retention, and isolated recipient select-all/clear/partial-state checks. Mailpit captured HTML and plain text from an actual summary send using synthetic counts. HTML email rendering was visually checked on desktop and at 390px without horizontal overflow. Earlier browser batch checks used simulated responses.

Not verified: real account resets, a complete non-JavaScript submission, external inbox delivery, broad email-client compatibility, full screen-reader/200% zoom review, or the full declared WordPress/PHP runtime matrix. No real passwords were reset for these checks. PHPCS and local tests are not production-security clearance.

Current fixture: Zendogs, http://localhost:8081/wp-admin/tools.php?page=group-password-reset
Mailpit: http://localhost:8025/ — local setup instructions are outside this repository at `/Volumes/BACKUP8TB2/plugins/local-mailpit/README.md`.

## Packaging and maintenance

Use `python3 scripts/build-release.py`; updater asset must be `group-password-reset.zip` with one `group-password-reset/` root. `.distignore` excludes development files and the root vendor directory while preserving all bundled updater dependencies. Follow MAINTAINING.md for checks and consider a Codex Security diff review; do not run that plugin automatically.

October 18 documentation audit: current workflow labels, feature lists, email/selector behavior, data retention, and release status were reconciled with 3.5.1 source. These are repository documentation corrections, with no new release or replacement of the published ZIP. The corrected packaged readme will ship with the next release.

## Historical material

- `docs/history/3.5.0-3.5.1-development-notes.md`: intermediate plans and review log, preserved as historical evidence.
- `docs/mockup/`: original 3.5.0 design proposal and screenshot; not a current feature reference or shipped UI.
- `releases/`: historical release notes; 3.5.1 describes the latest published feature set.

## 3.5.2 release

User approved versioning, packaging and publication of both plugins. The 3.5.2 release ships the documentation audit corrections that were deliberately excluded from the existing 3.5.1 asset. No reset-engine or mail-behavior changes. Prior audit-only/no-release notes describe that earlier stage.

## Animated branding

The 2026 THRG heart logo appears in the main plugin header and native View Details banner, matching Site Inspector. Two gentle heartbeat cycles run on opening (4.4 seconds total); reduced-motion preferences and printing disable animation. The details title wraps for smaller screens. Local PNG and scoped CSS require no JavaScript, CDN or animation library. Branding styles load only on this plugin's admin page or its matching information dialog. Source kit: `/Volumes/BACKUP8TB2/plugins/hackrepair-with-heart-animated-2026/plugin-kit`. Included in release 3.5.3.

Branding validation: shared PHPCS passed (12 files); administrator notification and updater notice regressions passed. Zendogs browser checks confirmed the new header asset and two-cycle CSS animation, native View Details at 772px and 390px without horizontal overflow, and disabled animation with reduced motion. No password reset or live email send was performed. Local runtime backup: `/tmp/gpr-before-brand-preview.tar.gz` in `zendogs-wp-sandbox-wordpress-1`. No release was published.

Release 3.5.3: refreshed 2026 THRG heart branding, two heartbeat cycles on opening, reduced-motion/print support, and a wrapping native View Details banner. Assets are bundled locally. Consider the Codex Security review reminder before future changes.

Release 3.5.4: ZIP files are normalized to Unix regular-file mode 0644, with an isolated 0600-source regression test (`python3 tests/package-permissions.py`) and build-time verification. Preserve old release ZIPs. Consider the existing Codex Security review reminder separately.

## Community support

Questions, ideas and community help: https://github.com/hackrepair/group-password-reset/discussions. Reproducible bugs: https://github.com/hackrepair/group-password-reset/issues. Security reports stay private. GitHub Discussions is enabled; Reddit is no longer the support destination. Documentation and support-link updates are committed for future packages without replacing published release assets.
