# Ena

Starter child theme for WordPress projects built on the [Mythus](https://github.com/vinnyrags/mythus) + [IX](https://github.com/vinnyrags/IX) framework.

## Quickstart

From the WordPress project root:

```bash
composer create-project vincentragosta/ena wp-content/themes/<project-slug> --no-install
cd wp-content/themes/<project-slug>
./bin/rename <project-slug> "Project Display Name"
composer install
npm install && npm run build
wp theme activate <project-slug>
```

`bin/rename` rewrites the placeholder namespace (`Ena\\`), package name (`vincentragosta/ena`), and theme metadata (`Theme Name: Ena`, `Text Domain: ena`) to match the project, then deletes itself. The rename is one-shot and idempotent only in the sense that re-running it would do nothing — but the script self-removes after success.

## What you get

- `src/Theme.php` — extends `IX\Theme`, ready to register your providers
- `src/Providers/Theme/ThemeProvider.php` — extends `IX\Providers\Theme\ThemeProvider`; add features/hooks here
- Build pipeline wired to IX's `scripts/build-providers.js` via npm scripts
- SCSS load paths configured to resolve IX's breakpoint mixins

## Prerequisites

- The project root has Mythus installed as a mu-plugin and IX installed as the parent theme (typically via `composer require vincentragosta/mythus vincentragosta/ix`)
- Node 22+ and PHP 8.4+

## Layout

```
.
├── bin/rename                          # placeholder swap, self-deleting
├── functions.php                        # autoload chain + Theme::bootstrap()
├── package.json                         # build scripts delegated to ../ix
├── scripts/build-providers.config.js    # SCSS load paths
├── src/
│   ├── Theme.php
│   └── Providers/Theme/
│       ├── ThemeProvider.php
│       └── assets/{scss,js}/
├── style.css                            # Theme header (Template: ix)
└── views/                               # Twig overrides (optional)
```
