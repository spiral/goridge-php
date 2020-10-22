<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Stream;

use Spiral\Goridge\Exception\GoridgeException;

class ResourceStream implements StreamInterface
{
    /**
     * Readable resource modes.
     *
     * @see http://php.net/manual/function.fopen.php
     * @see http://php.net/manual/en/function.gzopen.php
     * @var string[]
     */
    private const READABLE_MODES = ['+', 'r'];

    /**
     * Writable resource modes.
     *
     * @see http://php.net/manual/function.fopen.php
     * @see http://php.net/manual/en/function.gzopen.php
     * @var string[]
     */
    private const WRITABLE_MODES = ['x', 'w', 'c', 'a', '+'];

    /**
     * @var string
     */
    private const ERROR_INVALID_TYPE = 'Invalid stream reference provided';

    /**
     * @var string
     */
    private const ERROR_CREATING = 'An error occurred while creating resource stream';

    /**
     * A list of allowed stream resource types that are allowed to
     * instantiate a ResourceStream.
     *
     * @var string[]
     */
    private const ALLOWED_STREAM_RESOURCE_TYPES = [
        'gd',
        'stream',
    ];

    /**
     * @var resource|null
     */
    private $resource;

    /**
     * @var int
     */
    private int $size;

    /**
     * @param resource $resource
     * @param int $size
     */
    public function __construct($resource, int $size)
    {
        if (! $this->isValidStreamResourceType($resource)) {
            throw new \InvalidArgumentException(self::ERROR_INVALID_TYPE);
        }

        $this->resource = $resource;
        $this->size = $size;
    }

    /**
     * Determine if a resource is one of the resource types allowed to instantiate a Stream
     *
     * @param resource $resource
     * @return bool
     */
    private function isValidStreamResourceType($resource): bool
    {
        if (! \is_resource($resource)) {
            return false;
        }

        return \in_array(\get_resource_type($resource), self::ALLOWED_STREAM_RESOURCE_TYPES, true);
    }

    /**
     * @param string $resource
     * @param string $mode
     * @return resource
     */
    public static function open(string $resource, string $mode = 'r')
    {
        try {
            $result = self::wrap(function () use ($resource, $mode) {
                return \fopen($resource, $mode);
            });
        } catch (\ErrorException $e) {
            throw new GoridgeException(self::ERROR_INVALID_TYPE, $e->getCode(), $e);
        }

        if (! \is_resource($result)) {
            throw new GoridgeException(self::ERROR_CREATING);
        }

        return $result;
    }

    /**
     * @param callable $expression
     * @return mixed
     * @throws \ErrorException
     */
    private static function wrap(callable $expression)
    {
        /** @psalm-var null | array { 0: int, 1: string } $error */
        $error = null;

        \set_error_handler(static function (int $code, int $message) use (&$error): void {
            if ($code !== E_WARNING) {
                return;
            }

            $error = [$code, $message];
        });

        $result = $expression();

        \restore_error_handler();

        if ($error !== null) {
            throw new \ErrorException($error[1], $error[0]);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * {@inheritDoc}
     */
    public function getContents(): string
    {
        if (! $this->isReadable()) {
            throw new \RuntimeException('Cannot read from non-readable stream');
        }

        $result = @\stream_get_contents($this->resource);

        if ($result === false) {
            throw new \RuntimeException('Unable to read from stream');
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function isReadable(): bool
    {
        $mode = $this->getMetadata('mode', '');

        return $this->mode($mode, self::READABLE_MODES);
    }

    /**
     * @param string $section
     * @param mixed $default
     * @return mixed|null
     */
    private function getMetadata(string $section, $default = null)
    {
        if ($this->isClosed()) {
            return $default;
        }

        $meta = \stream_get_meta_data($this->resource);

        return $meta[$section] ?? $default;
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return ! \is_resource($this->resource);
    }

    /**
     * @param string $mode
     * @param string[] $needle
     * @return bool
     */
    private function mode(string $mode, array $needle): bool
    {
        if ($mode === '') {
            return false;
        }

        foreach ($needle as $char) {
            if (\strpos($mode, $char) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $size
     * @return string
     */
    public function read(int $size): string
    {
        if (! $this->isReadable()) {
            throw new \RuntimeException('Cannot read from non-readable stream');
        }

        $string = @\fread($this->resource, $size);

        if ($string === false) {
            throw new \RuntimeException('Unable to read from stream');
        }

        return $string;
    }

    /**
     * @return resource|null
     */
    public function toResource()
    {
        if ($this->isClosed()) {
            return null;
        }

        return $this->resource;
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $string): int
    {
        if (! $this->isWritable()) {
            throw new \RuntimeException('Cannot write to a non-writable stream');
        }

        $result = \fwrite($this->resource, $string);

        if ($result === false) {
            throw new \RuntimeException('Unable to write to stream');
        }

        $this->size += $result;

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function isWritable(): bool
    {
        $mode = $this->getMetadata('mode', '');

        return $this->mode($mode, self::WRITABLE_MODES);
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * @return void
     */
    public function close(): void
    {
        if (! $this->isClosed()) {
            \fclose($this->resource);
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getContents();
    }
}
