<?php

declare(strict_types=1);

namespace Joranski\Flux;

use Closure;

/**
 * Default link resolution for `<x-joranski-flux::clip>` when no `href` is passed.
 *
 * The package knows nothing about the host's models or routes; the host app
 * registers a resolver (record => URL or null) from a service provider.
 */
final class Clip
{
    /**
     * @var (Closure(object): ?string)|null
     */
    private static ?Closure $hrefResolver = null;

    /**
     * @param  (Closure(object): ?string)|null  $resolver
     */
    public static function resolveHrefUsing(?Closure $resolver): void
    {
        self::$hrefResolver = $resolver;
    }

    public static function defaultHref(object $record): ?string
    {
        if (self::$hrefResolver === null) {
            return null;
        }

        return (self::$hrefResolver)($record);
    }
}
