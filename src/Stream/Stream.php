<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Stream;

use Spiral\Goridge\Exception\TransportException;

final class Stream
{
    /**
     * @var string
     */
    private const ERROR_INVALID_RESOURCE_TYPE = 'Resource stream must be a valid resource type, but %s given';

    /**
     * @var string
     */
    private const ERROR_NOT_READABLE = 'Resource stream must be readable';

    /**
     * @var string
     */
    private const ERROR_NOT_WRITABLE = 'Resource stream must be readable';

    /**
     * @var string
     */
    private const ERROR_NOT_SEEKABLE = 'Resource stream must be seekable';

    /**
     * @param string $message
     * @return resource
     */
    public static function fromString(string $message)
    {
        $stream = \fopen('php://memory', 'ab+');

        \fwrite($stream, $message);
        \rewind($stream);

        return $stream;
    }

    /**
     * Checks that the resource is a valid stream resource.
     *
     * @param resource $stream
     */
    public static function assertIsValid($stream): void
    {
        if (! self::isValid($stream)) {
            $message = \sprintf(self::ERROR_INVALID_RESOURCE_TYPE, \get_debug_type($stream));

            throw new TransportException($message, 0x01);
        }
    }

    /**
     * @param resource $stream
     * @return bool
     */
    public static function isValid($stream): bool
    {
        if (! \is_resource($stream)) {
            return false;
        }

        $type = @\get_resource_type($stream);

        return \is_string($type) && $type === 'stream';
    }

    /**
     * Checks that the resource is readable and throws an exception otherwise.
     *
     * @param resource $stream
     */
    public static function assertIsReadable($stream): void
    {
        if (! self::isReadable($stream)) {
            throw new TransportException(self::ERROR_NOT_READABLE, 0x02);
        }
    }

    /**
     * Checks if stream is readable.
     *
     * @param resource $stream
     * @return bool
     */
    public static function isReadable($stream): bool
    {
        $meta = \stream_get_meta_data($stream);
        $mode = $meta['mode'];

        return (
            \str_contains($mode, 'r') ||
            \str_contains($mode, '+')
        );
    }

    /**
     * Checks that the resource is writable and throws an exception otherwise.
     *
     * @param resource $stream
     */
    public static function assertIsWritable($stream): void
    {
        if (! self::isWritable($stream)) {
            throw new TransportException(self::ERROR_NOT_WRITABLE, 0x03);
        }
    }

    /**
     * Checks if stream is writable.
     *
     * @param resource $stream
     * @return bool
     */
    public static function isWritable($stream): bool
    {
        $meta = \stream_get_meta_data($stream);
        $mode = $meta['mode'];

        return (
            \str_contains($mode, 'x') ||
            \str_contains($mode, 'w') ||
            \str_contains($mode, 'c') ||
            \str_contains($mode, 'a') ||
            \str_contains($mode, '+')
        );
    }

    /**
     * Checks that the resource is seekable and throws an exception otherwise.
     *
     * @param resource $stream
     */
    public static function assertIsSeekable($stream): void
    {
        if (! self::isSeekable($stream)) {
            throw new TransportException(self::ERROR_NOT_SEEKABLE, 0x04);
        }
    }

    /**
     * Checks if stream is seekable.
     *
     * @param resource $stream
     * @return bool
     */
    public static function isSeekable($stream): bool
    {
        $meta = \stream_get_meta_data($stream);

        return $meta['seekable'];
    }
}
