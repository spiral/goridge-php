<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Transport\Connector;

final class StdOutConnector extends PipeConnector
{
    /**
     * @var string
     */
    public const DEFAULT_URI = 'stdout';

    /**
     * {@inheritDoc}
     */
    public static function create(string $uri = self::DEFAULT_URI, bool $blocking = false)
    {
        return parent::create($uri, $blocking);
    }
}
