# Changelog

All notable changes to the Ena starter child theme are documented here. The format
follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/); versions are derived from
annotated git tags (no `version` field in `composer.json`).

Because Ena is consumed via `composer create-project`, a change only reaches new projects
once it is tagged **and** the satis registry is rebuilt.

## [Unreleased]

## [0.1.3] - 2026-09-25

### Fixed

- **`composer.lock` was gitignored, so every new project deployed non-deterministically.**
  The child theme is the deployed application, not a library — the deploy hook runs
  `composer install` inside the theme directory, and with no committed lock the server
  resolved dependencies fresh on each deploy. Both A View From The Bridge and Matchbook
  Festival had already un-ignored it by hand; the starter now does it so the fix is not
  rediscovered per project.

### Added

- **Documented the required mu-plugin loader.** WordPress only auto-loads mu-plugins that
  sit directly in `wp-content/mu-plugins/`, and Composer installs Mythus into a
  subdirectory — so without `mu-plugins/mu-autoloader.php` in the project root, Mythus
  never boots. The failure is quiet rather than loud: the theme still renders, the front
  page still returns 200, and nothing appears in the error log; providers simply never
  register. The README now carries the loader verbatim plus a one-line verification.
- **Provisioning conventions** (`docs/PROVISIONING.md`) — FastCGI micro-cache, no-Redis
  default.

## [0.1.2] - 2026-04-27

### Changed

- Pinned Mythus and IX to `^1.0` rather than tracking a moving branch.

## [0.1.1] - 2026-04-27

### Fixed

- `bin/rename` derives the PascalCase namespace from the display name when one is given,
  so a single-word slug with a multi-word display name produces `CelebrityAutobiography`
  rather than `Celebrityautobiography`.

## [0.1.0] - 2026-04-27

### Added

- Initial scaffold — `src/Theme.php`, `src/Providers/Theme/ThemeProvider.php`, the build
  pipeline delegated to IX's `scripts/build-providers.js`, and the self-deleting
  `bin/rename`.
