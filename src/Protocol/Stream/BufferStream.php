<?php

/**
 * This file is part of Goridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Goridge\Protocol\Stream;

/**
 * Provides a buffer stream that can be written to to fill a buffer, and read
 * from to remove bytes from the buffer.
 *
 * This stream implementation contains a "hwm" value that tells upstream
 * consumers what the configured high water mark of the stream is, or the
 * maximum preferred size of the buffer.
 */
final class BufferStream implements DuplexStreamInterface
{
    /**
     * @var string
     */
    private const ERROR_BUFFER_OVERFLOW = 'A buffer overflow has occurred for which %d bytes were allocated';

    /**
     * @var int
     */
    private const DEFAULT_HIGH_WATER_MARK = 16384;

    /**
     * @var string
     */
    private string $buffer;

    /**
     * @var int
     */
    private int $hwm;

    /**
     * @param string $buffer
     * @param int $hwm
     */
    public function __construct(string $buffer = '', int $hwm = self::DEFAULT_HIGH_WATER_MARK)
    {
        if (\strlen($buffer) > $hwm) {
            throw new \InvalidArgumentException('HWM value can not be less than initializer buffer string size');
        }

        $this->buffer = $buffer;
        $this->hwm = $hwm;
    }

    /**
     * Reads data from the buffer.
     *
     * {@inheritDoc}
     */
    public function read(int $size): string
    {
        $currentSize = $this->getSize();

        //
        // No need to slice the buffer because we don't have enough data.
        //
        if ($size >= $currentSize) {
            try {
                return $this->buffer;
            } finally {
                $this->buffer = '';
            }
        }

        //
        // Slice up the result to provide a subset of the buffer.
        //
        try {
            return \substr($this->buffer, 0, $size);
        } finally {
            $this->buffer = \substr($this->buffer, $size);
        }
    }

    /**
     * Writes data to the buffer.
     *
     * {@inheritDoc}
     */
    public function write(string $string): int
    {
        $this->buffer .= $string;

        if ($this->getSize() >= $this->hwm) {
            throw new \OverflowException(\sprintf(self::ERROR_BUFFER_OVERFLOW, $this->hwm));
        }

        return \strlen($string);
    }

    /**
     * {@inheritDoc}
     */
    public function getSize(): int
    {
        return \strlen($this->buffer);
    }

    /**
     * {@inheritDoc}
     */
    public function getContents(): string
    {
        return $this->buffer;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return $this->buffer;
    }
}
