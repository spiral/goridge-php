<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Transport\Connector;

final class UnixConnector extends SocketConnector
{
    /**
     * {@inheritDoc}
     */
    public static function create(string $uri, bool $blocking = false, array $context = [])
    {
        return parent::create('unix://' . $uri, $blocking, $context);
    }
}
