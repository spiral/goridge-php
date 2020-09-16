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

abstract class SocketConnector extends Connector
{
    /**
     * @var int
     */
    private const DEFAULT_STREAM_FLAGS = \STREAM_CLIENT_CONNECT | \STREAM_CLIENT_ASYNC_CONNECT;

    /**
     * {@inheritDoc}
     */
    public static function create(string $uri, bool $blocking = false, array $context = [])
    {
        $stream = self::createConnection($uri, self::createContext($context));

        \stream_set_blocking($stream, $blocking);

        return $stream;
    }

    /**
     * @param string $uri
     * @param resource $context
     * @return resource
     */
    private static function createConnection(string $uri, $context)
    {
        $flags = self::DEFAULT_STREAM_FLAGS;

        $socket = @\stream_socket_client($uri, $code, $error, 0, $flags, $context);

        if ($error !== '') {
            throw new TransportException($error);
        }

        return $socket;
    }

    /**
     * @param array $context
     * @return resource
     */
    private static function createContext(array $context = [])
    {
        return \stream_context_create([
            'socket' => $context,
        ]);
    }
}
