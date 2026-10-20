# Contributing

Thanks for helping improve Group Password Reset.

- Ask questions and suggest features in [GitHub Discussions](https://github.com/hackrepair/group-password-reset/discussions).
- Report reproducible bugs through [GitHub issues](https://github.com/hackrepair/group-password-reset/issues).
- Report security problems privately. See [SECURITY.md](SECURITY.md).
- Follow WordPress coding standards: `/Volumes/BACKUP8TB2/plugins/PHPCS/run-phpcs.sh "$PWD"`
- Run `php tests/admin-notification.php` and `php tests/update-notices.php` for notification or updater changes.
- Keep domain/plugin email tags, HTML/plain-text output, recipient behavior, expiry, and the latest-20-row limit documented when changing them.
- Run PHP syntax checks on changed files with `php -l`.
- Keep `README.md`, `readme.txt`, and the plugin header in sync when behavior or requirements change.

Maintainer QA and release steps are in [MAINTAINING.md](MAINTAINING.md).
