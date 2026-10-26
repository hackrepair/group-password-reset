# Group Password Reset project

Current release: **3.6.2**, tag `v3.6.2`, published and verified October 25, 2026.

Repository: https://github.com/hackrepair/group-password-reset
Release: https://github.com/hackrepair/group-password-reset/releases/tag/v3.6.1

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


## View Details banner correction — October 20, 2026

The packaged banner existed, but was added only by the local metadata fallback. Successful GitHub details responses could omit it. The own-plugin companion filter now attaches the installed banner to successful details responses too, while preserving sections and other plugins' metadata. This is a pending source correction; no version bump or release is authorized by this report.

The details stylesheet also replaced the artwork with its gradient/mascot. It now layers the original banner above the gradient and hides duplicate visual title/logo while retaining an accessible heading. Standalone banner regression and shared PHPCS pass.

Zendogs received the source and stylesheet corrections with recoverable copies in `/tmp/gpr-companion-before-banner-fix.php` and `/tmp/gpr-details-before-banner-fix.css`. Native details metadata now supplies the banner. Host HTTP checks return 200 and image bytes match source. The details stylesheet uses its modification time in the version query to avoid reusing the old gradient-only CSS. No passwords, settings or emails were changed; no package or release was created.


## GitHub banner presentation — October 20, 2026

The README now shows the original Group Password Reset banner above the title, replacing the animated heart in that location. It references the existing packaged artwork directly, avoiding another image copy. The matching View Details corrections and regression are committed with this presentation update for the next release; the published 3.6.0 package and tag remain unchanged.


## 3.6.1 release verification — October 20, 2026

Published latest release: https://github.com/hackrepair/group-password-reset/releases/tag/v3.6.1. Release/tag commit `4615981a25a1311f6917b045246345423f97297a`. Asset `group-password-reset.zip`, 509009 bytes, 141 files. SHA-256 `be635b4342b74f6b3e3c07c1ba510d8e6fdbcd8a05fb458496953cf0cae37555` matches the local package, GitHub asset digest and freshly downloaded ZIP. Previous local packages were retained in dist/archive; older releases/tags remain unchanged.

Shared PHPCS passes all 12 configured files; notification, update-message, own-plugin banner/isolation and restrictive-source package-permission regressions pass. All 54 packaged PHP files lint clean; admin JavaScript syntax and ZIP/source integrity pass. GitHub PHP 7.4/8.3 checks passed runs 39514044227 and 39514044635, including the new banner regression. CI: https://github.com/hackrepair/group-password-reset/actions/runs/39514044635.

Zendogs' registered updater anonymously discovered the exact published 3.6.1 ZIP and WordPress bulk upgrade installed it from GitHub. Activation and saved excluded usernames/audit schema options were preserved. All 141 installed files match the downloaded published ZIP. Prior installation backed up at `/tmp/gpr-before-published-3.6.1.tgz` in the container.

Ego Lite native View Details shows version 3.6.1, the original artwork in front of the gradient and the expected Description, Changelog, Installation, FAQ and Other THRG Plugins tabs. Screenshot `/tmp/gpr-361-view-details.png` visually inspected. Browser task closed after verification. No real passwords were reset and no emails were sent for this presentation-only update; broader reset/email runtime limits documented above remain applicable. This documentation follow-up does not replace the published asset or move its tag.


## Companion-plugin guide — October 25, 2026 (pending release)

The native View Details **Other THRG Plugins** tab lists the other five tools: Disable Other Plugin Notices, Group Password Reset, Plugin Detective, Site Inspector, Post Update Email Notifier and Plugin Archiver, excluding the current plugin. Plugin Archiver always links to https://wordpress.org/plugins/hackrepair-plugin-archiver/; the GitHub-distributed tools link to their own repositories. Cards are informational links, with no automatic installs or external requests. Archiver retains WordPress.org updates and metadata. No published version, tag or release asset is changed by this source update.

Verification: shared PHPCS and `php tests/companion-plugins.php` pass for this checkout. Native WordPress View Details on Zendogs renders five distinct companion cards, with no self-link and Archiver linked only to WordPress.org. Archiver uses `plugins_api_result` for official metadata and preserves ratings/version/update controls; the test used a temporary loader beside the older installed Archiver, not a full upgrade. Temporary preview files are restored after testing; no reset, archive, delete or email action was performed. Published releases remain unchanged.


## 3.6.2 release preparation — October 25, 2026

User approved packaging and publication for the five GitHub-updated plugins. Plugin Archiver is explicitly excluded from this release pass. Version headers/constants, stable tag, README and release notes are aligned at 3.6.2. Release checks and publication results follow below.


## 3.6.2 published and verified — October 25, 2026

Published: https://github.com/hackrepair/group-password-reset/releases/tag/v3.6.2. Version metadata, ZIP and published digest agree. Real Zendogs updater discovery/install, retained settings/activation, installed file hashes and native View Details verified. All local checks passed; CI scope and hashes are recorded in `docs/release-verification-3.6.2.md`. Older packages/releases retained. Plugin Archiver was excluded from this release pass.
