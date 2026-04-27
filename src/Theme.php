<?php

declare(strict_types=1);

namespace Ena;

use Ena\Providers\Theme\ThemeProvider;
use IX\Theme as BaseTheme;

/**
 * Main theme class.
 *
 * Bootstraps the theme by registering service providers.
 * Extends IX's base Theme class.
 */
class Theme extends BaseTheme
{
    /**
     * @var array<class-string>
     */
    protected array $providers = [
        ThemeProvider::class,
    ];
}
