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

- Node 22+ and PHP 8.4+
- The project root has Mythus installed as a mu-plugin and IX installed as the parent theme (typically via `composer require vincentragosta/mythus vincentragosta/ix`)
- **The project root has a mu-plugin loader** at `wp-content/mu-plugins/mu-autoloader.php` (see below)

### The mu-plugin loader

WordPress only auto-loads mu-plugins that are *files directly inside* `wp-content/mu-plugins/`.
Composer installs Mythus into a **subdirectory**, so WordPress never loads it on its own.

Without the loader the failure is quiet rather than loud: the theme still renders and the front
page still returns 200, but Mythus never boots, so no provider ever registers. Nothing in the
error log points at the cause.

Create `wp-content/mu-plugins/mu-autoloader.php` in the project root:

```php
<?php
/**
 * Plugin Name: MU Plugin Autoloader
 * Description: Loads the root Composer autoloader and all mu-plugin subdirectories.
 */

declare(strict_types=1);

// Load the root Composer autoloader — provides namespaces for all
// Composer-managed mu-plugins and their dependencies.
$rootAutoload = dirname(__DIR__, 2) . '/vendor/autoload.php';

if (file_exists($rootAutoload)) {
    require_once $rootAutoload;
}

// Subdirectory mu-plugins to load, in order.
// Each entry is the path to the plugin's main file relative to mu-plugins/.
$plugins = [
    'mythus/mythus.php',
];

foreach ($plugins as $plugin) {
    $path = __DIR__ . '/' . $plugin;

    if (!file_exists($path)) {
        wp_die(
            sprintf(
                'Required mu-plugin <code>%s</code> is not installed. Run <code>composer install</code> from the project root.',
                $plugin
            ),
            'Missing MU Plugin',
            ['response' => 500, 'back_link' => false]
        );
    }

    require_once $path;
}
```

Verify it worked before building anything on top of it:

```bash
wp eval 'echo defined("MYTHUS_VERSION") ? "ok" : "mythus not loaded";'
```

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
