<?php

/*
 * This file is part of Chevereto.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevereto;

use Chevere\Regex\Interfaces\RegexInterface;
use Chevere\Regex\Regex;
use Chevereto\Legacy\Classes\Settings;

/**
 * Perl Compatible Regular Expressions.
 */
enum PCRE: string
{
    case Username = '/^' . Settings::USERNAME_PATTERN . '$/';

    case UserEmail = '/^.+\@{1}.+$/';

    case UserPassword = '/^' . Settings::USER_PASSWORD_PATTERN . '$/';

    case UrlKey = '/^[\-\w]+$/';

    /**
     * Matches a route name: lowercase alphanumeric words joined by single hyphen or underscore
     */
    case RouteName = '/^[a-z0-9]+(?:[\-_][a-z0-9]+)*$/D';

    /**
     * Matches any non-empty string, not consisting entirely of whitespace
     */
    case NonEmpty = '/\S.*/';

    /**
     * Matches any non-empty string of min to max characters, not consisting entirely of whitespace
     */
    case NonEmptyLimit = '/^(?!\s*$).{%min,%max}$/';

    /**
     * Matches any non-empty string of 1 to max characters, not consisting entirely of whitespace
     */
    case NonEmptyMax = '/^(?!\s*$).{1,%max}$/';

    /**
     * Matches a human-readable size string consisting of a numeric value
     * followed by a unit suffix (e.g. "2 GB", "47.5 KB").
     *
     * Used to validate formatted/display representations of byte sizes
     * (disk capacity, file size, space used, etc.), as opposed to their
     * raw numeric byte values.
     */
    case HumanReadableSize = '/^[\d\.]+\s*[A-Za-z]{2}$/';

    public function regex(string|int|float ...$values): RegexInterface
    {
        $value = $values === []
            ? $this->value
            : $this->tr(...$values);

        return new Regex($value);
    }

    /**
     * Translates the enum value with the given values.
     *
     * @param string|int|float ...$values Pair values to replace in the enum value
     *
     * ^(?!\s*$).{1,%max}+$/
     *
     * Example:
     *
     * ```php
     * PCRE::NON_EMPTY_MAX->tr(45);
     * ```
     */
    public function tr(string|int|float ...$values): string
    {
        $keys = array_keys($values);
        foreach ($keys as &$key) {
            $key = '%' . $key;
        }

        return strtr(
            $this->value,
            array_combine($keys, $values)
        );
    }
}
