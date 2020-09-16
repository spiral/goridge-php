<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Transport\Connector;

use Spiral\Goridge\Exception\TransportException;

abstract class PipeConnector implements ConnectorInterface
{
    /**
     * {@inheritDoc}
     */
    public static function create(string $uri, bool $blocking = false)
    {
        \error_clear_last();

        $stream = @\fopen('php://' . $uri, 'rb');

        \stream_set_blocking($stream, $blocking);

        if ($error = \error_get_last()) {
            throw new TransportException($error['message']);
        }

        return $stream;
    }
}
