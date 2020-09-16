<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Protocol;

final class Version
{
    /**
     * <code>
     *  4194304 == 1 << 22 | 0 << 12 | 0
     * </code>
     *
     * @var int
     */
    public const VERSION_1 = 1 << 22 | 0 << 12 | 0;

    /**
     * <code>
     *  8388608 == 2 << 22 | 0 << 12 | 0
     * </code>
     *
     * @var int
     */
    public const VERSION_2 = 2 << 22 | 0 << 12 | 0;

    /**
     * @param int $major
     * @param int $minor
     * @param int $patch
     * @return int
     */
    public static function make(int $major, int $minor = 0, int $patch = 0): int
    {
        return $major << 22 | $minor << 12 | $patch;
    }

    /**
     * @param int $version
     * @return int
     */
    public static function major(int $version): int
    {
        return $version >> 22;
    }

    /**
     * @param int $version
     * @return int
     */
    public static function minor(int $version): int
    {
        return $version >> 12 & 0x3ff;
    }

    /**
     * @param int $version
     * @return int
     */
    public static function patch(int $version): int
    {
        return $version & 0xfff;
    }
}
